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
        // Get penitipan data to verify ownership
        $query = "SELECT p.*, pt.id_penitipan 
                  FROM pesanan p 
                  JOIN penitipan pt ON p.id_pesanan = pt.id_pesanan 
                  WHERE p.id_pesanan = ? AND p.id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_pesanan, $_SESSION['id_pelanggan']]);
        $penitipan = $stmt->fetch();

        if (!$penitipan) {
            echo json_encode(['success' => false, 'message' => 'Penitipan tidak ditemukan atau tidak memiliki akses']);
            exit();
        }

        // Check if penitipan can be cancelled
        if ($penitipan['status_pesanan'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Penitipan tidak dapat dibatalkan']);
            exit();
        }

        // Update pesanan status
        $update_pesanan = "UPDATE pesanan SET status_pesanan = 'cancelled' WHERE id_pesanan = ?";
        $stmt = $pdo->prepare($update_pesanan);
        $stmt->execute([$id_pesanan]);

        echo json_encode(['success' => true, 'message' => 'Penitipan berhasil dibatalkan']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>