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

        // Start transaction
        $pdo->beginTransaction();

        // First, get the anabul data to verify ownership
        $query = "SELECT * FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$anabul_id, $pelanggan_id]);
        $anabul = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$anabul) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses']);
            exit();
        }

        // Get all photos associated with this anabul
        $foto_query = "SELECT nama_file FROM anabul_foto WHERE id_anabul = ?";
        $foto_stmt = $pdo->prepare($foto_query);
        $foto_stmt->execute([$anabul_id]);
        $fotos = $foto_stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete photos from anabul_foto table
        $delete_foto_query = "DELETE FROM anabul_foto WHERE id_anabul = ?";
        $delete_foto_stmt = $pdo->prepare($delete_foto_query);
        $delete_foto_result = $delete_foto_stmt->execute([$anabul_id]);

        if (!$delete_foto_result) {
            throw new Exception('Gagal menghapus data foto dari database');
        }

        // Delete the main record from anabul table
        $delete_query = "DELETE FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_result = $delete_stmt->execute([$anabul_id, $pelanggan_id]);

        if (!$delete_result) {
            throw new Exception('Gagal menghapus data anabul dari database');
        }

        // If database operations successful, delete the photo files
        $upload_dir = '../../uploads/anabul/';
        foreach ($fotos as $foto) {
            $photo_path = $upload_dir . $foto;
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }

        // Delete main photo if exists
        if (!empty($anabul['foto_utama'])) {
            $main_photo_path = $upload_dir . $anabul['foto_utama'];
            if (file_exists($main_photo_path)) {
                unlink($main_photo_path);
            }
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Data anabul berhasil dihapus']);

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data anabul: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>