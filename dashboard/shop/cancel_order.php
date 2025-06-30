<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'User tidak terautentikasi']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'] ?? null;

    if (!$id_pesanan) {
        echo json_encode(['success' => false, 'message' => 'ID pesanan tidak valid']);
        exit();
    }

    try {
        // Get pesanan data to verify ownership
        $query = "SELECT * FROM pesanan WHERE id_pesanan = ? AND id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_pesanan, $_SESSION['id_pelanggan']]);
        $pesanan = $stmt->fetch();

        if (!$pesanan) {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan atau tidak memiliki akses']);
            exit();
        }

        // Check if pesanan can be cancelled
        if ($pesanan['status_pesanan'] !== 'pending' && $pesanan['status_pesanan'] !== 'confirmed') {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak dapat dibatalkan']);
            exit();
        }

        // Update pesanan status
        $update_query = "UPDATE pesanan SET status_pesanan = 'cancelled' WHERE id_pesanan = ?";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([$id_pesanan]);

        echo json_encode(['success' => true, 'message' => 'Pesanan produk berhasil dibatalkan']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>