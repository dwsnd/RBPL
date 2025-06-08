<?php

require 'function.php';
if (isset($_POST["submit"])) {
    if (tambah($_POST) > 0) {
        echo "
            <script>
            alert('data berhasil ditambahkan!');
            document.location.href='dataproduk.php';
            </script>
        ";
    } else {
        echo "gagal";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Ling-Ling Pet Shop</title>
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

        .sidebar {
            height: 100vh;
            background-color: #fff;
            border-right: 1px solid #ddd;
        }

        .sidebar .nav-link {
            color: #6c757d !important;
            text-decoration: none !important;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #495057 !important;
        }


        .sidebar a:link,
        .sidebar a:visited,
        .sidebar a:not(.active) {
            color: #6c757d !important;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link.active:link,
        .sidebar .nav-link.active:visited {
            background-color: #ff7f50 !important;
            color: #fff !important;
        }

        /* logout red */
        .sidebar .text-danger,
        .sidebar .text-danger:link,
        .sidebar .text-danger:visited {
            color: #dc3545 !important;
        }

        .sidebar .nav-link.active {
            background-color: #ff7f50;
            color: #fff !important;
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

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-3">
                <h5 class="text-center fw-bold mb-4">🐾 Ling-Ling Pet Shop</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="admin.php"><i class="fa fa-chart-bar me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link active" href="dataproduk.php"><i class="fa fa-cube me-2"></i>Data Produk</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="datapesanan.php"><i class="fa fa-shopping-cart me-2"></i>Data Pesanan</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="dataperawatan.php"><i class="fa fa-cut me-2"></i>Data Perawatan</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="datapenitipan.php"><i class="fa fa-home me-2"></i>Penitipan Hewan</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="jadwal.php"><i class="fa fa-calendar me-2"></i>Jadwal Konsultasi</a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="login.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-10 p-4">
                <div class="d-flex justify-content-between mb-3">
                    <h3>Data Produk</h3>
                    <input type="search" class="form-control w-25" placeholder="Search...">
                </div>

                <div class="container mt-4">
                    <div class="row">
                        <div class="col">
                            <div class="card p-4">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <!-- Upload Gambar -->
                                    <div class="text-center mb-4" onclick="document.getElementById('fileInput').click()" style="border: 2px dashed #ccc; padding: 30px; cursor: pointer; border-radius: 10px;">
                                        <i class="bi bi-cloud-upload" style="font-size: 40px; color: #666;"></i>
                                        <p style="margin: 10px 0;"><strong>Pilih file atau tarik ke sini untuk unggah</strong></p>
                                        <p style="font-size: 12px; color: gray;">Unggah file dalam format JPG atau PNG, maksimal 10MB</p>
                                        <label class="btn btn-warning" style="cursor:pointer;">Pilih File</label>
                                        <input type="file" class="d-none" id="fileInput" name="images" required>
                                    </div>

                                    <!-- Nama Produk -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Nama Produk</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="name" placeholder="Masukkan nama produk" style="background-color: #e0e0e0;" required>
                                        </div>
                                    </div>

                                    <!-- Harga -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Harga</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="price" placeholder="Masukkan harga" style="background-color: #e0e0e0;" required>
                                        </div>
                                    </div>

                                    <!-- Kategori -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Kategori</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="category" style="background-color: #e0e0e0;" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                <option value="kucing">Kucing</option>
                                                <option value="anjing">Anjing</option>
                                                <option value="hamster">Hamster</option>
                                                <option value="kelinci">Kelinci</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Stok -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Stok</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="stock" placeholder="Masukkan jumlah stok" style="background-color: #e0e0e0;" required>
                                        </div>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Deskripsi</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="description" placeholder="Masukkan deskripsi" style="background-color: #e0e0e0;" required>
                                        </div>
                                    </div>

                                    <!-- Tombol Aksi -->
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <a href="dataproduk.php" class="btn btn-secondary me-2">Batal</a>
                                            <button type="submit" name="submit" class="btn btn-warning">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>