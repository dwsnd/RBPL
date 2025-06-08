<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Database connection
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anabul'])) {
    try {
        $anabul_id = (int) $_POST['id_anabul'];
        $pelanggan_id = $_SESSION['id_pelanggan'];

        // First, get the anabul data to verify ownership and get photo filename
        $query = "SELECT * FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$anabul_id, $pelanggan_id]);
        $anabul = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$anabul) {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses']);
            exit();
        }

        // Delete the record from database
        $delete_query = "DELETE FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_result = $delete_stmt->execute([$anabul_id, $pelanggan_id]);

        if ($delete_result) {
            // Delete the photo file if exists
            if (!empty($anabul['foto'])) {
                $photo_path = '../uploads/anabul/' . $anabul['foto'];
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Data anabul berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data dari database']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>