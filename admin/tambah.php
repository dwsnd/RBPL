<?php

require 'function.php';

// Get the type from URL parameter
$type = isset($_GET['type']) ? $_GET['type'] : 'produk';

if (isset($_POST["submit"])) {
    // Combine POST and FILES data only if the type requires foto_utama
    $data = $_POST;
    if (in_array($type, ['produk', 'dokter', 'anabul'])) {
        $data = array_merge($_POST, ['foto_utama' => $_FILES['foto_utama']]);
    }

    if (tambah($data, $type) > 0) {
        echo "
            <script>
            alert('Data berhasil ditambahkan!');
            document.location.href='data{$type}.php';
            </script>
        ";
    } else {
        echo "
            <script>
            alert('Data gagal ditambahkan!');
            document.location.href='data{$type}.php';
            </script>
        ";
    }
}

// Get page title based on type
$pageTitle = ucfirst($type);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah <?php echo $pageTitle; ?> | Ling-Ling Pet Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: #6c757d !important;
            text-decoration: none !important;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #495057 !important;
        }

        .sidebar .nav-link.active {
            background-color: #ff7f50 !important;
            color: #fff !important;
        }

        .sidebar .text-danger {
            color: #dc3545 !important;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .header {
            background: #fff;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .upload-area {
            border-radius: 8px;
            background-color: #d9d9d9;
            padding: 30px;
            text-align: center;
            cursor: pointer;
        }

        .upload-area i {
            font-size: 40px;
            color: #555;
            margin-bottom: 10px;
        }

        .upload-area p {
            margin: 5px 0;
        }

        .custom-file-input {
            display: none;
        }

        .btn-upload {
            border: 1px solid #ff6600;
            color: #ff6600;
            background: none;
            padding: 5px 20px;
            border-radius: 4px;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-upload:hover {
            background: #ff6600;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1 class="brand-title">
            <i class="fas fa-paw"></i> PetShop Admin
        </h1>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home me-3"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'produk' ? 'active' : ''; ?>" href="dataproduk.php">
                        <i class="fas fa-box me-3"></i> Data Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'pesanan' ? 'active' : ''; ?>" href="datapesanan.php">
                        <i class="fas fa-shopping-cart me-3"></i> Data Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'penitipan' ? 'active' : ''; ?>" href="datapenitipan.php">
                        <i class="fas fa-paw me-3"></i> Data Penitipan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'perawatan' ? 'active' : ''; ?>" href="dataperawatan.php">
                        <i class="fas fa-cut me-3"></i> Data Perawatan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'konsultasi' ? 'active' : ''; ?>" href="datakonsultasi.php">
                        <i class="fas fa-stethoscope me-3"></i> Data Konsultasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'dokter' ? 'active' : ''; ?>" href="datadokter.php">
                        <i class="fas fa-user-md me-3"></i> Data Dokter
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'pelanggan' ? 'active' : ''; ?>" href="datapelanggan.php">
                        <i class="fas fa-users me-3"></i> Data Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'anabul' ? 'active' : ''; ?>" href="dataanabul.php">
                        <i class="fas fa-heart me-3"></i> Data Anabul
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-3"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="header">
                <h4 class="page-title">Tambah <?php echo $pageTitle; ?></h4>
            </div>

            <div class="container mt-2">
                <div class="row">
                    <div class="col">
                        <div class="card p-4">
                            <form action="" method="post" enctype="multipart/form-data">
                                <?php
                                // Dynamic form fields based on type
                                switch ($type) {
                                    case 'produk':
                                        include 'forms/tambah_produk_form.php';
                                        break;
                                    case 'dokter':
                                        include 'forms/tambah_dokter_form.php';
                                        break;
                                    case 'pelanggan':
                                        include 'forms/tambah_pelanggan_form.php';
                                        break;
                                    case 'anabul':
                                        include 'forms/tambah_anabul_form.php';
                                        break;
                                    case 'pesanan':
                                        include 'forms/tambah_pesanan_form.php';
                                        break;
                                    case 'penitipan':
                                        include 'forms/tambah_penitipan_form.php';
                                        break;
                                    case 'perawatan':
                                        include 'forms/tambah_perawatan_form.php';
                                        break;
                                    case 'konsultasi':
                                        include 'forms/tambah_konsultasi_form.php';
                                        break;
                                }
                                ?>

                                <!-- Tombol Aksi -->
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end mt-4">
                                        <a href="data<?php echo $type; ?>.php" class="btn btn-secondary me-2">Batal</a>
                                        <button type="submit" name="submit" class="btn btn-warning">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>