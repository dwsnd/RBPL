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

if (!$cart_id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID keranjang tidak valid'
    ]);
    exit;
}

require_once '../../includes/db.php';

try {
    // Check if cart item exists and belongs to user
    $stmt = $pdo->prepare("SELECT id_keranjang FROM keranjang WHERE id_keranjang = ? AND id_pelanggan = ?");
    $stmt->execute([$cart_id, $_SESSION['id_pelanggan']]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart_item) {
        echo json_encode([
            'success' => false,
            'message' => 'Item keranjang tidak ditemukan'
        ]);
        exit;
    }

    // Remove item from cart
    $stmt = $pdo->prepare("DELETE FROM keranjang WHERE id_keranjang = ?");
    $stmt->execute([$cart_id]);

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM keranjang WHERE id_pelanggan = ?");
    $stmt->execute([$_SESSION['id_pelanggan']]);
    $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    echo json_encode([
        'success' => true,
        'message' => 'Item berhasil dihapus dari keranjang',
        'cart_count' => $cart_count
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus item'
    ]);
}
?>