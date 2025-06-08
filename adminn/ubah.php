<?php
require 'function.php';
$id = $_GET["id"];
$pdk = query("SELECT * FROM produk WHERE id=$id")[0];
if (isset($_POST["submit"])) {
    if (ubah($_POST) > 0) {
        echo "
            <script>
            alert('data berhasil dubah!');
            document.location.href='dataproduk.php';
            </script>
        ";
    } else {
        echo "
        <script>
        alert('data gagal dubah!');
        document.location.href='dataproduk.php';
        </script>
    ";
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                    <!-- hidenn ges -->
                                    <input type="hidden" name="id" value="<?= $pdk["id"] ?>">

                                    <!-- Upload Gambar -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Gambar</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control" name="gambar" style="background-color: #e0e0e0;">
                                            <img src="../assets/img/produk/<?= $pdk["image"] ?>" alt="" class="img-thumbnail mt-2" width="100">
                                        </div>

                                    <!-- Nama Produk -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Nama Produk</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="name" style="background-color: #e0e0e0;" required value="<?= $pdk["name"] ?>">
                                        </div>
                                    </div>

                                    <!-- Harga -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Harga</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="price" style="background-color: #e0e0e0;" required value="<?= $pdk["price"] ?>">
                                        </div>
                                    </div>

                                    <!-- Kategoriy -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Kategori</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <?php
                                            $jenisSaatIni = $pdk["category"];
                                            ?>
                                            <select class="form-control" name="category" style="background-color: #e0e0e0;" required>
                                                <option value="kucing" <?= ($jenisSaatIni == 'kucing') ? 'selected' : '';?>>Kucing</option>
                                                <option value="anjing" <?= ($jenisSaatIni == 'anjing') ? 'selected' : '';?>>Anjing</option>
                                                <option value="hamster" <?= ($jenisSaatIni == 'hamster') ? 'selected' : '';?>>Hamster</option>
                                                <option value="kelinci"<?= ($jenisSaatIni == 'kelinci') ? 'selected' : '';?>>Kelinci</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Stok -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Stok</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="stock" style="background-color: #e0e0e0;" required value="<?= $pdk["stock"] ?>">
                                        </div>
                                    </div>

                                    <!-- Dskripsi -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-3 text-start">Deskripsi</div>
                                        <div class="col-sm-1 text-end">:</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="description" style="background-color: #e0e0e0;" required value="<?= $pdk["description"] ?>">
                                        </div>
                                    </div>

                                    <!-- aks -->
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <a href="dataproduk.php" class="btn btn-secondary me-2">Batal</a>
                                            <button type="submit" name="submit" class="btn btn-warning">Ubah</button>
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
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus.php?id=' + id;
                }
            })
        }
    </script>
</body>

</html>