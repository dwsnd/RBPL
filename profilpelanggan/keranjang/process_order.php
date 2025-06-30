<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['items']) || empty($data['items'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Data pesanan tidak valid'
    ]);
    exit;
}

require_once '../../includes/db.php';

try {
    $pdo->beginTransaction();

    $pelanggan_id = $_SESSION['id_pelanggan'];
    $total_amount = $data['total'];
    $payment_method = $data['payment_method'];
    $order_date = date('Y-m-d H:i:s');

    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

    // Create order using the correct table structure
    $order_query = "INSERT INTO pesanan (nomor_pesanan, id_pelanggan, jenis_pesanan, total_harga, metode_pembayaran, status_pembayaran, status_pesanan, tanggal_pesanan) 
                    VALUES (?, ?, 'produk', ?, ?, 'pending', 'pending', ?)";
    $stmt = $pdo->prepare($order_query);
    $stmt->execute([$order_number, $pelanggan_id, $total_amount, $payment_method, $order_date]);
    $order_id = $pdo->lastInsertId();

    // Process each item
    foreach ($data['items'] as $item) {
        // Check stock availability
        $stock_query = "SELECT stok FROM produk WHERE id_produk = ?";
        $stmt = $pdo->prepare($stock_query);
        $stmt->execute([$item['id_produk']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product['stok'] < $item['quantity']) {
            throw new Exception("Stok produk tidak mencukupi");
        }

        // Update stock
        $update_stock = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
        $stmt = $pdo->prepare($update_stock);
        $stmt->execute([$item['quantity'], $item['id_produk']]);

        // Create order detail using the correct table structure
        $subtotal = $item['harga'] * $item['quantity'];
        $detail_query = "INSERT INTO pesanan_produk (id_pesanan, id_produk, quantity, harga_satuan, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($detail_query);
        $stmt->execute([$order_id, $item['id_produk'], $item['quantity'], $item['harga'], $subtotal]);

        // Remove from cart if not direct checkout
        if (!$item['is_direct_checkout'] && isset($item['id_keranjang'])) {
            $remove_cart = "DELETE FROM keranjang WHERE id_keranjang = ?";
            $stmt = $pdo->prepare($remove_cart);
            $stmt->execute([$item['id_keranjang']]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pesanan berhasil dibuat',
        'order_id' => $order_id,
        'order_number' => $order_number
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
    ]);
}
?>