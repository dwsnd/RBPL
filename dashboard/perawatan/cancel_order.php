<?php
/**
 * File untuk memproses pembatalan pesanan
 * Hanya pesanan dengan status 'pending' yang bisa dibatalkan
 */

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
        $query = "SELECT p.* FROM pesanan p 
                  WHERE p.id_pesanan = ? AND p.id_pelanggan = ? AND p.jenis_pesanan = 'perawatan'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_pesanan, $_SESSION['id_pelanggan']]);
        $pesanan = $stmt->fetch();

        if (!$pesanan) {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan atau tidak memiliki akses']);
            exit();
        }

        // Check if pesanan can be cancelled
        if ($pesanan['status_pesanan'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak dapat dibatalkan']);
            exit();
        }

        // Update pesanan status
        $update_pesanan = "UPDATE pesanan SET status_pesanan = 'cancelled' WHERE id_pesanan = ?";
        $stmt = $pdo->prepare($update_pesanan);
        $stmt->execute([$id_pesanan]);

        echo json_encode(['success' => true, 'message' => 'Pesanan perawatan berhasil dibatalkan']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>