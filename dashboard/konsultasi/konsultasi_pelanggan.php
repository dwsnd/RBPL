<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch available doctors with their schedules
$dokter_data = [];
$dokter_query = "SELECT d.id_dokter, d.nama_dokter, d.spesialisasi, d.tarif_konsultasi, d.pengalaman_tahun,
                 GROUP_CONCAT(CONCAT(jd.hari, ':', jd.waktu_mulai, '-', jd.waktu_selesai, ':', jd.slot_maksimal) SEPARATOR ',') as jadwal_tersedia
                 FROM dokter_hewan d 
                 LEFT JOIN jadwal_dokter jd ON d.id_dokter = jd.id_dokter 
                 WHERE d.status = 'aktif' AND jd.status = 'aktif'
                 GROUP BY d.id_dokter";
$dokter_result = mysqli_query($conn, $dokter_query);
if ($dokter_result && mysqli_num_rows($dokter_result) > 0) {
    while ($row = mysqli_fetch_assoc($dokter_result)) {
        $dokter_data[] = $row;
    }
}

// Fetch user data
$user_data = [];
$query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_pelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

// Fetch user's pets data
$pets_data = [];
$pets_query = "SELECT id_anabul, nama_hewan, spesies, ciri_khusus FROM anabul WHERE id_pelanggan = ? AND status = 'aktif'";
$stmt = mysqli_prepare($conn, $pets_query);
mysqli_stmt_bind_param($stmt, "i", $id_pelanggan);
mysqli_stmt_execute($stmt);
$pets_result = mysqli_stmt_get_result($stmt);
if ($pets_result && mysqli_num_rows($pets_result) > 0) {
    while ($row = mysqli_fetch_assoc($pets_result)) {
        $pets_data[] = $row;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $pet_spesies = mysqli_real_escape_string($conn, $_POST['pet_spesies']);
    $pet_ciri_khusus = mysqli_real_escape_string($conn, $_POST['pet_ciri_khusus']);
    $keluhan_utama = mysqli_real_escape_string($conn, $_POST['keluhan_utama']);
    $gejala = mysqli_real_escape_string($conn, $_POST['gejala']);
    $durasi_gejala = mysqli_real_escape_string($conn, $_POST['durasi_gejala']);
    $service_date = mysqli_real_escape_string($conn, $_POST['service_date']);
    $id_dokter = mysqli_real_escape_string($conn, $_POST['id_dokter']);
    $tingkat_keparahan = mysqli_real_escape_string($conn, $_POST['tingkat_keparahan']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;

    // Get doctor's consultation fee
    $dokter_fee_query = "SELECT tarif_konsultasi FROM dokter_hewan WHERE id_dokter = ?";
    $stmt = mysqli_prepare($conn, $dokter_fee_query);
    mysqli_stmt_bind_param($stmt, "i", $id_dokter);
    mysqli_stmt_execute($stmt);
    $dokter_fee_result = mysqli_stmt_get_result($stmt);
    $dokter_fee = 0;
    if ($dokter_fee_result && mysqli_num_rows($dokter_fee_result) > 0) {
        $dokter_fee = mysqli_fetch_assoc($dokter_fee_result)['tarif_konsultasi'];
    }

    // PERBAIKAN: Konversi nama hari ke bahasa Indonesia dan format yang benar
    $day_names = [
        'Sunday' => 'minggu',
        'Monday' => 'senin',
        'Tuesday' => 'selasa',
        'Wednesday' => 'rabu',
        'Thursday' => 'kamis',
        'Friday' => 'jumat',
        'Saturday' => 'sabtu'
    ];

    $english_day = date('l', strtotime($service_date));
    $indonesian_day = $day_names[$english_day];

    // PERBAIKAN: Query yang lebih akurat untuk validasi jadwal
    $cek_jadwal = "SELECT jd.*, 
                   (SELECT COUNT(*) FROM konsultasi k 
                    WHERE k.id_dokter = ? 
                    AND k.tanggal_kontrol = ? 
                    AND k.status_konsultasi != 'dibatalkan') as slot_terpakai
                   FROM jadwal_dokter jd
                   WHERE jd.id_dokter = ? 
                   AND jd.hari = ?
                   AND jd.status = 'aktif'";

    $stmt = mysqli_prepare($conn, $cek_jadwal);
    mysqli_stmt_bind_param($stmt, "isis", $id_dokter, $service_date, $id_dokter, $indonesian_day);
    mysqli_stmt_execute($stmt);
    $jadwal_result = mysqli_stmt_get_result($stmt);

    if (!$jadwal_result || mysqli_num_rows($jadwal_result) == 0) {
        $error_message = "Maaf, dokter tidak tersedia pada hari " . ucfirst($indonesian_day) . " (" . date('d-m-Y', strtotime($service_date)) . ").";
    } else {
        $jadwal_data = mysqli_fetch_assoc($jadwal_result);

        if ($jadwal_data['slot_terpakai'] >= $jadwal_data['slot_maksimal']) {
            $error_message = "Maaf, slot konsultasi untuk hari " . ucfirst($indonesian_day) . " (" . date('d-m-Y', strtotime($service_date)) . ") sudah penuh. Slot tersedia: " . $jadwal_data['slot_maksimal'] . ", sudah terpakai: " . $jadwal_data['slot_terpakai'];
        } else {
            // Start transaction
            mysqli_begin_transaction($conn);

            try {
                $id_anabul = null;

                // If existing pet is selected
                if ($id_anabul_existing && $id_anabul_existing !== 'new') {
                    $id_anabul = $id_anabul_existing;
                } else {
                    // Insert new pet data dengan prepared statement
                    $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, spesies, ciri_khusus, created_at) 
                                  VALUES (?, ?, ?, ?, NOW())";
                    $stmt = mysqli_prepare($conn, $insert_pet);
                    mysqli_stmt_bind_param($stmt, "isss", $id_pelanggan, $pet_name, $pet_spesies, $pet_ciri_khusus);

                    if (mysqli_stmt_execute($stmt)) {
                        $id_anabul = mysqli_insert_id($conn);
                    } else {
                        throw new Exception("Error inserting pet data: " . mysqli_error($conn));
                    }
                }

                // Generate unique order number
                $date = date('Ymd');
                $random = rand(1000, 9999);
                $nomor_pesanan = "INV-" . $date . "-" . $random;

                // Create pesanan record first
                $insert_pesanan = "INSERT INTO pesanan (nomor_pesanan, id_pelanggan, total_harga, status_pesanan, jenis_pesanan, created_at) 
                                  VALUES (?, ?, ?, 'pending', 'konsultasi', NOW())";
                $stmt = mysqli_prepare($conn, $insert_pesanan);
                mysqli_stmt_bind_param($stmt, "sid", $nomor_pesanan, $id_pelanggan, $dokter_fee);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error creating pesanan: " . mysqli_error($conn));
                }
                $id_pesanan = mysqli_insert_id($conn);

                // Insert consultation data with id_pesanan
                $insert_consultation = "INSERT INTO konsultasi 
                                (id_pesanan, id_dokter, id_anabul, keluhan_utama, gejala, durasi_gejala,
                                 tanggal_kontrol, status_konsultasi, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

                $stmt = mysqli_prepare($conn, $insert_consultation);
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiissss",
                    $id_pesanan,
                    $id_dokter,
                    $id_anabul,
                    $keluhan_utama,
                    $gejala,
                    $durasi_gejala,
                    $service_date
                );

                if (mysqli_stmt_execute($stmt)) {
                    $id_konsultasi = mysqli_insert_id($conn);

                    // Create initial medical record
                    $insert_riwayat = "INSERT INTO riwayat_medis 
                                      (id_anabul, id_konsultasi, tanggal, 
                                       deskripsi, created_at)
                                      VALUES (?, ?, ?, ?, NOW())";
                    $stmt_riwayat = mysqli_prepare($conn, $insert_riwayat);
                    $deskripsi_riwayat = "Konsultasi: " . $keluhan_utama . " | Gejala: " . $gejala;
                    mysqli_stmt_bind_param(
                        $stmt_riwayat,
                        "iiss",
                        $id_anabul,
                        $id_konsultasi,
                        $service_date,
                        $deskripsi_riwayat
                    );
                    mysqli_stmt_execute($stmt_riwayat);

                    // Commit transaction
                    mysqli_commit($conn);

                    // Set success message
                    $_SESSION['success_message'] = "Konsultasi berhasil dijadwalkan! ID Konsultasi: " . $id_konsultasi .
                        " untuk tanggal " . date('d-m-Y', strtotime($service_date)) .
                        " (" . ucfirst($indonesian_day) . ")";

                    // Redirect to pesanan.php
                    header('Location: ../../profilpelanggan/pesanan/index_pesanan.php');
                    exit();
                } else {
                    throw new Exception("Error inserting consultation data: " . mysqli_error($conn));
                }

            } catch (Exception $e) {
                // Rollback transaction
                mysqli_rollback($conn);
                $error_message = $e->getMessage();
            }
        }
    }
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

        .text-danger {
            color: #dc3545 !important;
        }

        .alert {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        .consultation-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .consultation-info .fas {
            color: #0066cc;
        }

        /* New styles for consultation schedule section */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .form-control-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
        }

        .alert-info {
            background-color: #e7f3ff;
            border-left: 4px solid #0dcaf0;
        }

        .alert-info .text-info {
            color: #0dcaf0 !important;
        }

        .alert-heading {
            color: #0c5460;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: #fd7e14;
        }

        .form-control:focus,
        .form-control-lg:focus {
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Nggak Perlu Panik, Dokter
                    <br>Kami Siaga Bantu Hewan Kesayanganmu!
                </h1>
                <a href="#booking-form" class="btn btn-black text-base mt-2">Mulai Konsultasi Hewan</a>
            </div>
        </div>
        <img src="../../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- gejala konsultasi -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-center font-bold text-2xl mb-8">Apakah Anabul Kesayanganmu Mengalami Masalah Ini?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                $gejala = [
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Biasanya aktif tapi sekarang lemas'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Kehilangan nafsu makan atau minum'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Mengalami gangguan pencernaan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Sering muntah dan bersin berulang kali'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Terdapat luka/cedera/pincang'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Menggaruk-garuk tubuh secara terus-menerus'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Sering menjilat atau menggigit tubuhnya secara berlebihan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Mengalami perubahan perilaku seperti agresif atau terlalu pasif'
                    ]
                ];

                foreach ($gejala as $item) {
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

    <!-- info fasilitas -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-5/12 mb-6 lg:mb-0">
                    <img src="../../aset/konsultasi1.png" alt="Grooming Illustration" class="w-full">
                </div>
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-4 text-center pl-20">Layanan Kami
                    </h2>
                    <?php
                    $layanan = [
                        ['text' => 'Konsultasi & Pemeriksaan Rutin'],
                        ['text' => 'Vaksinasi & Sterilisasi'],
                        ['text' => 'Rawat Inap & ICU'],
                        ['text' => 'Grooming & Perawatan Kesehatan'],
                        ['text' => 'Operasi bedah & Penanganan Darurat'],
                        ['text' => 'Laboratorium & Diagnotik'],
                        ['text' => 'USG'],
                        ['text' => 'Farmasi Lengkap']
                    ];

                    foreach ($layanan as $index => $item) {
                        $number = $index + 1;
                        echo '<div class="flex items-start pl-32">
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
                    $syarat = [
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

                    foreach ($syarat as $index => $item) {
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
                    <img src="../../aset/konsultasi2.png" alt="Grooming Illustration" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- bagian promo -->
    <section class="py-20 px-16">
        <div class="container mx-auto px-4 py-10 bg-green-100 rounded-3xl">
            <h2 class="text-center font-bold text-2xl mb-2">Dapatkan Konsultasi Terbaik Untuk Hewan Kesayangan Anda!
            </h2>
            <div class="text-center mb-8">
                <p class="font-medium text-base mb-4">Konsultasi Online Mulai Dari <strong>50 Ribu Rupiah</strong> Per
                    Sesi</p>
                <a href="#booking-form"
                    class="inline-flex items-center justify-center text-sm bg-green-500 text-white px-4 py-2 rounded font-semibold hover:bg-green-600 transition duration-200">
                    <i class="fas fa-stethoscope text-2xl mr-2"></i>Konsultasi Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- bagian form konsultasi -->
    <section id="booking-form" class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-md border border-grey">
                <div class="bg-orange-500 text-white p-6 text-center">
                    <h3 class="text-lg font-semibold mb-2">Form Konsultasi Hewan</h3>
                    <p class="text-sm">Konsultasi kesehatan untuk hewan kesayangan Anda</p>
                </div>

                <div class="p-8">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="consultation-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Informasi Konsultasi:</strong>
                                <p class="mb-0 small mt-1">Konsultasi akan dilakukan oleh dokter hewan berpengalaman.
                                    Biaya konsultasi akan ditentukan setelah pemeriksaan awal.</p>
                            </div>
                        </div>
                    </div>

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
                                        value="<?php echo htmlspecialchars($user_data['nama_lengkap'] ?? ''); ?>"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="tel" name="nomor_telepon" class="form-control"
                                        value="<?php echo htmlspecialchars($user_data['nomor_telepon'] ?? ''); ?>"
                                        readonly>
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
                                                        data-spesies="<?php echo htmlspecialchars($pet['spesies']); ?>"
                                                        data-ciri="<?php echo htmlspecialchars($pet['ciri_khusus']); ?>">
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
                                        <input type="text" name="pet_name" id="pet_name" class="form-control"
                                            placeholder="Masukkan nama hewan peliharaan" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Spesies Hewan</label>
                                        <select name="pet_spesies" id="pet_spesies" class="form-control" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                            <option value="">Pilih spesies hewan</option>
                                            <option value="kucing">Kucing</option>
                                            <option value="anjing">Anjing</option>
                                            <option value="kelinci">Kelinci</option>
                                            <option value="hamster">Hamster</option>
                                            <option value="burung">Burung</option>
                                            <option value="reptil">Reptil</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ciri Khusus (Opsional)</label>
                                        <input type="text" name="pet_ciri_khusus" id="pet_ciri_khusus"
                                            class="form-control"
                                            placeholder="Contoh: Warna bulu, ukuran, atau kondisi khusus">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keluhan Hewan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-stethoscope me-2"></i>Keluhan Hewan
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Keluhan Utama <span class="text-danger">*</span></label>
                                    <textarea name="keluhan_utama" class="form-control" rows="3" required
                                        placeholder="Jelaskan keluhan utama yang dialami hewan peliharaan Anda"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Gejala yang Terlihat <span
                                            class="text-danger">*</span></label>
                                    <textarea name="gejala" class="form-control" rows="3" required
                                        placeholder="Jelaskan gejala yang terlihat (contoh: tidak mau makan, lemas, muntah, diare, batuk, dll)"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Durasi Gejala <span class="text-danger">*</span></label>
                                    <select name="durasi_gejala" class="form-control" required>
                                        <option value="">Pilih durasi gejala</option>
                                        <option value="kurang_dari_1_hari">Kurang dari 1 hari</option>
                                        <option value="1-3_hari">1-3 hari</option>
                                        <option value="4-7_hari">4-7 hari</option>
                                        <option value="1-2_minggu">1-2 minggu</option>
                                        <option value="lebih_dari_2_minggu">Lebih dari 2 minggu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tingkat Keparahan</label>
                                    <select name="tingkat_keparahan" class="form-control">
                                        <option value="">Pilih tingkat keparahan</option>
                                        <option value="ringan">Ringan</option>
                                        <option value="sedang">Sedang</option>
                                        <option value="berat">Berat</option>
                                        <option value="sangat_berat">Sangat Berat</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal Konsultasi Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-calendar me-2"></i>Jadwal Konsultasi
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <label class="form-label">
                                                <i class="fas fa-calendar-day text-orange-500 me-2"></i>
                                                Tanggal Konsultasi <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="service_date" id="service_date"
                                                class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Konsultasi hanya tersedia pada hari Sabtu dan Minggu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <label class="form-label">
                                                <i class="fas fa-user-md text-orange-500 me-2"></i>
                                                Pilih Dokter <span class="text-danger">*</span>
                                            </label>
                                            <select name="id_dokter" id="id_dokter" class="form-control" required>
                                                <option value="">Pilih dokter hewan</option>
                                                <?php foreach ($dokter_data as $dokter): ?>
                                                    <option value="<?php echo $dokter['id_dokter']; ?>"
                                                        data-tarif="<?php echo $dokter['tarif_konsultasi']; ?>"
                                                        data-jadwal="<?php echo htmlspecialchars($dokter['jadwal_tersedia']); ?>">
                                                        <?php echo htmlspecialchars($dokter['nama_dokter']); ?>
                                                        (<?php echo htmlspecialchars($dokter['spesialisasi']); ?>) -
                                                        Rp
                                                        <?php echo number_format($dokter['tarif_konsultasi'], 0, ',', '.'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Pilih dokter sesuai dengan spesialisasi yang dibutuhkan
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info border-0 shadow-sm py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-calendar-alt text-info"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="alert-heading mb-2 fw-bold">Informasi Jadwal Konsultasi</h6>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-check-circle text-info me-2 small"></i>
                                                        <span class="small">Konsultasi hanya tersedia pada hari
                                                            <strong>Sabtu dan Minggu</strong></span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-check-circle text-info me-2 small"></i>
                                                        <span class="small">Jadwal yang tersedia akan disesuaikan dengan
                                                            ketersediaan dokter yang dipilih</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-check-circle text-info me-2 small"></i>
                                                        <span class="small">Durasi konsultasi sekitar 30-45 menit per
                                                            sesi</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 px-3">
                            <button type="reset" class="btn-reset">
                                <i class="fas fa-redo me-2"></i>Reset Form
                            </button>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-calendar-check me-2"></i>Jadwalkan Konsultasi
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

        // Add this at the beginning of your script
        window.onload = function () {
            // Reset form when page loads
            const form = document.getElementById('bookingForm');
            if (form) {
                form.reset();
                // Hide manual pet form if it was shown
                document.getElementById('manual-pet-form').style.display = 'none';
                // Remove selected class from all pet options
                document.querySelectorAll('.pet-option').forEach(option => {
                    option.classList.remove('selected');
                });
                // Reset select colors
                document.querySelectorAll('select').forEach(select => {
                    select.style.color = '#888';
                });
            }
        };

        // Pet selection handlers
        document.querySelectorAll('input[name="id_anabul_existing"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const manualForm = document.getElementById('manual-pet-form');
                if (this.value === 'new') {
                    manualForm.style.display = 'block';
                    // Make fields required
                    document.getElementById('pet_name').required = true;
                    document.getElementById('pet_spesies').required = true;
                } else {
                    manualForm.style.display = 'none';
                    // Remove required attribute
                    document.getElementById('pet_name').required = false;
                    document.getElementById('pet_spesies').required = false;
                }
            });
        });

        function handleSelectColor(selectElement) {
            if (selectElement.value) {
                selectElement.style.color = '#000';
            } else {
                selectElement.style.color = '#888';
            }
        }

        // Validasi hanya weekend
        document.addEventListener('DOMContentLoaded', function () {
            const serviceDateInput = document.getElementById('service_date');
            const dokterSelect = document.getElementById('id_dokter');

            // Validasi hanya weekend
            serviceDateInput.addEventListener('change', function () {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 6 = Saturday

                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Bukan Sabtu atau Minggu
                    showCustomConfirm('Konsultasi hanya tersedia pada hari Sabtu dan Minggu. Silakan pilih tanggal lain.', function (result) {
                        if (result) {
                            serviceDateInput.value = '';
                        }
                    });
                    return;
                }
            });

            // Update form validation
            window.validateForm = function () {
                const form = document.getElementById('bookingForm');
                const errorMessages = [];

                // Validasi tanggal weekend
                const serviceDate = serviceDateInput.value;
                if (serviceDate) {
                    const selectedDate = new Date(serviceDate);
                    const dayOfWeek = selectedDate.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                        errorMessages.push('Konsultasi hanya tersedia pada hari Sabtu dan Minggu');
                    }
                }

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
                            document.getElementById('pet_spesies').required = true;
                        }
                    });
                    return false;
                } else if (selectedPet.value === 'new') {
                    const petName = form.querySelector('#pet_name').value.trim();
                    const petSpesies = form.querySelector('#pet_spesies').value;
                    if (!petName) errorMessages.push('Nama hewan peliharaan harus diisi');
                    if (!petSpesies) errorMessages.push('Spesies hewan harus dipilih');
                }

                // Check consultation details
                const keluhanUtama = form.querySelector('textarea[name="keluhan_utama"]').value.trim();
                const gejala = form.querySelector('textarea[name="gejala"]').value.trim();
                const durasiGejala = form.querySelector('select[name="durasi_gejala"]').value;
                const idDokter = form.querySelector('select[name="id_dokter"]').value;

                if (!keluhanUtama) errorMessages.push('Keluhan utama harus diisi');
                if (!gejala) errorMessages.push('Gejala harus diisi');
                if (!durasiGejala) errorMessages.push('Durasi gejala harus dipilih');
                if (!serviceDate) errorMessages.push('Tanggal konsultasi harus dipilih');
                if (!idDokter) errorMessages.push('Dokter harus dipilih');

                // Validate minimum text length
                if (keluhanUtama && keluhanUtama.length < 10) {
                    errorMessages.push('Keluhan utama minimal 10 karakter');
                }
                if (gejala && gejala.length < 10) {
                    errorMessages.push('Gejala minimal 10 karakter');
                }

                if (errorMessages.length > 0) {
                    showCustomConfirm('Mohon lengkapi data berikut:\n' + errorMessages.join('\n'), function (result) {
                        if (result) {
                            // User clicked "Ya, Lanjutkan" - they can continue filling the form
                            // Focus on the first error field
                            if (!keluhanUtama) {
                                form.querySelector('textarea[name="keluhan_utama"]').focus();
                            } else if (!gejala) {
                                form.querySelector('textarea[name="gejala"]').focus();
                            } else if (!durasiGejala) {
                                form.querySelector('select[name="durasi_gejala"]').focus();
                            } else if (!serviceDate) {
                                serviceDateInput.focus();
                            } else if (!idDokter) {
                                dokterSelect.focus();
                            }
                        }
                    });
                    return false;
                }

                return true;
            };
        });
    </script>
</body>

</html>