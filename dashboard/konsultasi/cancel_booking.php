<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'User tidak terautentikasi']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_konsultasi = $_POST['id_konsultasi'] ?? null;

    if (!$id_konsultasi) {
        echo json_encode(['success' => false, 'message' => 'ID konsultasi tidak valid']);
        exit();
    }

    try {
        // Get konsultasi data to verify ownership
        $query = "SELECT k.*, p.id_pelanggan 
                  FROM konsultasi k 
                  JOIN pesanan p ON k.id_pesanan = p.id_pesanan 
                  WHERE k.id_konsultasi = ? AND p.id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_konsultasi, $_SESSION['id_pelanggan']]);
        $konsultasi = $stmt->fetch();

        if (!$konsultasi) {
            echo json_encode(['success' => false, 'message' => 'Konsultasi tidak ditemukan atau tidak memiliki akses']);
            exit();
        }

        // Check if konsultasi can be cancelled
        if ($konsultasi['status_konsultasi'] !== 'pending') {
            echo json_encode(['success' => false, 'message' => 'Konsultasi tidak dapat dibatalkan']);
            exit();
        }

        // Update konsultasi status
        $update_query = "UPDATE konsultasi SET status_konsultasi = 'cancelled' WHERE id_konsultasi = ?";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([$id_konsultasi]);

        // Update pesanan status
        $update_pesanan = "UPDATE pesanan SET status_pesanan = 'cancelled' WHERE id_pesanan = ?";
        $stmt = $pdo->prepare($update_pesanan);
        $stmt->execute([$konsultasi['id_pesanan']]);

        echo json_encode(['success' => true, 'message' => 'Konsultasi berhasil dibatalkan']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>