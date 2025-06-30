<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Add validation functions
function checkAvailability($conn, $tanggal_masuk, $tanggal_keluar, $service_type)
{
    // Get capacity from configuration
    $capacity_query = "SELECT nilai FROM konfigurasi WHERE nama_setting = ?";
    $capacity_stmt = mysqli_prepare($conn, $capacity_query);

    $kategori_query = "SELECT kategori_layanan FROM layanan WHERE id_layanan = ?";
    $kategori_stmt = mysqli_prepare($conn, $kategori_query);
    mysqli_stmt_bind_param($kategori_stmt, "i", $service_type);
    mysqli_stmt_execute($kategori_stmt);
    $kategori_result = mysqli_stmt_get_result($kategori_stmt);
    $kategori_row = mysqli_fetch_assoc($kategori_result);
    $kategori = $kategori_row['kategori_layanan'];

    $setting_name = 'max_penitipan_' . $kategori;

    mysqli_stmt_bind_param($capacity_stmt, "s", $setting_name);
    mysqli_stmt_execute($capacity_stmt);
    $capacity_result = mysqli_stmt_get_result($capacity_stmt);
    $capacity_row = mysqli_fetch_assoc($capacity_result);

    $max_capacity = $capacity_row ? intval($capacity_row['nilai']) : 10; // default 10

    // Check current bookings
    $query = "SELECT COUNT(*) as booked_count 
              FROM penitipan p
              JOIN pesanan ps ON p.id_pesanan = ps.id_pesanan
              JOIN pesanan_layanan pl ON ps.id_pesanan = pl.id_pesanan
              JOIN layanan l ON pl.id_layanan = l.id_layanan
              WHERE l.id_layanan = ? 
              AND ps.status_pesanan IN ('pending', 'confirmed', 'processing') 
              AND p.status_checkin != 'checked_out'
              AND (
                  (p.tanggal_checkin <= ? AND p.tanggal_checkout >= ?) OR
                  (p.tanggal_checkin >= ? AND p.tanggal_checkin <= ?)
              )";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issss", $service_type, $tanggal_keluar, $tanggal_masuk, $tanggal_masuk, $tanggal_keluar);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return ($row['booked_count'] < $max_capacity);
}

function checkPetConflict($conn, $id_anabul, $tanggal_masuk, $tanggal_keluar, $exclude_order_id = null)
{
    $query = "SELECT p.id_pesanan, p.tanggal_checkin, p.tanggal_checkout, ps.status_pesanan
              FROM penitipan p
              JOIN pesanan ps ON p.id_pesanan = ps.id_pesanan
              WHERE p.id_anabul = ? 
              AND ps.status_pesanan IN ('pending', 'confirmed', 'processing')
              AND p.status_checkin != 'checked_out'
              AND (
                  (p.tanggal_checkin <= ? AND p.tanggal_checkout >= ?) OR
                  (p.tanggal_checkin >= ? AND p.tanggal_checkin <= ?)
              )";

    if ($exclude_order_id) {
        $query .= " AND p.id_pesanan != ?";
    }

    $stmt = mysqli_prepare($conn, $query);
    if ($exclude_order_id) {
        mysqli_stmt_bind_param($stmt, "issssi", $id_anabul, $tanggal_keluar, $tanggal_masuk, $tanggal_masuk, $tanggal_keluar, $exclude_order_id);
    } else {
        mysqli_stmt_bind_param($stmt, "issss", $id_anabul, $tanggal_keluar, $tanggal_masuk, $tanggal_masuk, $tanggal_keluar);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $conflict = mysqli_fetch_assoc($result);
        $masuk = new DateTime($conflict['tanggal_checkin']);
        $keluar = new DateTime($conflict['tanggal_checkout']);
        return "Hewan peliharaan Anda sudah memiliki booking pada tanggal " .
            $masuk->format('d/m/Y') . " sampai " . $keluar->format('d/m/Y') .
            " dengan status " . $conflict['status_pesanan'];
    }

    return false;
}

function validateBookingDates($conn, $tanggal_masuk, $tanggal_keluar)
{
    $errors = [];

    // Get configuration settings
    $config_query = "SELECT nama_setting, nilai FROM konfigurasi 
                     WHERE nama_setting IN ('max_advance_booking_days', 'min_advance_booking_hours', 'max_boarding_duration_days')";
    $config_result = mysqli_query($conn, $config_query);
    $config = [];

    if ($config_result) {
        while ($row = mysqli_fetch_assoc($config_result)) {
            $config[$row['nama_setting']] = intval($row['nilai']);
        }
    }

    // Default values if not in database
    $max_advance_days = $config['max_advance_booking_days'] ?? 30;
    $min_advance_hours = $config['min_advance_booking_hours'] ?? 24;
    $max_duration_days = $config['max_boarding_duration_days'] ?? 30;

    $now = new DateTime();
    $masuk = new DateTime($tanggal_masuk);
    $keluar = new DateTime($tanggal_keluar);

    // Check minimum advance booking
    $min_booking_time = clone $now;
    $min_booking_time->add(new DateInterval('PT' . $min_advance_hours . 'H'));

    if ($masuk < $min_booking_time) {
        $errors[] = "Booking harus dilakukan minimal {$min_advance_hours} jam sebelum tanggal masuk";
    }

    // Check maximum advance booking
    $max_booking_time = clone $now;
    $max_booking_time->add(new DateInterval('P' . $max_advance_days . 'D'));

    if ($masuk > $max_booking_time) {
        $errors[] = "Booking hanya bisa dilakukan maksimal {$max_advance_days} hari ke depan";
    }

    // Check duration
    $duration = $masuk->diff($keluar)->days;
    if ($duration > $max_duration_days) {
        $errors[] = "Durasi penitipan maksimal {$max_duration_days} hari";
    }

    // Check if checkout is after checkin
    if ($keluar <= $masuk) {
        $errors[] = "Tanggal keluar harus setelah tanggal masuk";
    }

    return $errors;
}

function updateCapacityTracking($conn, $tanggal_masuk, $tanggal_keluar, $service_type, $action = 'add')
{
    $start_date = new DateTime($tanggal_masuk);
    $end_date = new DateTime($tanggal_keluar);

    // Get kategori_layanan from layanan table
    $kategori_query = "SELECT kategori_layanan FROM layanan WHERE id_layanan = ?";
    $kategori_stmt = mysqli_prepare($conn, $kategori_query);
    mysqli_stmt_bind_param($kategori_stmt, "i", $service_type);
    mysqli_stmt_execute($kategori_stmt);
    $kategori_result = mysqli_stmt_get_result($kategori_stmt);
    $kategori_row = mysqli_fetch_assoc($kategori_result);
    $kategori = $kategori_row['kategori_layanan'];

    while ($start_date < $end_date) {
        $current_date = $start_date->format('Y-m-d');

        // Get current count for the date and service type
        $count_query = "SELECT COUNT(*) as current_count 
                       FROM penitipan p
                       JOIN pesanan ps ON p.id_pesanan = ps.id_pesanan
                       JOIN pesanan_layanan pl ON ps.id_pesanan = pl.id_pesanan
                       JOIN layanan l ON pl.id_layanan = l.id_layanan
                       WHERE l.id_layanan = ?
                       AND p.tanggal_checkin <= ? 
                       AND p.tanggal_checkout >= ?
                       AND p.status_checkin != 'checked_out'
                       AND ps.status_pesanan IN ('pending', 'confirmed', 'processing')";

        $count_stmt = mysqli_prepare($conn, $count_query);
        mysqli_stmt_bind_param($count_stmt, "iss", $service_type, $current_date, $current_date);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $count_row = mysqli_fetch_assoc($count_result);

        $current_count = $count_row['current_count'];

        // Get max capacity from config
        $capacity_query = "SELECT nilai FROM konfigurasi WHERE nama_setting = ?";
        $capacity_stmt = mysqli_prepare($conn, $capacity_query);
        $setting_name = 'max_penitipan_' . $kategori;
        mysqli_stmt_bind_param($capacity_stmt, "s", $setting_name);
        mysqli_stmt_execute($capacity_stmt);
        $capacity_result = mysqli_stmt_get_result($capacity_stmt);
        $capacity_row = mysqli_fetch_assoc($capacity_result);

        $max_capacity = $capacity_row ? intval($capacity_row['nilai']) : 10;

        // Check if record exists for this date and service type
        $check_query = "SELECT id_kapasitas FROM kapasitas_penitipan 
                       WHERE tanggal = ? AND kategori_layanan = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ss", $current_date, $kategori);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            // Update existing record
            $update_query = "UPDATE kapasitas_penitipan 
                           SET kapasitas_terpakai = ?, 
                               kapasitas_maksimal = ?,
                               updated_at = NOW()
                           WHERE tanggal = ? AND kategori_layanan = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "iiss", $current_count, $max_capacity, $current_date, $kategori);
            mysqli_stmt_execute($update_stmt);
        } else {
            // Insert new record
            $insert_query = "INSERT INTO kapasitas_penitipan 
                           (tanggal, kategori_layanan, kapasitas_maksimal, kapasitas_terpakai, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, NOW(), NOW())";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ssii", $current_date, $kategori, $max_capacity, $current_count);
            mysqli_stmt_execute($insert_stmt);
        }

        $start_date->add(new DateInterval('P1D'));
    }
}

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch user data
$user_data = [];
$user_query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = ?";
$user_stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $id_pelanggan);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
}


// Fetch user's pets data
$pets_data = [];
$pets_query = "SELECT id_anabul, nama_hewan, spesies, ciri_khusus FROM anabul WHERE id_pelanggan = ? AND status = 'aktif'";
$pets_stmt = mysqli_prepare($conn, $pets_query);
mysqli_stmt_bind_param($pets_stmt, "i", $id_pelanggan);
mysqli_stmt_execute($pets_stmt);
$pets_result = mysqli_stmt_get_result($pets_stmt);
if ($pets_result && mysqli_num_rows($pets_result) > 0) {
    while ($row = mysqli_fetch_assoc($pets_result)) {
        $pets_data[] = $row;
    }
}

// Fetch services data
$services_data = [];
$services_query = "SELECT id_layanan, nama_layanan, jenis_layanan, harga FROM layanan WHERE jenis_layanan = 'penitipan' ORDER BY id_layanan";
$services_result = mysqli_query($conn, $services_query);
if ($services_result && mysqli_num_rows($services_result) > 0) {
    while ($row = mysqli_fetch_assoc($services_result)) {
        $services_data[] = $row;
        // Debug output
        error_log("Service: " . $row['id_layanan'] . " - " . $row['nama_layanan'] . " - " . $row['harga']);
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $kontak_darurat = mysqli_real_escape_string($conn, $_POST['kontak_darurat']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $tanggal_masuk = mysqli_real_escape_string($conn, $_POST['tanggal_masuk']);
    $tanggal_keluar = mysqli_real_escape_string($conn, $_POST['tanggal_keluar']);
    $pola_makan = mysqli_real_escape_string($conn, $_POST['pola_makan']);
    $obat_obatan = mysqli_real_escape_string($conn, $_POST['obat_obatan']);
    $kebiasaan_penting = mysqli_real_escape_string($conn, $_POST['kebiasaan_penting']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;

    // Initialize pet data variables
    $nama_hewan = '';
    $spesies = '';
    $ciri_khusus = '';

    // If new pet is being added, get the form data
    if ($id_anabul_existing === 'new') {
        $nama_hewan = isset($_POST['nama_hewan']) ? mysqli_real_escape_string($conn, $_POST['nama_hewan']) : '';
        $spesies = isset($_POST['spesies']) ? mysqli_real_escape_string($conn, $_POST['spesies']) : '';
        $ciri_khusus = isset($_POST['ciri_khusus']) ? mysqli_real_escape_string($conn, $_POST['ciri_khusus']) : '';
    }

    // Validate dates
    $date_errors = validateBookingDates($conn, $tanggal_masuk, $tanggal_keluar);
    if (!empty($date_errors)) {
        $error_message = implode("<br>", $date_errors);
    } else {
        // Start transaction BEFORE availability check
        mysqli_begin_transaction($conn);

        try {
            // Check availability within transaction
            if (!checkAvailability($conn, $tanggal_masuk, $tanggal_keluar, $service_type)) {
                throw new Exception("Maaf, tidak ada ketersediaan untuk tanggal dan paket layanan yang dipilih. Silakan pilih tanggal atau paket lain.");
            }

            // Calculate duration and total price
            $date_masuk = new DateTime($tanggal_masuk);
            $date_keluar = new DateTime($tanggal_keluar);
            $interval = $date_masuk->diff($date_keluar);
            $jumlah_hari = $interval->days;

            // Get current pricing from database
            $pricing_query = "SELECT harga FROM layanan WHERE id_layanan = ?";
            $pricing_stmt = mysqli_prepare($conn, $pricing_query);
            mysqli_stmt_bind_param($pricing_stmt, "s", $service_type);
            mysqli_stmt_execute($pricing_stmt);
            $pricing_result = mysqli_stmt_get_result($pricing_stmt);
            $pricing_row = mysqli_fetch_assoc($pricing_result);

            if (!$pricing_row) {
                throw new Exception("Layanan tidak ditemukan");
            }

            $harga = $pricing_row['harga'];
            $total_harga = $harga * $jumlah_hari;

            $id_anabul = null;

            // If existing pet is selected
            if ($id_anabul_existing && $id_anabul_existing !== 'new') {
                $id_anabul = $id_anabul_existing;

                // Check for pet booking conflicts
                $conflict_message = checkPetConflict($conn, $id_anabul, $tanggal_masuk, $tanggal_keluar);
                if ($conflict_message) {
                    throw new Exception($conflict_message);
                }
            } else {
                // Validate required fields for new pet
                if (empty($nama_hewan) || empty($spesies)) {
                    throw new Exception("Nama hewan dan spesies hewan harus diisi untuk hewan baru.");
                }

                // Insert new pet data
                $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, spesies, ciri_khusus, created_at) 
                              VALUES (?, ?, ?, ?, NOW())";
                $pet_stmt = mysqli_prepare($conn, $insert_pet);
                mysqli_stmt_bind_param($pet_stmt, "isss", $id_pelanggan, $nama_hewan, $spesies, $ciri_khusus);

                if (mysqli_stmt_execute($pet_stmt)) {
                    $id_anabul = mysqli_insert_id($conn);
                } else {
                    throw new Exception("Error inserting pet data: " . mysqli_error($conn));
                }
            }

            // Insert ke tabel pesanan
            $insert_pesanan = "INSERT INTO pesanan 
                            (nomor_pesanan, id_pelanggan, jenis_pesanan, total_harga, 
                             metode_pembayaran, status_pembayaran, status_pesanan, 
                             catatan_pelanggan, catatan_admin, created_at, updated_at) 
                            VALUES 
                            (CONCAT('PES-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 9999), 4, '0')), 
                             ?, 'penitipan', ?, 'cash', 'pending', 'pending', 
                             'Booking layanan penitipan', '', NOW(), NOW())";

            $pesanan_stmt = mysqli_prepare($conn, $insert_pesanan);
            mysqli_stmt_bind_param($pesanan_stmt, "id", $id_pelanggan, $total_harga);

            if (mysqli_stmt_execute($pesanan_stmt)) {
                $id_pesanan = mysqli_insert_id($conn);

                // Insert ke tabel pesanan_layanan
                $insert_layanan = "INSERT INTO pesanan_layanan 
                                (id_pesanan, id_layanan, id_anabul, harga_layanan) 
                                VALUES 
                                (?, ?, ?, ?)";

                $layanan_stmt = mysqli_prepare($conn, $insert_layanan);
                mysqli_stmt_bind_param($layanan_stmt, "iiid", $id_pesanan, $service_type, $id_anabul, $total_harga);

                if (mysqli_stmt_execute($layanan_stmt)) {
                    $id_pesanan_layanan = mysqli_insert_id($conn);

                    // Insert ke tabel penitipan
                    $insert_penitipan = "INSERT INTO penitipan 
                                    (id_pesanan, id_anabul, tanggal_checkin, tanggal_checkout, 
                                     jumlah_hari, makanan_khusus, obat_khusus, instruksi_khusus,
                                     kontak_darurat, status_checkin, created_at, updated_at) 
                                    VALUES 
                                    (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";

                    // Prepare kontak darurat as JSON
                    $kontak_darurat_json = json_encode([
                        'nama' => explode(' - ', $kontak_darurat)[0] ?? '',
                        'telepon' => explode(' - ', $kontak_darurat)[1] ?? '',
                        'hubungan' => 'Kontak Darurat'
                    ]);

                    $penitipan_stmt = mysqli_prepare($conn, $insert_penitipan);
                    mysqli_stmt_bind_param(
                        $penitipan_stmt,
                        "iissiisss",
                        $id_pesanan,
                        $id_anabul,
                        $tanggal_masuk,
                        $tanggal_keluar,
                        $jumlah_hari,
                        $pola_makan,
                        $obat_obatan,
                        $kebiasaan_penting,
                        $kontak_darurat_json
                    );

                    if (mysqli_stmt_execute($penitipan_stmt)) {
                        $id_penitipan = mysqli_insert_id($conn);

                        // Update capacity tracking
                        updateCapacityTracking($conn, $tanggal_masuk, $tanggal_keluar, $service_type, 'add');

                        // Commit transaction
                        mysqli_commit($conn);

                        // Set success message
                        $_SESSION['success_message'] = "Pesanan layanan penitipan berhasil dibuat! ID Pesanan: " . $id_pesanan;

                        // Redirect using header
                        header('Location: ../../profilpelanggan/pesanan/index_pesanan.php');
                        exit();
                    } else {
                        throw new Exception("Error inserting penitipan data: " . mysqli_error($conn));
                    }
                } else {
                    throw new Exception("Error inserting layanan data: " . mysqli_error($conn));
                }
            } else {
                throw new Exception("Error inserting pesanan data: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error_message = $e->getMessage();
        }
    }
}

// Add JavaScript alert and redirect if success
if (isset($redirect_success) && $redirect_success) {
    echo "<script>
        alert('Pesanan berhasil dibuat!');
        window.location.href = '../../profilpelanggan/penitipan/penitipan_pelanggan.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Hero Section */
        .hero-section {
            background: #fff;
            padding: 65px 0;
        }

        .hero-section .container {
            min-height: 460px;
            position: relative;
            z-index: 2;
        }

        .shape-main {
            position: absolute;
            right: 40px;
            top: 13%;
            width: 50%;
            z-index: 1;
        }

        .image-catdog {
            position: absolute;
            right: 40px;
            top: 14%;
            width: 50%;
            z-index: 2;
            transition: transform 0.3s ease;
        }

        .image-catdog:hover {
            transform: scale(1.02);
        }

        .shape-leftup {
            position: absolute;
            left: 15%;
            top: -12%;
            width: 13%;
            z-index: 1;
        }

        .shape-leftdown {
            position: absolute;
            left: 25%;
            bottom: 5%;
            width: 11%;
            z-index: 1;
        }

        .btn-black {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-black:hover {
            background: #333;
            color: #fff;
        }

        footer {
            padding: 40px 0;
        }

        .form-control:focus,
        select:focus,
        input:focus,
        textarea:focus {
            border-color: #fd7e14 !important;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25) !important;
        }

        select.form-control {
            color: #888;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23000' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 2.5rem !important;
        }

        select.form-control option {
            color: #888 !important;
        }

        select.form-control option:checked {
            color: #000 !important;
        }

        select.form-control option:first-child {
            color: #888 !important;
        }

        .pet-option {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin-bottom: 6px;
            padding: 8px 12px;
        }

        .pet-option .form-check {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .pet-option .form-check-input {
            margin: 0;
            margin-right: 8px;
            align-self: center;
        }

        .pet-option .form-check-label {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            flex: 1;
        }

        .pet-option .d-flex {
            margin: 0;
            padding: 0;
        }

        .pet-option .fas {
            font-size: 0.9rem;
        }

        .pet-option .text-muted {
            font-size: 0.85rem;
        }

        .pet-option .small {
            font-size: 0.8rem;
        }

        .pet-option:hover {
            background-color: #fff3cd;
            border-color: #ffc107;
        }

        .pet-option.selected {
            background-color: #fff3cd;
            border-color: #ffc107;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-section {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .section-title {
            color: #fd7e14;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #fff3cd;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 6px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
        }

        .form-control:read-only {
            background-color: #f8f9fa;
        }

        .btn-reset {
            padding: 6px 16px;
            border: 2px solid #fd7e14;
            color: #fd7e14;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-reset:hover {
            background-color: #fff3cd;
        }

        .btn-submit {
            padding: 6px 16px;
            background-color: #fd7e14;
            color: white;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-submit:hover {
            background-color: #e96e0a;
        }

        .row {
            margin-bottom: 8px;
        }

        .col-md-6 {
            padding: 0 8px;
        }

        .duration-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 0.9rem;
        }

        .price-breakdown {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 12px;
            margin-top: 8px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden">
        <!-- SHAPE BESAR KANAN -->
        <img src="../../aset/Shape2.png" class="shape-main" alt="Shape">
        <!-- SHAPE KECIL KIRI ATAS -->
        <img src="../../aset/Shape.png" class="shape-leftup" alt="Shape2">
        <!-- SHAPE KECIL KIRI BAWAH -->
        <img src="../../aset/Shape1.png" class="shape-leftdown" alt="Shape1">
        <div class="container d-flex flex-wrap align-items-center justify-content-between position-relative"
            style="z-index:2;">
            <div class="col-lg-6 mb-4 text-lg-start text-center">
                <h6 class="text-orange-500 text-base font-semibold mb-2">Ling-Ling Pet Shop</h6>
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Titipin? Gas! Kita Rawat
                    <br>Kayak Punya Sendiri.
                </h1>
                <a href="#booking-form" class="btn btn-black text-base mt-2">Mulai Penitipan Hewan</a>
            </div>
        </div>
        <img src="../../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- masalah pelanggan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-center font-bold text-2xl mb-8">Apakah Kamu Mengalami Masalah Ini?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $masalah = [
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Anabulmu mengalami stres saat harus ikut mudik/lebaran'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Merasa tidak tega meninggalkan anabulmu sendirian di rumah tanpa pengawasan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Sulit mencari orang yang bisa merawat anabulmu selama kamu pergi liburan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Khawatir jika dititipkan ke orang lain, perawatannya tidak sesuai dengan yang kamu inginkan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Tidak memiliki kerabat atau teman yang bisa dititipi untuk menjaga anabulmu'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Terbatasnya waktu mengurus kebutuhan anabulmu dengan baik'
                    ]
                ];

                foreach ($masalah as $item) {
                    echo '<div class="bg-gray-200 p-4 rounded-lg h-full">
                            <div class="flex items-center h-full">
                                <div class="text-2xl text-orange-500 mr-6 flex-shrink-0">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="text-sm flex-grow">' . $item['text'] . '</p>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- syarat penitipan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-5/12 mb-6 lg:mb-0">
                    <img src="../../aset/anjingpenitipan1.png" alt="Grooming Illustration" class="w-full">
                </div>
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-4 text-center pl-20">Syarat Penitipan Hewan
                    </h2>
                    <?php
                    $syarat = [
                        ['text' => 'Hewan dalam kondisi sehat & tidak berkutu. Kami akan melakukan pemeriksaan kondisi anjing & kucing Anda saat tiba. Hal ini bertujuan untuk menjaga keamanan & kesehatan semua hewan yang dititipkan.'],
                        ['text' => 'Jika setelah pemeriksaan ditemukan kutu atau masalah kulit, akan dilakukan pengobatan khusus segera saat kedatangan. Hewan tetap bisa dititipkan dalam ruang perawatan khusus yang terpisah dari hewan yang sehat.'],
                        ['text' => 'Selama penitipan, hewan kesayangan Anda memiliki waktu bermain bebas bersama pengunjung/hewan lain tanpa diikat atau dikandangkan, kecuali atas permintaan khusus atau dalam situasi tertentu.'],
                        ['text' => 'Anda dapat membawa perlengkapan khusus sesuai kebutuhan seperti makanan, cemilan, vitamin, mainan, dan sebagainya.']
                    ];

                    foreach ($syarat as $index => $item) {
                        $number = $index + 1;
                        echo '<div class="flex items-start pl-20">
                                <div class="flex items-center justify-center font-semibold text-base mr-4">' . $number . '</i>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-base flex-1">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- bagian fasilitas & benefit -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-4 text-center pr-20">Fasilitas & Benefit
                    </h2>
                    <?php
                    $fasilitas = [
                        ['text' => 'Jasa Antar-Jemput khusus area DIY (biaya tambahan sesuai jarak)'],
                        ['text' => 'Free grooming untuk minimal 1 minggu penitipan'],
                        ['text' => 'Full AC (Indoor Cage)'],
                        ['text' => 'Kandang Bersih dan Luas dengan satu kandang untuk satu ekor anjing/kucing, tidak dicampur dengan hewan lain'],
                        ['text' => 'Update Foto & Video harian'],
                        ['text' => 'Pembersihan Alat Makan & Minum 2x Sehari'],
                        ['text' => 'Pembersihan Toilet Setiap Hari'],
                        ['text' => 'Paramedis yang siaga untuk memantau kondisi anabul'],
                        ['text' => 'Penyediaan berbagai mainan untuk Anjing & Kucing']
                    ];

                    foreach ($fasilitas as $index => $item) {
                        $number = $index + 1;
                        echo '<div class="flex items-start">
                                <div class="flex items-center justify-center font-semibold text-base mr-4">' . $number . '</i>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-base flex-1 mr-10">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
                <div class="w-full lg:w-5/12 mb-6 lg:mb-0">
                    <img src="../../aset/anjingpenitipan2.png" alt="Grooming Illustration" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- bagian fasilitas & benefit -->
    <section class="py-20 px-16">
        <div class="container mx-auto px-4 py-10 bg-orange-100 rounded-3xl">
            <h2 class="text-center font-bold text-2xl mb-2">Penitipan Hewan Terpercaya & Aman!</h2>
            <div class="text-center mb-8">
                <p class="font-medium text-base mb-4">Mulai Dari <strong>35 Ribu Rupiah</strong> Per Hari</p>
                <a href="#booking-form"
                    class="inline-flex items-center justify-center text-sm bg-orange-500 text-white px-4 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-heart text-2xl mr-2"></i>Booking Penitipan Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- bagian form booking -->
    <section id="booking-form" class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-md border border-grey">
                <div class="bg-orange-500 text-white p-6 text-center">
                    <h3 class="text-lg font-semibold mb-2">Form Layanan Penitipan</h3>
                    <p class="text-sm">Layanan penitipan untuk hewan kesayangan Anda</p>
                </div>

                <div class="p-8">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-dangeralert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="space-y-4" id="bookingForm" onsubmit="return validateForm()">
                        <!-- Data Pelanggan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user me-2"></i>Data Pelanggan
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" name="nama_lengkap" class="form-control"
                                        value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="tel" name="nomor_telepon" class="form-control"
                                        value="<?php echo htmlspecialchars($user_data['nomor_telepon']); ?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Kontak Darurat <span class="text-danger">*</span></label>
                                    <input type="text" name="kontak_darurat" class="form-control"
                                        placeholder="Nama dan nomor telepon kontak darurat (contoh: Budi - 081234567890)"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Data Hewan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-paw me-2"></i>Data Hewan Peliharaan
                            </h5>

                            <?php if (!empty($pets_data)): ?>
                                <div class="mb-4">
                                    <label class="form-label">Pilih Hewan Peliharaan</label>
                                    <div class="pet-selection">
                                        <?php foreach ($pets_data as $pet): ?>
                                            <div class="pet-option">
                                                <div class="form-check item">
                                                    <input class="form-check-input accent-orange-500" type="radio"
                                                        name="id_anabul_existing" value="<?php echo $pet['id_anabul']; ?>"
                                                        id="pet_<?php echo $pet['id_anabul']; ?>"
                                                        data-name="<?php echo htmlspecialchars($pet['nama_hewan']); ?>"
                                                        data-category="<?php echo htmlspecialchars($pet['spesies']); ?>"
                                                        data-special="<?php echo htmlspecialchars($pet['ciri_khusus']); ?>">
                                                    <label class="form-check-label" for="pet_<?php echo $pet['id_anabul']; ?>">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-paw me-2 text-orange-500"></i>
                                                            <div>
                                                                <span
                                                                    class="font-semibold"><?php echo htmlspecialchars($pet['nama_hewan']); ?></span>
                                                                <span
                                                                    class="text-muted ms-2">(<?php echo htmlspecialchars($pet['spesies']); ?>)</span>
                                                                <?php if ($pet['ciri_khusus']): ?>
                                                                    <div class="text-muted small mt-1">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        <?php echo htmlspecialchars($pet['ciri_khusus']); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="pet-option">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="id_anabul_existing"
                                                    value="new" id="pet_new">
                                                <label class="form-check-label" for="pet_new">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-plus-circle me-2 text-orange-500"></i>
                                                        <span class="font-semibold">Tambah Hewan Baru</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Form input manual untuk hewan baru -->
                            <div id="manual-pet-form" <?php echo !empty($pets_data) ? 'style="display: none;"' : ''; ?>>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Hewan Peliharaan</label>
                                        <input type="text" name="nama_hewan" id="pet_name" class="form-control"
                                            placeholder="Masukkan nama hewan peliharaan" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Spesies Hewan</label>
                                        <select name="spesies" id="spesies" class="form-control" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                            <option value="">Pilih spesies hewan</option>
                                            <option value="kucing">Kucing</option>
                                            <option value="anjing">Anjing</option>
                                            <option value="kelinci">Kelinci</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ciri-Ciri Khusus (Opsional)</label>
                                        <input type="text" name="ciri_khusus" id="pet_special" class="form-control"
                                            placeholder="Contoh: Warna bulu, ukuran, atau kondisi khusus">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Layanan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-home me-2"></i>Detail Layanan Penitipan
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                                        min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control"
                                        min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                </div>
                                <div class="col-12">
                                    <div id="duration-info" class="duration-info" style="display: none;">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span id="duration-text">Durasi penitipan: 0 hari</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Paket Layanan Penitipan <span
                                            class="text-danger">*</span></label>
                                    <select name="service_type" id="service_type" class="form-control" required>
                                        <option value="">Pilih paket layanan</option>
                                        <?php foreach ($services_data as $service): ?>
                                            <option value="<?php echo $service['id_layanan']; ?>"
                                                data-harga="<?php echo $service['harga']; ?>">
                                                <?php echo htmlspecialchars($service['nama_layanan']); ?> - Rp
                                                <?php echo number_format($service['harga'], 0, ',', '.'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total Harga</label>
                                    <input type="text" id="total_price" class="form-control" placeholder="Rp0" readonly>
                                </div>
                                <div class="col-12">
                                    <div id="price-breakdown" class="price-breakdown" style="display: none;">
                                        <h6 class="mb-2"><i class="fas fa-calculator me-2"></i>Rincian Harga</h6>
                                        <div id="price-details"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kebutuhan Khusus Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-heart me-2"></i>Kebutuhan Khusus (Opsional)
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Pola Makan</label>
                                    <textarea name="pola_makan" class="form-control"
                                        placeholder="Contoh: Makan 2x sehari, pagi jam 7, sore jam 5. Makanan favorit: whiskas tuna"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Obat-obatan</label>
                                    <textarea name="obat_obatan" class="form-control"
                                        placeholder="Contoh: Vitamin setiap hari setelah makan, obat cacing setiap bulan"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kebiasaan Penting</label>
                                    <textarea name="kebiasaan_penting" class="form-control"
                                        placeholder="Contoh: Suka bermain di sore hari, takut suara keras, perlu selimut saat tidur"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 px-3">
                            <button type="reset" class="btn-reset">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-check me-2"></i>Pesan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../../includes/footer.php'; ?>

    <!-- Custom Confirmation Modal -->
    <div id="customConfirmModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
            <div class="text-xl font-bold text-gray-900 mb-4 text-center" id="confirmMessage"></div>
            <div class="flex justify-end space-x-3">
                <button id="confirmCancelBtn"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-full hover:bg-gray-100 transition-colors text-sm">
                    Batal
                </button>
                <button id="confirmOKBtn"
                    class="px-6 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors text-sm">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Custom confirmation modal functions
        function showCustomConfirm(message, callback) {
            const modal = document.getElementById('customConfirmModal');
            const messageEl = document.getElementById('confirmMessage');
            const cancelBtn = document.getElementById('confirmCancelBtn');
            const okBtn = document.getElementById('confirmOKBtn');

            messageEl.textContent = message;
            modal.classList.remove('hidden');

            const handleConfirm = () => {
                modal.classList.add('hidden');
                callback(true);
                cleanup();
            };

            const handleCancel = () => {
                modal.classList.add('hidden');
                callback(false);
                cleanup();
            };

            const cleanup = () => {
                okBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            okBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);
        }

        // Harga per hari untuk setiap layanan
        const hargaPerHari = {};
        <?php foreach ($services_data as $service): ?>
            hargaPerHari['<?php echo $service['id_layanan']; ?>'] = <?php echo intval($service['harga']); ?>;
            console.log('Service <?php echo $service['id_layanan']; ?>: <?php echo $service['harga']; ?>');
        <?php endforeach; ?>

        function validateForm() {
            const form = document.getElementById('bookingForm');
            const errorMessages = [];

            // Check pet selection
            const selectedPet = form.querySelector('input[name="id_anabul_existing"]:checked');
            if (!selectedPet) {
                showCustomConfirm('Silakan pilih hewan peliharaan atau tambahkan hewan baru', function (result) {
                    if (result) {
                        // User clicked "Ya, Lanjutkan" - they can continue to add a pet
                        // The form will show the manual pet form
                        document.querySelector('input[name="id_anabul_existing"][value="new"]').checked = true;
                        document.getElementById('manual-pet-form').style.display = 'block';
                        document.getElementById('pet_name').required = true;
                        document.getElementById('spesies').required = true;
                    }
                });
                return false;
            } else if (selectedPet.value === 'new') {
                const petName = form.querySelector('#pet_name').value.trim();
                const petCategory = form.querySelector('#spesies').value;
                if (!petName) errorMessages.push('Nama hewan peliharaan harus diisi');
                if (!petCategory) errorMessages.push('Spesies hewan harus dipilih');
            }

            // Check service details
            const serviceType = form.querySelector('select[name="service_type"]').value;
            const tanggalMasuk = form.querySelector('input[name="tanggal_masuk"]').value;
            const tanggalKeluar = form.querySelector('input[name="tanggal_keluar"]').value;
            const kontakDarurat = form.querySelector('input[name="kontak_darurat"]').value.trim();

            if (!kontakDarurat) errorMessages.push('Kontak darurat harus diisi');
            if (!serviceType) errorMessages.push('Paket layanan harus dipilih');
            if (!tanggalMasuk) errorMessages.push('Tanggal masuk harus dipilih');
            if (!tanggalKeluar) errorMessages.push('Tanggal keluar harus dipilih');

            // Validate dates - PERBAIKAN DISINI
            if (tanggalMasuk && tanggalKeluar) {
                const masuk = new Date(tanggalMasuk);
                const keluar = new Date(tanggalKeluar);
                if (keluar <= masuk) {
                    errorMessages.push('Tanggal keluar harus setelah tanggal masuk');
                }
            }

            if (errorMessages.length > 0) {
                showCustomConfirm('Mohon lengkapi data berikut:\n' + errorMessages.join('\n'), function (result) {
                    if (result) {
                        // User clicked "Ya, Lanjutkan" - they can continue filling the form
                        // Focus on the first error field
                        if (!kontakDarurat) {
                            form.querySelector('input[name="kontak_darurat"]').focus();
                        } else if (!serviceType) {
                            form.querySelector('select[name="service_type"]').focus();
                        } else if (!tanggalMasuk) {
                            form.querySelector('input[name="tanggal_masuk"]').focus();
                        } else if (!tanggalKeluar) {
                            form.querySelector('input[name="tanggal_keluar"]').focus();
                        }
                    }
                });
                return false;
            }

            return true;
        }

        function calculateDurationAndPrice() {
            const tanggalMasuk = document.getElementById('tanggal_masuk').value;
            const tanggalKeluar = document.getElementById('tanggal_keluar').value;
            const serviceType = document.getElementById('service_type').value;
            const durationInfo = document.getElementById('duration-info');
            const durationText = document.getElementById('duration-text');
            const totalPrice = document.getElementById('total_price');
            const priceBreakdown = document.getElementById('price-breakdown');
            const priceDetails = document.getElementById('price-details');

            console.log('Calculating price with:', {
                tanggalMasuk,
                tanggalKeluar,
                serviceType,
                hargaPerHari
            });

            if (tanggalMasuk && tanggalKeluar && serviceType) {
                const masuk = new Date(tanggalMasuk);
                const keluar = new Date(tanggalKeluar); // ✅ Perbaikan disini
                const timeDiff = keluar.getTime() - masuk.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

                if (daysDiff > 0) {
                    durationText.textContent = `Durasi penitipan: ${daysDiff} hari`;
                    durationInfo.style.display = 'block';

                    const hargaPerHariValue = parseInt(hargaPerHari[serviceType]);
                    console.log('Harga per hari:', hargaPerHariValue);

                    if (!isNaN(hargaPerHariValue)) {
                        const totalHarga = hargaPerHariValue * daysDiff;
                        console.log('Total harga:', totalHarga);

                        totalPrice.value = 'Rp ' + totalHarga.toLocaleString('id-ID');

                        // Show price breakdown
                        const serviceName = document.querySelector(`#service_type option[value="${serviceType}"]`).textContent.split(' - ')[0];

                        priceDetails.innerHTML = `
                    <div class="d-flex justify-content-between mb-1">
                        <span>Paket ${serviceName}:</span>
                        <span>Rp ${hargaPerHariValue.toLocaleString('id-ID')}/hari</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Durasi:</span>
                        <span>${daysDiff} hari</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>Total:</span>
                        <span>Rp ${totalHarga.toLocaleString('id-ID')}</span>
                    </div>
                `;
                        priceBreakdown.style.display = 'block';
                    }
                }
            }
        }

        // Add event listeners for all select elements
        document.addEventListener('DOMContentLoaded', function () {
            // Reset form when page loads
            const form = document.getElementById('bookingForm');
            if (form) {
                form.reset();
            }

            // Definisikan variabel yang hilang
            const serviceTypeSelect = document.getElementById('service_type');
            const petCategorySelect = document.getElementById('spesies');
            const tanggalMasukInput = document.getElementById('tanggal_masuk');
            const tanggalKeluarInput = document.getElementById('tanggal_keluar');

            // Add event listeners
            if (serviceTypeSelect) {
                serviceTypeSelect.addEventListener('change', function () {
                    handleSelectColor(this);
                    calculateDurationAndPrice();
                });
                handleSelectColor(serviceTypeSelect);
            }

            if (petCategorySelect) {
                petCategorySelect.addEventListener('change', function () {
                    handleSelectColor(this);
                });
                handleSelectColor(petCategorySelect);
            }

            // Add event listeners untuk tanggal
            if (tanggalMasukInput) {
                tanggalMasukInput.addEventListener('change', function () {
                    // Set minimum date for tanggal keluar to be the day after tanggal masuk
                    if (this.value) {
                        const selectedDate = new Date(this.value);
                        selectedDate.setDate(selectedDate.getDate() + 1);
                        const minDate = selectedDate.toISOString().split('T')[0];
                        tanggalKeluarInput.min = minDate;

                        // If tanggal keluar is before the new minimum, clear it
                        if (tanggalKeluarInput.value && new Date(tanggalKeluarInput.value) <= new Date(this.value)) {
                            tanggalKeluarInput.value = '';
                        }
                    }
                    calculateDurationAndPrice();
                });
            }

            if (tanggalKeluarInput) {
                tanggalKeluarInput.addEventListener('change', function () {
                    // Validate that tanggal keluar is after tanggal masuk
                    if (tanggalMasukInput.value && this.value) {
                        const masuk = new Date(tanggalMasukInput.value);
                        const keluar = new Date(this.value);
                        if (keluar <= masuk) {
                            alert('Tanggal keluar harus setelah tanggal masuk');
                            this.value = '';
                        }
                    }
                    calculateDurationAndPrice();
                });
            }

            // Pet selection handlers
            document.querySelectorAll('input[name="id_anabul_existing"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const manualForm = document.getElementById('manual-pet-form');
                    if (this.value === 'new') {
                        manualForm.style.display = 'block';
                        // Make fields required
                        document.getElementById('pet_name').required = true;
                        document.getElementById('spesies').required = true;
                    } else {
                        manualForm.style.display = 'none';
                        // Remove required attribute
                        document.getElementById('pet_name').required = false;
                        document.getElementById('spesies').required = false;
                    }
                });
            });
        });

        function handleSelectColor(selectElement) {
            if (selectElement.value) {
                selectElement.style.color = '#000';
            } else {
                selectElement.style.color = '#888';
            }
        }
    </script>
</body>

</html>