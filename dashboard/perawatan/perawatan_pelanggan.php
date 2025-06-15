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

// Fetch user data
$user_data = [];
$query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

// Fetch user's pets data
$pets_data = [];
$pets_query = "SELECT id_anabul, nama_hewan, kategori_hewan, karakteristik FROM anabul WHERE id_pelanggan = '$id_pelanggan'";
$pets_result = mysqli_query($conn, $pets_query);
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
    $pet_category = mysqli_real_escape_string($conn, $_POST['pet_category']);
    $pet_special = mysqli_real_escape_string($conn, $_POST['pet_special']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $service_date = mysqli_real_escape_string($conn, $_POST['service_date']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;

    // Calculate total price
    $harga_layanan = [
        'basic' => 150000,
        'mix' => 200000,
        'complete' => 250000
    ];
    $total_harga = $harga_layanan[$service_type];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        $id_anabul = null;

        // If existing pet is selected
        if ($id_anabul_existing && $id_anabul_existing !== 'new') {
            $id_anabul = $id_anabul_existing;
        } else {
            // Insert new pet data
            $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, kategori_hewan, karakteristik, created_at) 
                          VALUES ('$id_pelanggan', '$pet_name', '$pet_category', '$pet_special', NOW())";

            if (mysqli_query($conn, $insert_pet)) {
                $id_anabul = mysqli_insert_id($conn);
            } else {
                throw new Exception("Error inserting pet data: " . mysqli_error($conn));
            }
        }

        // Insert booking/order data
        $insert_order = "INSERT INTO pesanan_layanan 
                        (id_pelanggan, id_anabul, jenis_layanan, tanggal_layanan, waktu_layanan, 
                         total_harga, status_pesanan, created_at) 
                        VALUES 
                        ('$id_pelanggan', '$id_anabul', '$service_type', '$service_date', '$service_time', 
                         '$total_harga', 'pending', NOW())";

        if (mysqli_query($conn, $insert_order)) {
            $id_pesanan = mysqli_insert_id($conn);

            // Commit transaction
            mysqli_commit($conn);

            // Set success message
            $_SESSION['success_message'] = "Pesanan layanan grooming berhasil dibuat! ID Pesanan: " . $id_pesanan;

            // Reset form and redirect
            echo "<script>
                // Reset form data
                document.getElementById('bookingForm').reset();
                // Reset select colors
                document.querySelectorAll('select').forEach(select => {
                    select.style.color = '#888';
                });
                // Hide manual pet form if it was shown
                document.getElementById('manual-pet-form').style.display = 'none';
                // Remove selected class from pet options
                document.querySelectorAll('.pet-option').forEach(option => {
                    option.classList.remove('selected');
                });
                // Reset total price
                document.getElementById('total_price').value = 'Rp0';
                
                // Redirect after reset
                setTimeout(function() {
                    window.location.href = 'pesanan.php';
                }, 100);
            </script>";
            exit();
        } else {
            throw new Exception("Error inserting order data: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Booking Grooming - Ling-Ling Pet Shop</title>
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
                <a href="shop.php" class="btn btn-black text-base mt-2">Mulai Belanja</a>
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
                    <i class="fab fa-whatsapp text-2xl mr-2"></i>Saya Mau Booking Sekarang
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
                                                        data-category="<?php echo htmlspecialchars($pet['kategori_hewan']); ?>"
                                                        data-special="<?php echo htmlspecialchars($pet['karakteristik']); ?>">
                                                    <label class="form-check-label" for="pet_<?php echo $pet['id_anabul']; ?>">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-paw me-2 text-orange-500"></i>
                                                            <div>
                                                                <span
                                                                    class="font-semibold"><?php echo htmlspecialchars($pet['nama_hewan']); ?></span>
                                                                <span
                                                                    class="text-muted ms-2">(<?php echo htmlspecialchars($pet['kategori_hewan']); ?>)</span>
                                                                <?php if ($pet['karakteristik']): ?>
                                                                    <div class="text-muted small mt-1">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        <?php echo htmlspecialchars($pet['karakteristik']); ?>
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
                                        <label class="form-label">Ciri-Ciri Khusus (Opsional)</label>
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
                                        <option value="basic">Basic - Rp 150.000</option>
                                        <option value="mix">Mix - Rp 200.000</option>
                                        <option value="complete">Complete - Rp 250.000</option>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            const form = document.getElementById('bookingForm');
            const errorMessages = [];

            // Check pet selection
            const selectedPet = form.querySelector('input[name="id_anabul_existing"]:checked');
            if (!selectedPet) {
                errorMessages.push('Silakan pilih hewan peliharaan atau tambahkan hewan baru');
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
                alert('Mohon lengkapi data berikut:\n' + errorMessages.join('\n'));
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
                    const selectedService = this.value;
                    if (selectedService && hargaPerawatan[selectedService]) {
                        const total = hargaPerawatan[selectedService];
                        totalPrice.value = 'Rp ' + total.toLocaleString('id-ID');
                    } else {
                        totalPrice.value = 'Rp0';
                    }
                });
            }

            // Handle pet selection
            petRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Remove selected class from all options
                    document.querySelectorAll('.pet-option').forEach(option => {
                        option.classList.remove('selected');
                    });

                    // Add selected class to current option
                    this.closest('.pet-option').classList.add('selected');

                    if (this.value === 'new') {
                        // Show manual form
                        manualPetForm.style.display = 'block';
                        // Make fields required
                        petNameInput.required = true;
                        petCategorySelect.required = true;
                        // Clear fields
                        petNameInput.value = '';
                        petCategorySelect.value = '';
                        petSpecialInput.value = '';
                    } else {
                        // Hide manual form and populate with existing data
                        manualPetForm.style.display = 'none';
                        // Make fields not required
                        petNameInput.required = false;
                        petCategorySelect.required = false;

                        // Fill with selected pet data
                        petNameInput.value = this.dataset.name || '';
                        petCategorySelect.value = this.dataset.category || '';
                        petSpecialInput.value = this.dataset.special || '';

                        // Update select color
                        handleSelectColor(petCategorySelect);
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
        });
    </script>
</body>

</html>