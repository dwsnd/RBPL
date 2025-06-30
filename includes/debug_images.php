<?php
// File: debug_images.php
// Letakkan file ini di folder yang sama dengan get_products.php (includes/)

header('Content-Type: application/json');
require_once 'db.php';

try {
    // Ambil sample produk dari database
    $query = "SELECT id_produk, nama_produk, foto_utama, target_hewan FROM produk LIMIT 5";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $debug_info = [];

    foreach ($products as $product) {
        $imagePath = $product['foto_utama'];
        $debug_product = [
            'id' => $product['id_produk'],
            'name' => $product['nama_produk'],
            'original_image_path' => $imagePath,
            'target_hewan' => $product['target_hewan'],
            'checks' => []
        ];

        // Test berbagai kemungkinan path
        $test_paths = [
            $imagePath, // Path asli dari database
            '../uploads/produk/' . strtolower($product['target_hewan']) . '/' . basename($imagePath),
            'uploads/produk/' . strtolower($product['target_hewan']) . '/' . basename($imagePath),
            '../uploads/produk/' . basename($imagePath),
            'uploads/produk/' . basename($imagePath),
            '../aset/' . basename($imagePath),
            'aset/' . basename($imagePath)
        ];

        foreach ($test_paths as $test_path) {
            // Path untuk pengecekan dari includes/ folder
            $check_path = __DIR__ . '/' . str_replace('../', '', $test_path);

            $debug_product['checks'][] = [
                'test_path' => $test_path,
                'full_server_path' => $check_path,
                'exists' => file_exists($check_path),
                'is_readable' => file_exists($check_path) ? is_readable($check_path) : false,
                'file_size' => file_exists($check_path) ? filesize($check_path) : 0
            ];
        }

        $debug_info[] = $debug_product;
    }

    // Informasi tambahan tentang struktur direktori
    $directory_info = [
        'current_dir' => __DIR__,
        'parent_dir' => dirname(__DIR__),
        'uploads_dir_exists' => is_dir(dirname(__DIR__) . '/uploads'),
        'uploads_produk_dir_exists' => is_dir(dirname(__DIR__) . '/uploads/produk'),
        'aset_dir_exists' => is_dir(dirname(__DIR__) . '/aset'),
        'directory_contents' => []
    ];

    // Cek isi direktori uploads/produk jika ada
    if (is_dir(dirname(__DIR__) . '/uploads/produk')) {
        $produk_dirs = scandir(dirname(__DIR__) . '/uploads/produk');
        foreach ($produk_dirs as $dir) {
            if ($dir != '.' && $dir != '..' && is_dir(dirname(__DIR__) . '/uploads/produk/' . $dir)) {
                $files = scandir(dirname(__DIR__) . '/uploads/produk/' . $dir);
                $directory_info['directory_contents'][$dir] = array_filter($files, function ($file) {
                    return $file != '.' && $file != '..';
                });
            }
        }
    }

    echo json_encode([
        'success' => true,
        'debug_info' => $debug_info,
        'directory_info' => $directory_info,
        'server_info' => [
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'script_filename' => $_SERVER['SCRIPT_FILENAME'],
            'current_working_directory' => getcwd()
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>