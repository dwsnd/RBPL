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
$cart_id = isset($data['cart_id']) ? (int) $data['cart_id'] : 0;
$quantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;

if (!$cart_id || $quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak valid'
    ]);
    exit;
}

require_once '../../includes/db.php';

try {
    // Check if cart item exists and belongs to user
    $stmt = $pdo->prepare("SELECT k.*, p.stok FROM keranjang k 
                          JOIN produk p ON k.id_produk = p.id_produk 
                          WHERE k.id_keranjang = ? AND k.id_pelanggan = ?");
    $stmt->execute([$cart_id, $_SESSION['id_pelanggan']]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart_item) {
        echo json_encode([
            'success' => false,
            'message' => 'Item keranjang tidak ditemukan'
        ]);
        exit;
    }

    // Check if quantity exceeds stock
    if ($quantity > $cart_item['stok']) {
        echo json_encode([
            'success' => false,
            'message' => 'Jumlah melebihi stok yang tersedia'
        ]);
        exit;
    }

    // Update quantity
    $stmt = $pdo->prepare("UPDATE keranjang SET quantity = ? WHERE id_keranjang = ?");
    $stmt->execute([$quantity, $cart_id]);

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM keranjang WHERE id_pelanggan = ?");
    $stmt->execute([$_SESSION['id_pelanggan']]);
    $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => 'Jumlah berhasil diperbarui',
        'cart_count' => $cart_count
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memperbarui keranjang'
    ]);
}
?>