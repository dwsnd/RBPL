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

require_once 'db.php';

// Get product ID from POST data
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

if (!$product_id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk tidak valid'
    ]);
    exit;
}

$pelanggan_id = $_SESSION['id_pelanggan'];

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id_produk FROM produk WHERE id_produk = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ]);
        exit;
    }

    // Check if already in favorites
    $stmt = $pdo->prepare("SELECT id_favorit FROM favorit WHERE id_pelanggan = ? AND id_produk = ?");
    $stmt->execute([$pelanggan_id, $product_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Remove from favorites
        $stmt = $pdo->prepare("DELETE FROM favorit WHERE id_favorit = ?");
        $stmt->execute([$favorite['id_favorit']]);
        $message = 'Produk dihapus dari favorit';
        $is_favorite = false;
    } else {
        // Add to favorites
        $stmt = $pdo->prepare("INSERT INTO favorit (id_pelanggan, id_produk) VALUES (?, ?)");
        $stmt->execute([$pelanggan_id, $product_id]);
        $message = 'Produk ditambahkan ke favorit';
        $is_favorite = true;
    }

    // Get updated favorites count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorit WHERE id_pelanggan = ?");
    $stmt->execute([$pelanggan_id]);
    $favorites_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    echo json_encode([
        'success' => true,
        'message' => $message,
        'is_favorite' => $is_favorite,
        'favorites_count' => $favorites_count
    ]);

} catch (PDOException $e) {
    error_log("Database error in toggle_wishlist.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memproses favorit'
    ]);
}
?>