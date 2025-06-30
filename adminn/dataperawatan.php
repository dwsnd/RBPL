<?php
require 'function.php';

$query = "SELECT 
            p.id_pesanan,
            pl.nama_lengkap AS nama_pelanggan,
            CONCAT(p.tanggal_layanan, ' ', p.waktu_layanan) AS waktu_penjadwalan,
            a.kategori_hewan,
            dl.nama_layanan,
            p.total_harga,
            p.tanggal_booking,
            p.status_pesanan
          FROM pesanan_layanan p
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          JOIN anabul a ON p.id_anabul = a.id_anabul
          JOIN detail_layanan dl ON p.jenis_layanan = dl.jenis_layanan
          ORDER BY p.tanggal_layanan DESC, p.waktu_layanan ASC";

$result = mysqli_query($conn, $query);

// Inisialisasi total
$total_dijadwalkan = 0;
$total_proses = 0;
$total_selesai = 0;

// Ambil tanggal hari ini
$today = date("Y-m-d");

// Hitung total berdasarkan kondisi tanggal_booking
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['tanggal_booking'] < $today) {
            $total_dijadwalkan++;
        } elseif ($row['tanggal_booking'] == $today) {
            $total_proses++;
        } else { // $row['tanggal_booking'] > $today
            $total_selesai++;
        }
        $data[] = $row; // Simpan data untuk ditampilkan ke tabel nanti
    }
} else {
    $data = []; // Biar aman kalau kosong
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
                        <a class="nav-link" href="dataproduk.php"><i class="fa fa-cube me-2"></i>Data Produk</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="datapesanan.php"><i class="fa fa-shopping-cart me-2"></i>Data Pesanan</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link active" href="#"><i class="fa fa-cut me-2"></i>Data Perawatan</a>
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
                    <h3>Data Perawatan</h3>
                    <input type="search" class="form-control w-25" placeholder="Search...">
                </div>
                <!-- Isi Kontent dsini ygy -->
                <div class="container">
                    <div class="row">
                        <div class="col-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Dijadwalkan</h5>
                                    <h3 class="text-warning fw-bold"><?= $total_dijadwalkan ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Dalam Proses</h5>
                                    <h3 class="text-primary fw-bold"><?= $total_proses ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Selesai</h5>
                                    <h3 class="text-success fw-bold"><?= $total_selesai ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="card p-4">
                            <h4 class="mb-4">Jadwal Grooming Hari Ini</h4>
                            <table class="table table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <?php
                                        $slots = ['11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
                                        foreach ($slots as $slot) {
                                            echo "<th>" . date('H:i', strtotime($slot)) . "</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        $today = date('Y-m-d');
                                        $query_jadwal = "SELECT waktu_layanan FROM pesanan_layanan WHERE tanggal_layanan = '$today'";
                                        $result_jadwal = mysqli_query($conn, $query_jadwal);

                                        $jadwal_terisi = [];
                                        if ($result_jadwal && mysqli_num_rows($result_jadwal) > 0) {
                                            while ($row_jadwal = mysqli_fetch_assoc($result_jadwal)) {
                                                $jadwal_terisi[] = $row_jadwal['waktu_layanan'];
                                            }
                                        }

                                        foreach ($slots as $slot) {
                                            if (in_array($slot, $jadwal_terisi)) {
                                                echo "<td><a href='#' class='btn btn-primary btn-sm'>Lihat</a></td>";
                                            } else {
                                                echo "<td><span class='text-muted'>Kosong</span></td>";
                                            }
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class="row">
                        <div class="card p-4">
                            <h4 class="mb-4">Daftar Perawatan Hari Ini</h4>
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Waktu Penjadwalan</th>
                                        <th>Kategori</th>
                                        <th>Layanan</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($data)) {
                                        foreach ($data as $row) {
                                            echo "<tr>";
                                            echo "<td>{$row['id_pesanan']}</td>";
                                            echo "<td>{$row['nama_pelanggan']}</td>";
                                            echo "<td>{$row['tanggal_booking']}</td>";
                                            echo "<td>{$row['kategori_hewan']}</td>";
                                            echo "<td>{$row['nama_layanan']}</td>";
                                            echo "<td>Rp" . number_format($row['total_harga'], 0, ',', '.') . ",-</td>";

                                            $today = date("Y-m-d");
                                            if ($row['tanggal_booking'] < $today) {
                                                $status_text = "Dijadwalkan";
                                                $status_color = "text-warning";
                                            } elseif ($row['tanggal_booking'] == $today) {
                                                $status_text = "Dalam Proses";
                                                $status_color = "text-primary";
                                            } else {
                                                $status_text = "Selesai";
                                                $status_color = "text-success";
                                            }
                                            echo "<td class='$status_color fw-bold'>$status_text</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>Belum ada data.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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