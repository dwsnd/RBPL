<?php
require 'function.php';
// $produk = query("SELECT * FROM produk");

// search sko keyword
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = htmlspecialchars($_GET['keyword']);
    $produk = query("SELECT * FROM produk WHERE name LIKE '%$keyword%' OR category LIKE '%$keyword%'");
} else {
    $produk = query("SELECT * FROM produk");
}

// dinggo halaman jml data per halaman pahinatiomn
$jumlahDataPerHalaman = 8;
$jumlahData = count(query("SELECT * FROM produk"));
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
$halamanAktif = (isset($_GET["halaman"])) ? (int)$_GET["halaman"] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

// manut gpt
$produk = query("SELECT * FROM produk LIMIT $awalData, $jumlahDataPerHalaman");
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

        .pagination .page-item.active .page-link {
            background-color: #ffc107;
            /* kuning bootstrap */
            border-color: #ffc107;
            color: #fff;
        }

        .pagination .page-link {
            color: #ffc107;
        }

        .pagination .page-link:hover {
            background-color: #ffe08a;
            border-color: #ffc107;
            color: #212529;
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
                    <form action="" method="GET" class="d-flex">
                        <input type="text" name="keyword" class="form-control me-2 w-75" placeholder="Cari produk..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                        <button type="submit" class="btn btn-outline-primary me-2" title="Cari">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="dataproduk.php" class="btn btn-outline-secondary" title="Reset">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                    </form>
                </div>

                <!-- Tabel -->
                <div class="container">
                    <a href="tambah.php" class="btn btn-warning mb-3">Tambah Data</a>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produk as $row) : ?>
                                <tr>
                                    <td><?= $row['id']; ?></td>
                                    <td><img src="aset/produk/kucing<?= $row["image"]; ?>" width="100" alt="Gambar"></td>
                                    <td><?= $row['name']; ?></td>
                                    <td><?= $row['price']; ?></td>
                                    <td><?= $row['category']; ?></td>
                                    <td><?= $row['stock']; ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="ubah.php?id=<?= $row["id"]; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete('<?= $row['id']; ?>')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- hlmn -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($halamanAktif > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?halaman=<?= $halamanAktif - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
                                <li class="page-item <?= ($i == $halamanAktif) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?halaman=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($halamanAktif < $jumlahHalaman) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?halaman=<?= $halamanAktif + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
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