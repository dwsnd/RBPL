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
                        <a class="nav-link active" href="#"><i class="fa fa-chart-bar me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="dataproduk.php"><i class="fa fa-cube me-2"></i>Data Produk</a>
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
                    <h3>Dashboard</h3>
                    <input type="search" class="form-control w-25" placeholder="Search...">
                </div>

                <!-- card dueur dwe done -->
                <div class="container my-4">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                        <div class="col">
                            <div class="card text-center p-3">
                                <div class="text-orange mb-2"><i class="fa-solid fa-money-bill-wave fa-2x"></i></div>
                                <div>Pendapatan</div>
                                <h5 class="fw-bold">Rp 69,420,000</h5>
                                <small class="text-success fw-semibold">▲ +201</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center p-3">
                                <div class="text-orange mb-2"><i class="fa-solid fa-box fa-2x"></i></div>
                                <div>Pesanan Produk</div>
                                <h5 class="fw-bold">Rp 69,420,000</h5>
                                <small class="text-success fw-semibold">▲ +201</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center p-3">
                                <div class="text-orange mb-2"><i class="fa-solid fa-cut fa-2x"></i></div>
                                <div>Grooming</div>
                                <h5 class="fw-bold">Rp 69,420,000</h5>
                                <small class="text-success fw-semibold">▲ +201</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center p-3">
                                <div class="text-orange mb-2"><i class="fa-solid fa-house fa-2x"></i></div>
                                <div>Penitipan</div>
                                <h5 class="fw-bold">Rp 69,420,000</h5>
                                <small class="text-success fw-semibold">▲ +201</small>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center p-3">
                                <div class="text-orange mb-2"><i class="fa-solid fa-stethoscope fa-2x"></i></div>
                                <div>Konsultasi</div>
                                <h5 class="fw-bold">Rp 69,420,000</h5>
                                <small class="text-success fw-semibold">▲ +201</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card duwur end -->



                <!-- Grafik kro kolom sbeleh e -->
                <div class="container">
                    <div class="row">
                        <!-- Kolom kiri -->
                        <div class="col-3">
                            <!-- 1 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="card-text">Pesanan Baru</p>
                                    <h5 class="card-title">12</h5>
                                </div>
                            </div>

                            <!-- 2 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="card-text">Belum Diproses</p>
                                    <h5 class="card-title">5</h5>
                                </div>
                            </div>

                            <!-- 3 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="card-text">Pembayaran</p>
                                    <h5 class="card-title">4</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom kanan / grafik -->
                        <div class="col-9">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Jumlah Pesanan</h6>
                                    <canvas id="chartOrder"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Grafik kro kolom sbeleh e -->



                <!-- Tabel Produk Terlaris -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h6>Produk Terlaris</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Terjual</th>
                                        <th>Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Royal Canin 2kg</td>
                                        <td>199</td>
                                        <td>Rp 3.049.000</td>
                                    </tr>
                                    <!-- Tambah data lain -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Review Pelanggan -->
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h6>Kesan Pesan Pelanggan</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Rating</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Sumanto</td>
                                        <td>17/02/2025</td>
                                        <td>⭐⭐⭐⭐⭐</td>
                                        <td><a href="#">Lihat Detail</a></td>
                                    </tr>
                                    <!-- Tambah data lain -->
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
    <script>
        // Chart.js sing nggu duwur
        const ctx = document.getElementById('chartOrder');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: [30, 90, 35, 20, 15, 25, 50, 80, 40, 90, 95, 30],
                    borderColor: '#ff7f50',
                    backgroundColor: 'rgba(255,127,80,0.2)',
                    tension: 0.3,
                    fill: true
                }]
            }
        });
    </script>

</body>

</html>