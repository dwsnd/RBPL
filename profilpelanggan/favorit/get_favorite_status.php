<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set header untuk JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'is_favorited' => false, 'message' => 'User not logged in']);
    exit();
}

// Database connection
require_once '../../includes/db.php';

// Get product ID from query parameter
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'is_favorited' => false, 'message' => 'Invalid product ID']);
    exit();
}

$pelanggan_id = $_SESSION['id_pelanggan'];

try {
    // Check if product is in favorites
    $query = "SELECT id FROM favorit WHERE id_pelanggan = ? AND id_produk = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pelanggan_id, $product_id]);
    $is_favorited = $stmt->fetch() !== false;

    echo json_encode([
        'success' => true,
        'is_favorited' => $is_favorited,
        'product_id' => $product_id
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_favorite_status.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'is_favorited' => false,
        'message' => 'Database error'
    ]);
}
?>