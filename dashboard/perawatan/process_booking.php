<?php
/**
 * File untuk memproses booking grooming
 * Terpisah dari form untuk pemisahan logic yang lebih baik
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Function untuk validasi slot waktu
function checkSlotAvailability($conn, $tanggal_layanan, $waktu_layanan)
{
    $query = "SELECT jumlah_booking, kapasitas_maksimal, status_slot 
              FROM jadwal_grooming 
              WHERE tanggal = '$tanggal_layanan' AND waktu_slot = '$waktu_layanan'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'available' => $row['status_slot'] === 'tersedia' && $row['jumlah_booking'] < $row['kapasitas_maksimal'],
            'current_booking' => $row['jumlah_booking'],
            'max_capacity' => $row['kapasitas_maksimal']
        ];
    } else {
        // Jika slot belum ada, anggap tersedia (akan dibuat otomatis)
        return [
            'available' => true,
            'current_booking' => 0,
            'max_capacity' => 5
        ];
    }
}

// Function untuk validasi tanggal
function validateBookingDate($tanggal_layanan)
{
    $today = date('Y-m-d');
    $booking_date = date('Y-m-d', strtotime($tanggal_layanan));

    // Tidak boleh booking untuk hari yang sudah lewat
    if ($booking_date < $today) {
        return [
            'valid' => false,
            'message' => 'Tanggal layanan tidak boleh kurang dari hari ini'
        ];
    }

    // Tidak boleh booking lebih dari 30 hari ke depan
    $max_date = date('Y-m-d', strtotime('+30 days'));
    if ($booking_date > $max_date) {
        return [
            'valid' => false,
            'message' => 'Tanggal layanan maksimal 30 hari dari sekarang'
        ];
    }

    // Cek apakah hari libur (contoh: Minggu)
    $day_of_week = date('w', strtotime($booking_date));
    if ($day_of_week == 0) { // 0 = Minggu
        return [
            'valid' => false,
            'message' => 'Layanan grooming tidak tersedia pada hari Minggu'
        ];
    }

    return ['valid' => true];
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan = $_SESSION['id_pelanggan'];

    // Sanitize input
    $pet_name = mysqli_real_escape_string($conn, trim($_POST['pet_name']));
    $pet_category = mysqli_real_escape_string($conn, $_POST['pet_category']);
    $pet_special = mysqli_real_escape_string($conn, trim($_POST['pet_special']));
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $service_date = mysqli_real_escape_string($conn, $_POST['service_date']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;
    $catatan_khusus = isset($_POST['catatan_khusus']) ? mysqli_real_escape_string($conn, trim($_POST['catatan_khusus'])) : '';

    // Validasi input
    $errors = [];

    // Validasi service type
    $valid_services = ['basic', 'mix', 'complete'];
    if (!in_array($service_type, $valid_services)) {
        $errors[] = 'Jenis layanan tidak valid';
    }

    // Validasi waktu layanan
    $valid_times = ['pagi', 'siang', 'sore', 'sore-akhir'];
    if (!in_array($service_time, $valid_times)) {
        $errors[] = 'Waktu layanan tidak valid';
    }

    // Validasi tanggal
    $date_validation = validateBookingDate($service_date);
    if (!$date_validation['valid']) {
        $errors[] = $date_validation['message'];
    }

    // Validasi slot ketersediaan
    if (empty($errors)) {
        $slot_check = checkSlotAvailability($conn, $service_date, $service_time);
        if (!$slot_check['available']) {
            $errors[] = 'Slot waktu yang dipilih sudah penuh. Silakan pilih waktu lain.';
        }
    }

    // Validasi data hewan
    if ($id_anabul_existing === 'new' || $id_anabul_existing === null) {
        if (empty($pet_name)) {
            $errors[] = 'Nama hewan peliharaan harus diisi';
        }
        if (empty($pet_category)) {
            $errors[] = 'Kategori hewan harus dipilih';
        }
        $valid_categories = ['kucing', 'anjing', 'kelinci', 'hamster'];
        if (!in_array($pet_category, $valid_categories)) {
            $errors[] = 'Kategori hewan tidak valid';
        }
    } else {
        // Validasi ID anabul yang dipilih milik user yang login
        $check_pet = "SELECT id_anabul FROM anabul WHERE id_anabul = '$id_anabul_existing' AND id_pelanggan = '$id_pelanggan'";
        $check_result = mysqli_query($conn, $check_pet);
        if (!$check_result || mysqli_num_rows($check_result) == 0) {
            $errors[] = 'Data hewan peliharaan tidak valid';
        }
    }

    // Jika ada error, redirect kembali dengan pesan error
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode('<br>', $errors);
        header('Location: booking_form.php');
        exit();
    }

    // Hitung total harga
    $harga_layanan = [
        'basic' => 150000,
        'mix' => 200000,
        'complete' => 250000
    ];
    $total_harga = $harga_layanan[$service_type];

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        $id_anabul = null;

        // Jika pilih hewan yang sudah ada
        if ($id_anabul_existing && $id_anabul_existing !== 'new') {
            $id_anabul = $id_anabul_existing;
        } else {
            // Insert data hewan baru
            $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, kategori_hewan, karakteristik, created_at) 
                          VALUES ('$id_pelanggan', '$pet_name', '$pet_category', '$pet_special', NOW())";

            if (mysqli_query($conn, $insert_pet)) {
                $id_anabul = mysqli_insert_id($conn);
            } else {
                throw new Exception("Gagal menyimpan data hewan: " . mysqli_error($conn));
            }
        }

        // Insert pesanan layanan
        $insert_order = "INSERT INTO pesanan_layanan 
                        (id_pelanggan, id_anabul, jenis_layanan, tanggal_layanan, waktu_layanan, 
                         total_harga, status_pesanan, catatan_khusus, created_at) 
                        VALUES 
                        ('$id_pelanggan', '$id_anabul', '$service_type', '$service_date', '$service_time', 
                         '$total_harga', 'pending', '$catatan_khusus', NOW())";

        if (mysqli_query($conn, $insert_order)) {
            $id_pesanan = mysqli_insert_id($conn);

            // Commit transaksi
            mysqli_commit($conn);

            // Set pesan sukses
            $_SESSION['success_message'] = "
                <strong>Pesanan berhasil dibuat!</strong><br>
                <small>ID Pesanan: #" . str_pad($id_pesanan, 6, '0', STR_PAD_LEFT) . "</small><br>
                <small>Tanggal: " . date('d/m/Y', strtotime($service_date)) . "</small><br>
                <small>Waktu: " . ucfirst($service_time) . "</small><br>
                <small>Total: Rp " . number_format($total_harga, 0, ',', '.') . "</small>
            ";

            // Redirect ke halaman pesanan
            header('Location: pesanan.php');
            exit();

        } else {
            throw new Exception("Gagal menyimpan pesanan: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: booking_form.php');
        exit();
    }

} else {
    // Jika bukan POST request, redirect ke form
    header('Location: booking_form.php');
    exit();
}
?>