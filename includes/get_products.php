<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include file koneksi database
require_once 'db.php';

try {
    // Query to get all products from database
    $query = "SELECT 
                id_produk as id, 
                name, 
                price, 
                image, 
                category, 
                stock, 
                description,
                created_at 
              FROM produk 
              WHERE stock > 0 
              ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $response = [
        'success' => true,
        'products' => $products,
        'total' => count($products),
        'message' => 'Products loaded successfully'
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Handle database errors
    $response = [
        'success' => false,
        'products' => [],
        'total' => 0,
        'message' => 'Database error: ' . $e->getMessage()
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Handle other errors
    $response = [
        'success' => false,
        'products' => [],
        'total' => 0,
        'message' => 'Error: ' . $e->getMessage()
    ];

    echo json_encode($response);
}
?>