<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user data if logged in
    $user_id = $_SESSION['id_pelanggan'];
    $query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    // Get form data
    $nama_pelanggan = $user_data['nama_lengkap'];
    $nomor_telepon = $user_data['nomor_telepon'];
    $nama_hewan = $_POST['pet_name'];
    $kategori_hewan = $_POST['pet_category'];
    $ciri_khusus = $_POST['pet_special'];
    $jenis_perawatan = $_POST['service_type'];
    $tanggal_perawatan = $_POST['service_date'];
    $waktu_perawatan = $_POST['service_time'];
    $nama_groomer = $_POST['groomer'];
    $metode_pembayaran = $_POST['payment_method'];

    // Calculate total price based on service type and groomer
    $harga_perawatan = [
        'basic' => 150000,
        'mix' => 200000,
        'complete' => 250000
    ];

    $harga_groomer = [
        'andi' => 50000,
        'budi' => 45000,
        'cindy' => 55000,
        'dina' => 50000
    ];

    $total_harga = $harga_perawatan[$jenis_perawatan] + $harga_groomer[$nama_groomer];

    // Insert into database
    $query = "INSERT INTO perawatan (nama_pelanggan, nomor_telepon, nama_hewan, kategori_hewan, ciri_khusus, 
              jenis_perawatan, tanggal_perawatan, waktu_perawatan, nama_groomer, total_harga, metode_pembayaran) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssssssds",
        $nama_pelanggan,
        $nomor_telepon,
        $nama_hewan,
        $kategori_hewan,
        $ciri_khusus,
        $jenis_perawatan,
        $tanggal_perawatan,
        $waktu_perawatan,
        $nama_groomer,
        $total_harga,
        $metode_pembayaran
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Booking berhasil! Silahkan lakukan pembayaran.";
        header("Location: perawatan.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan. Silahkan coba lagi.";
        header("Location: perawatan.php");
    }
    exit();
}
?>