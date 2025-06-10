<?php
/**
 * File untuk memproses pembatalan pesanan
 * Hanya pesanan dengan status 'pending' yang bisa dibatalkan
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pesanan.php');
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];
$id_pesanan = isset($_POST['id_pesanan']) ? (int) $_POST['id_pesanan'] : 0;

// Validasi input
if ($id_pesanan <= 0) {
    $_SESSION['error_message'] = 'ID pesanan tidak valid';
    header('Location: pesanan.php');
    exit();
}

// Cek apakah pesanan milik user yang login dan statusnya masih pending
$check_query = "SELECT id_pesanan, status_pesanan, tanggal_layanan, waktu_layanan 
                FROM pesanan_layanan 
                WHERE id_pesanan = '$id_pesanan' 
                AND id_pelanggan = '$id_pelanggan' 
                AND status_pesanan = 'pending'";

$check_result = mysqli_query($conn, $check_query);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error_message'] = 'Pesanan tidak dapat dibatalkan. Hanya pesanan dengan status "Menunggu Konfirmasi" yang dapat dibatalkan.';
    header('Location: pesanan.php');
    exit();
}

$pesanan_data = mysqli_fetch_assoc($check_result);

// Mulai transaksi database
mysqli_begin_transaction($conn);

try {
    // Update status pesanan menjadi cancelled
    $update_query = "UPDATE pesanan_layanan 
                     SET status_pesanan = 'cancelled', 
                         updated_at = NOW() 
                     WHERE id_pesanan = '$id_pesanan' 
                     AND id_pelanggan = '$id_pelanggan'";

    if (mysqli_query($conn, $update_query)) {
        // Trigger akan otomatis mengurangi jumlah booking di jadwal_grooming
        // Commit transaksi
        mysqli_commit($conn);

        $_SESSION['success_message'] = "Pesanan #" . str_pad($id_pesanan, 6, '0', STR_PAD_LEFT) . " berhasil dibatalkan.";
    } else {
        throw new Exception("Gagal membatalkan pesanan: " . mysqli_error($conn));
    }

} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($conn);
    $_SESSION['error_message'] = $e->getMessage();
}

// Redirect kembali ke halaman pesanan
header('Location: pesanan.php');
exit();
?>