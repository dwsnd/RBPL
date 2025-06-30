<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

require_once '../../includes/db.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['product_id']) ? (int) $data['product_id'] : 0;
$quantity = isset($data['quantity']) ? (int) $data['quantity'] : 1;

if (!$product_id || $quantity < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Data produk tidak valid'
    ]);
    exit;
}

$pelanggan_id = $_SESSION['id_pelanggan'];

try {
    // Check if product exists and has enough stock
    $stmt = $pdo->prepare("SELECT id_produk, nama_produk, harga, stok FROM produk WHERE id_produk = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ]);
        exit;
    }

    if ($product['stok'] < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => 'Stok tidak mencukupi'
        ]);
        exit;
    }

    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT id_keranjang, quantity FROM keranjang WHERE id_pelanggan = ? AND id_produk = ?");
    $stmt->execute([$pelanggan_id, $product_id]);
    $cart_item = $stmt->fetch();

    if ($cart_item) {
        // Update existing cart item
        $new_quantity = $cart_item['quantity'] + $quantity;
        if ($new_quantity > $product['stok']) {
            echo json_encode([
                'success' => false,
                'message' => 'Jumlah melebihi stok yang tersedia'
            ]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE keranjang SET quantity = ?, updated_at = NOW() WHERE id_keranjang = ?");
        $stmt->execute([$new_quantity, $cart_item['id_keranjang']]);
        $message = 'Jumlah produk di keranjang berhasil diperbarui';
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("INSERT INTO keranjang (id_pelanggan, id_produk, quantity, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$pelanggan_id, $product_id, $quantity]);
        $message = 'Produk berhasil ditambahkan ke keranjang';
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM keranjang WHERE id_pelanggan = ?");
    $stmt->execute([$pelanggan_id]);
    $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => $message,
        'cart_count' => $cart_count
    ]);

} catch (PDOException $e) {
    error_log("Database error in add_to_cart.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menambahkan ke keranjang'
    ]);
}
?>