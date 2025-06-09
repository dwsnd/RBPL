<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set header untuk JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit();
}

// Database connection
require_once '../../includes/db.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !is_numeric($input['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID tidak valid']);
    exit();
}

$pelanggan_id = $_SESSION['id_pelanggan'];
$product_id = (int) $input['product_id'];

try {
    // Check if product exists
    $check_product = "SELECT id_produk FROM produk WHERE id_produk = ?";
    $stmt = $pdo->prepare($check_product);
    $stmt->execute([$product_id]);

    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
        exit();
    }

    // Check if already in favorites
    $check_favorit = "SELECT id_favorit FROM favorit WHERE id_pelanggan = ? AND id_produk = ?";
    $stmt = $pdo->prepare($check_favorit);
    $stmt->execute([$pelanggan_id, $product_id]);
    $existing_favorit = $stmt->fetch();

    if ($existing_favorit) {
        // Remove from favorites
        $delete_query = "DELETE FROM favorit WHERE id_pelanggan = ? AND id_produk = ?";
        $stmt = $pdo->prepare($delete_query);
        $result = $stmt->execute([$pelanggan_id, $product_id]);

        if ($result) {
            echo json_encode([
                'success' => true,
                'action' => 'removed',
                'message' => 'Produk dihapus dari favorit'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus dari favorit']);
        }
    } else {
        // Add to favorites
        $insert_query = "INSERT INTO favorit (id_pelanggan, id_produk) VALUES (?, ?)";
        $stmt = $pdo->prepare($insert_query);
        $result = $stmt->execute([$pelanggan_id, $product_id]);

        if ($result) {
            echo json_encode([
                'success' => true,
                'action' => 'added',
                'message' => 'Produk ditambahkan ke favorit'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke favorit']);
        }
    }

} catch (PDOException $e) {
    error_log("Database error in toggle_favorit.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
} catch (Exception $e) {
    error_log("General error in toggle_favorit.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
?>