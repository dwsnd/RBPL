<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once '../../includes/db.php';

if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $id_pesanan = isset($_POST['id_pesanan']) ? $_POST['id_pesanan'] : null;
    if (!$id_pesanan) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
    // Update status_pembayaran di pesanan
    $stmt2 = $pdo->prepare('UPDATE pesanan SET status_pembayaran = ? WHERE id_pesanan = ?');
    $success2 = $stmt2->execute(['paid', $id_pesanan]);
    if ($success2) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update status pembayaran']);
    }
    exit();
}
echo json_encode(['success' => false, 'message' => 'Invalid method']);