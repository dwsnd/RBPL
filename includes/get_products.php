<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

try {
    // Check if user is logged in
    $pelanggan_id = isset($_SESSION['id_pelanggan']) ? $_SESSION['id_pelanggan'] : null;

    if ($pelanggan_id) {
        // Query with favorite status for logged-in users
        $query = "SELECT 
                    p.id_produk as id, 
                    p.nama_produk as name, 
                    p.harga as price, 
                    p.foto_utama as image, 
                    p.kategori as category, 
                    p.target_hewan,
                    p.stok, 
                    p.deskripsi as description,
                    p.created_at,
                    CASE WHEN f.id_favorit IS NOT NULL THEN 1 ELSE 0 END as is_favorited
                  FROM produk p 
                  LEFT JOIN favorit f ON p.id_produk = f.id_produk AND f.id_pelanggan = ?
                  WHERE p.stok > 0 
                  ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$pelanggan_id]);
    } else {
        // Query without favorite status for non-logged-in users
        $query = "SELECT 
                    id_produk as id, 
                    nama_produk as name, 
                    harga as price, 
                    foto_utama as image, 
                    kategori as category, 
                    target_hewan,
                    stok, 
                    deskripsi as description,
                    created_at,
                    0 as is_favorited
                  FROM produk 
                  WHERE stok > 0 
                  ORDER BY created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedProducts = [];
    foreach ($products as $product) {
        $imagePath = $product['image'];

        if ($imagePath) {
            // Clean up the path
            $imagePath = trim($imagePath);
            $imagePath = str_replace('\\', '/', $imagePath);

            // If the path doesn't start with uploads/, add it
            if (!str_starts_with($imagePath, 'uploads/')) {
                $imagePath = 'uploads/produk/' . $imagePath;
            }

            // Add ../ to make it relative to public folder
            $imagePath = '../' . $imagePath;
        } else {
            $imagePath = '../aset/default-product.png';
        }

        $formattedProducts[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $imagePath,
            'category' => $product['category'],
            'target_hewan' => $product['target_hewan'],
            'stock' => $product['stok'],
            'description' => $product['description'],
            'created_at' => $product['created_at'],
            'is_favorited' => (bool) $product['is_favorited']
        ];
    }

    echo json_encode([
        'success' => true,
        'products' => $formattedProducts,
        'total' => count($formattedProducts),
        'message' => 'Products loaded successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'products' => [],
        'total' => 0,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>