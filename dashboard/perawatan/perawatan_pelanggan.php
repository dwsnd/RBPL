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

if (isset($_GET['check_availability']) && isset($_GET['date'])) {
    header('Content-Type: application/json');
    $selected_date = mysqli_real_escape_string($conn, $_GET['date']);
    $available_slots = getAvailableTimeSlots($conn, $selected_date);
    echo json_encode($available_slots);
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch user data
$user_data = [];
$query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

// Fetch user's pets data
$pets_data = [];
$pets_query = "SELECT id_anabul, nama_hewan, spesies, ciri_khusus FROM anabul WHERE id_pelanggan = '$id_pelanggan' AND status = 'aktif'";
$pets_result = mysqli_query($conn, $pets_query);
if ($pets_result && mysqli_num_rows($pets_result) > 0) {
    while ($row = mysqli_fetch_assoc($pets_result)) {
        $pets_data[] = $row;
    }
}

// Fetch services data
$services_data = [];
$services_query = "SELECT id_layanan, nama_layanan, jenis_layanan, harga FROM layanan WHERE jenis_layanan = 'perawatan'";
$services_result = mysqli_query($conn, $services_query);
if ($services_result && mysqli_num_rows($services_result) > 0) {
    while ($row = mysqli_fetch_assoc($services_result)) {
        $services_data[] = $row;
    }
}

function getAvailableTimeSlots($conn, $selected_date = null)
{
    // Daftar semua slot waktu yang tersedia
    $all_time_slots = [
        'pagi' => '08:00 - 10:00',
        'siang' => '10:00 - 12:00',
        'sore' => '13:00 - 15:00',
        'sore-akhir' => '15:00 - 17:00'
    ];

    // Jika tidak ada tanggal yang dipilih, return semua slot
    if (!$selected_date) {
        return $all_time_slots;
    }

    // PERBAIKAN: Query langsung ke tabel perawatan
    $booked_query = "SELECT waktu_mulai, waktu_selesai 
                     FROM perawatan 
                     WHERE status_pesanan NOT IN ('cancelled', 'rejected', 'completed') 
                     AND tanggal_perawatan = '$selected_date'
                     AND status_pesanan IN ('scheduled', 'in_progress')";

    $booked_result = mysqli_query($conn, $booked_query);
    $booked_times = [];

    if ($booked_result && mysqli_num_rows($booked_result) > 0) {
        while ($row = mysqli_fetch_assoc($booked_result)) {
            // Konversi waktu database ke slot waktu
            $waktu_mulai = $row['waktu_mulai'];
            if ($waktu_mulai == '08:00:00')
                $booked_times[] = 'pagi';
            elseif ($waktu_mulai == '10:00:00')
                $booked_times[] = 'siang';
            elseif ($waktu_mulai == '13:00:00')
                $booked_times[] = 'sore';
            elseif ($waktu_mulai == '15:00:00')
                $booked_times[] = 'sore-akhir';
        }
    }

    // Filter slot waktu yang masih tersedia
    $available_slots = [];
    foreach ($all_time_slots as $key => $display_time) {
        if (!in_array($key, $booked_times)) {
            $available_slots[$key] = $display_time;
        }
    }

    return $available_slots;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $pet_species = mysqli_real_escape_string($conn, $_POST['pet_category']);
    $pet_special = mysqli_real_escape_string($conn, $_POST['pet_special']);
    $id_layanan = mysqli_real_escape_string($conn, $_POST['service_type']);
    $service_date = mysqli_real_escape_string($conn, $_POST['service_date']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;

    // Konversi service_time ke waktu database
    $waktu_mulai = '';
    $waktu_selesai = '';
    switch ($service_time) {
        case 'pagi':
            $waktu_mulai = '08:00:00';
            $waktu_selesai = '10:00:00';
            break;
        case 'siang':
            $waktu_mulai = '10:00:00';
            $waktu_selesai = '12:00:00';
            break;
        case 'sore':
            $waktu_mulai = '13:00:00';
            $waktu_selesai = '15:00:00';
            break;
        case 'sore-akhir':
            $waktu_mulai = '15:00:00';
            $waktu_selesai = '17:00:00';
            break;
    }

    // Cek ketersediaan waktu
    $check_availability = "SELECT COUNT(*) as total FROM perawatan 
                          WHERE tanggal_perawatan = '$service_date' 
                          AND waktu_mulai = '$waktu_mulai'
                          AND status_pesanan IN ('scheduled', 'in_progress')";

    $availability_result = mysqli_query($conn, $check_availability);
    $availability_data = mysqli_fetch_assoc($availability_result);

    if ($availability_data['total'] > 0) {
        $error_message = "Maaf, waktu yang dipilih sudah tidak tersedia. Silakan pilih waktu lain.";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            $id_anabul = null;

            // Handle pet data
            if ($id_anabul_existing && $id_anabul_existing !== 'new') {
                $id_anabul = $id_anabul_existing;
            } else {
                // Insert new pet data
                $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, spesies, ciri_khusus, created_at) 
                              VALUES ('$id_pelanggan', '$pet_name', '$pet_species', '$pet_special', NOW())";

                if (mysqli_query($conn, $insert_pet)) {
                    $id_anabul = mysqli_insert_id($conn);
                } else {
                    throw new Exception("Error inserting pet data: " . mysqli_error($conn));
                }
            }

            // Get service price from database
            $price_query = "SELECT harga FROM layanan WHERE id_layanan = '$id_layanan'";
            $price_result = mysqli_query($conn, $price_query);
            if (!$price_result) {
                throw new Exception("Error getting service price: " . mysqli_error($conn));
            }
            $service_data = mysqli_fetch_assoc($price_result);
            if (!$service_data) {
                throw new Exception("Service not found");
            }
            $total_harga = $service_data['harga'];

            // Insert ke tabel pesanan terlebih dahulu
            $insert_pesanan = "INSERT INTO pesanan 
                              (nomor_pesanan, id_pelanggan, jenis_pesanan, total_harga, 
                               metode_pembayaran, status_pembayaran, status_pesanan, 
                               catatan_pelanggan, catatan_admin, created_at, updated_at) 
                              VALUES 
                              (CONCAT('PES-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 9999), 4, '0')), 
                               '$id_pelanggan', 'perawatan', '$total_harga', 'cash', 'pending', 'pending', 
                               'Booking layanan grooming', '', NOW(), NOW())";

            if (mysqli_query($conn, $insert_pesanan)) {
                $id_pesanan = mysqli_insert_id($conn);

                // Insert ke tabel pesanan_layanan
                $insert_layanan = "INSERT INTO pesanan_layanan 
                                  (id_pesanan, id_layanan, id_anabul, harga_layanan) 
                                  VALUES 
                                  ('$id_pesanan', '$id_layanan', '$id_anabul', '$total_harga')";

                if (mysqli_query($conn, $insert_layanan)) {
                    $id_pesanan_layanan = mysqli_insert_id($conn);

                    // Kemudian insert ke tabel perawatan
                    $insert_perawatan = "INSERT INTO perawatan 
                                        (id_pesanan_layanan, paket_perawatan, id_anabul, tanggal_perawatan, 
                                         waktu_mulai, waktu_selesai, petugas, kondisi_awal, 
                                         kondisi_akhir, catatan_perawatan, foto_sebelum, foto_sesudah, 
                                         status_pesanan, created_at, updated_at) 
                                        VALUES 
                                        ('$id_pesanan_layanan', '$id_layanan', '$id_anabul', '$service_date', 
                                         '$waktu_mulai', '$waktu_selesai', '', '', '', 
                                         'Booking layanan grooming - Waktu: $service_time', '', '', 
                                         'scheduled', NOW(), NOW())";

                    if (mysqli_query($conn, $insert_perawatan)) {
                        $id_perawatan = mysqli_insert_id($conn);

                        // Commit transaction
                        mysqli_commit($conn);

                        // Set success message
                        $_SESSION['success_message'] = "Pesanan layanan grooming berhasil dibuat! ID Perawatan: " . $id_perawatan;

                        // Redirect immediately without form reset
                        header('Location: ../../profilpelanggan/pesanan/index_pesanan.php');
                        exit();
                    } else {
                        throw new Exception("Error inserting perawatan data: " . mysqli_error($conn));
                    }
                } else {
                    throw new Exception("Error inserting layanan data: " . mysqli_error($conn));
                }
            } else {
                throw new Exception("Error inserting main order: " . mysqli_error($conn));
            }

        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error_message = $e->getMessage();
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
        input:focus {
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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Grooming Bukan Sekadar
                    <br>Mandi Ini Biar Mereka
                    <br>Glow Up Total!
                </h1>
                <a href="#booking-form" class="btn btn-black text-base mt-2">Mulai Perawatan Hewan</a>
            </div>
        </div>
        <img src="../../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- masalah anabul -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-center font-bold text-2xl mb-8">Apakah Anabul Kesayanganmu Mengalami Masalah Ini?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $masalah = [
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Kulitnya bermasalah dengan bakteri dan jamur yang bikin tidak nyaman?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Kutu nakal muncul dan membuatnya terus menggaruk dengan gelisah?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Bulu rontok, kusut, atau terlihat tidak terawat seperti kehilangan kilaunya?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Ada kotoran membandel di area mata dan telinga yang sulit dibersihkan?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Bau kurang sedap yang mulai mengganggu pelukan hangatmu dengannya?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Takut salah memberikan perawatan yang tepat untuk kesehatan si kecil?'
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

    <!-- info perawatan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-5/12 mb-8 lg:mb-0">
                    <img src="../../aset/anjingperawatan.png" alt="Grooming Illustration" class="w-full">
                </div>
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-3">Segera Lakukan Grooming Pada Anabul Kesayangan!
                    </h2>
                    <p class="mb-2 text-base">Grooming rutin sangat penting untuk menjaga kesehatan dan kenyamanan si
                        kecil kesayangan Anda. Namun, kami memahami beberapa kendala yang mungkin
                        <br>Anda hadapi:
                    </p>

                    <?php
                    $kendala = [
                        ['icon' => 'exclamation-triangle', 'text' => 'Tidak memahami teknik grooming yang tepat dan aman untuk Anabul Anda'],
                        ['icon' => 'clock', 'text' => 'Kesibukan yang membuat Anda tidak punya waktu cukup untuk melakukan grooming sendiri'],
                        ['icon' => 'question-circle', 'text' => 'Bingung memilih produk perawatan yang aman dan sesuai untuk kondisi <br>Anabul Anda']
                    ];

                    foreach ($kendala as $item) {
                        echo '<div class="flex items-start">
                                <div class="mr-4 items-center justify-center text-lg">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="mb-2 text-base leading-relaxed">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                    <a href="https://wa.me/6288217723999"
                        class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-orange-600 transition duration-200">
                        <i class="fab fa-whatsapp text-2xl mr-2"></i>Hubungi Kami
                    </a>
                    <span class="ml-4 items-center justify-center text-base fw-semibold">Konsultasi permasalahan
                        anabulmu sekarang!</span>
                </div>
            </div>
        </div>
    </section>

    <!-- bagian perawatan -->
    <section class="py-12 px-16">
        <div class="container mx-auto px-4 py-10 bg-orange-100 rounded-3xl">
            <h2 class="text-center font-bold text-2xl mb-10">Ada Perawatan Apa Aja Kalau Kamu Grooming di Ling-Ling
                Petshop</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-6">
                <?php
                $services = [
                    [
                        'icon' => 'database',
                        'title' => 'Basic',
                        'description' => 'Layanan dasar yang mencakup grooming anti kutu & jamur, pemeriksaan kesehatan, serta pemberian obat cacing untuk menjaga kebersihan dan kesehatan hewan peliharaan Anda'
                    ],
                    [
                        'icon' => 'band-aid',
                        'title' => 'Mix',
                        'description' => 'Paket perawatan lengkap dengan grooming basic ditambah suntik vitamin untuk meningkatkan daya tahan tubuh dan vitalitas hewan kesayangan Anda'
                    ],
                    [
                        'icon' => 'trophy',
                        'title' => 'Complete',
                        'description' => 'Layanan premium mencakup grooming mix ditambah tes Revolution untuk deteksi dan perlindungan menyeluruh dari parasit sebagai solusi total bagi kesehatan hewan peliharaan Anda'
                    ]
                ];

                foreach ($services as $service) {
                    echo '<div class="text-center px-4">
                            <div class="text-5xl text-orange-500 mb-3">
                                <i class="fas fa-' . $service['icon'] . '"></i>
                            </div>
                            <h3 class="font-bold text-lg mb-3">' . $service['title'] . '</h3>
                            <p class="mb-0 text-sm text-justify">' . $service['description'] . '</p>
                          </div>';
                }
                ?>
            </div>

            <div class="text-center mt-8 mb-8 rounded-4xl">
                <p class="font-bold mb-4 text-sm">Benefit: Semua jenis grooming sudah termasuk gunting kuku, pembersihan
                    telinga, rapihkan bulu paw, dan bagian bokong</p>
                <a href="#booking-form"
                    class="inline-flex items-center justify-center bg-orange-500 text-sm text-white px-4 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-scissors text-2xl mr-2"></i>Saya Mau Booking Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- bagian form booking -->
    <section id="booking-form" class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-md border border-grey">
                <div class="bg-orange-500 text-white p-6 text-center">
                    <h3 class="text-lg font-semibold mb-2">Form Layanan Grooming</h3>
                    <p class="text-sm">Layanan perawatan untuk hewan kesayangan Anda</p>
                </div>

                <div class="p-8">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
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
                                        <input type="text" name="pet_name" id="pet_name" class="form-control"
                                            placeholder="Masukkan nama hewan peliharaan" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Hewan</label>
                                        <select name="pet_category" id="pet_category" class="form-control" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                            <option value="">Pilih kategori hewan</option>
                                            <option value="kucing">Kucing</option>
                                            <option value="anjing">Anjing</option>
                                            <option value="kelinci">Kelinci</option>
                                            <option value="hamster">Hamster</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ciri Khusus (Opsional)</label>
                                        <input type="text" name="pet_special" id="pet_special" class="form-control"
                                            placeholder="Contoh: Warna bulu, ukuran, atau kondisi khusus">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Layanan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-scissors me-2"></i>Detail Layanan
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Layanan Perawatan</label>
                                    <select name="service_type" id="service_type" class="form-control" required>
                                        <option value="">Pilih layanan grooming</option>
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
                                    <label class="form-label">Tanggal Perawatan</label>
                                    <input type="date" name="service_date" class="form-control"
                                        min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Perawatan</label>
                                    <select name="service_time" id="service_time" class="form-control" required>
                                        <option value="">Pilih waktu grooming</option>
                                        <option value="pagi">08:00 - 10:00</option>
                                        <option value="siang">10:00 - 12:00</option>
                                        <option value="sore">13:00 - 15:00</option>
                                        <option value="sore-akhir">15:00 - 17:00</option>
                                    </select>
                                    <small class="text-muted">Waktu yang sudah dibooking akan dinonaktifkan</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total Harga</label>
                                    <input type="text" id="total_price" class="form-control" placeholder="Rp0" readonly>
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

        // Add this at the beginning of your script
        window.onload = function () {
            // Reset form when page loads
            const form = document.getElementById('bookingForm');
            if (form) {
                form.reset();
                // Reset total price display
                document.getElementById('total_price').value = 'Rp0';
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
                        document.getElementById('pet_category').required = true;
                    }
                });
                return false;
            } else if (selectedPet.value === 'new') {
                const petName = form.querySelector('#pet_name').value.trim();
                const petCategory = form.querySelector('#pet_category').value;
                if (!petName) errorMessages.push('Nama hewan peliharaan harus diisi');
                if (!petCategory) errorMessages.push('Kategori hewan harus dipilih');
            }

            // Check service details
            const serviceType = form.querySelector('select[name="service_type"]').value;
            const serviceDate = form.querySelector('input[name="service_date"]').value;
            const serviceTime = form.querySelector('select[name="service_time"]').value;

            if (!serviceType) errorMessages.push('Jenis layanan harus dipilih');
            if (!serviceDate) errorMessages.push('Tanggal perawatan harus dipilih');
            if (!serviceTime) errorMessages.push('Waktu perawatan harus dipilih');

            if (errorMessages.length > 0) {
                showCustomConfirm('Mohon lengkapi data berikut:\n' + errorMessages.join('\n'), function (result) {
                    if (result) {
                        // User clicked "Ya, Lanjutkan" - they can continue filling the form
                        // Focus on the first error field
                        if (!serviceType) {
                            form.querySelector('select[name="service_type"]').focus();
                        } else if (!serviceDate) {
                            form.querySelector('input[name="service_date"]').focus();
                        } else if (!serviceTime) {
                            form.querySelector('select[name="service_time"]').focus();
                        }
                    }
                });
                return false;
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('bookingForm');
            const serviceType = form.querySelector('select[name="service_type"]');
            const totalPrice = form.querySelector('#total_price');
            const petRadios = form.querySelectorAll('input[name="id_anabul_existing"]');
            const manualPetForm = document.getElementById('manual-pet-form');
            const petNameInput = document.getElementById('pet_name');
            const petCategorySelect = document.getElementById('pet_category');
            const petSpecialInput = document.getElementById('pet_special');
            const submitBtn = document.getElementById('submitBtn');
            const serviceDate = document.querySelector('input[name="service_date"]');
            const serviceTime = document.querySelector('#service_time');

            // Reset form when page loads
            form.reset();
            totalPrice.value = 'Rp0';
            manualPetForm.style.display = 'none';
            document.querySelectorAll('.pet-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelectorAll('select').forEach(select => {
                select.style.color = '#888';
            });

            // Add required field indicators
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                const label = field.previousElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.innerHTML += ' <span class="text-danger">*</span>';
                }
            });

            // Function to handle select color changes
            function handleSelectColor(select) {
                if (select) {
                    select.style.color = select.value ? '#000' : '#888';
                }
            }

            // Add event listeners for all select elements
            const selects = [serviceType, petCategorySelect, form.querySelector('select[name="service_time"]')];
            selects.forEach(select => {
                if (select) {
                    select.addEventListener('change', function () {
                        handleSelectColor(this);
                    });
                    handleSelectColor(select);
                }
            });

            // Update total price when service type changes
            if (serviceType) {
                serviceType.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.harga) {
                        const price = parseInt(selectedOption.dataset.harga);
                        totalPrice.value = 'Rp ' + price.toLocaleString('id-ID');
                    } else {
                        totalPrice.value = 'Rp0';
                    }
                });
            }

            // Pet selection handlers
            petRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Remove selected class from all options
                    document.querySelectorAll('.pet-option').forEach(option => {
                        option.classList.remove('selected');
                    });

                    // Add selected class to current option
                    this.closest('.pet-option').classList.add('selected');

                    if (this.value === 'new') {
                        manualPetForm.style.display = 'block';
                        // Make fields required
                        petNameInput.required = true;
                        petCategorySelect.required = true;
                        // Clear fields
                        petNameInput.value = '';
                        petCategorySelect.value = '';
                        petSpecialInput.value = '';
                    } else {
                        manualPetForm.style.display = 'none';
                        // Remove required attribute
                        petNameInput.required = false;
                        petCategorySelect.required = false;
                    }
                });
            });

            // If no pets exist, show manual form by default
            <?php if (empty($pets_data)): ?>
                if (manualPetForm) {
                    manualPetForm.style.display = 'block';
                }
            <?php endif; ?>

            // Add input validation styles
            form.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('invalid', function (e) {
                    e.preventDefault();
                    this.classList.add('is-invalid');
                });

                input.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Event listener untuk perubahan tanggal
            if (serviceDate) {
                serviceDate.addEventListener('change', function () {
                    // Sementara dinonaktifkan
                    /*
                    updateTimeAvailability(this.value);
                    */
                });
            }

            // Validasi tambahan sebelum submit
            form.addEventListener('submit', function (e) {
                const selectedDate = serviceDate.value;
                const selectedTime = serviceTime.value;

                if (selectedDate && selectedTime) {
                    // Sementara dinonaktifkan
                    /*
                    // Double-check availability sebelum submit
                    fetch(`?check_availability=1&date=${selectedDate}`)
                        .then(response => response.json())
                        .then(availableSlots => {
                            if (!availableSlots.hasOwnProperty(selectedTime)) {
                                e.preventDefault();
                                alert('Maaf, waktu yang dipilih sudah tidak tersedia. Silakan refresh halaman dan pilih waktu lain.');
                                return false;
                            }
                        })
                        .catch(error => {
                            console.error('Error checking availability:', error);
                        });
                    */
                }
            });
        });
    </script>
</body>

</html>