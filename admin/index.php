<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE id_admin = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Get statistics
$stats = [];

// Total pendapatan
$query = "SELECT COALESCE(SUM(total_harga), 0) as total FROM pesanan WHERE status_pembayaran = 'paid'";
$result = $conn->query($query);
$stats['pendapatan'] = $result->fetch_assoc()['total'];

// Total pesanan
$query = "SELECT COUNT(*) as total FROM pesanan";
$result = $conn->query($query);
$stats['pesanan'] = $result->fetch_assoc()['total'];

// Total perawatan
$query = "SELECT COUNT(*) as total FROM perawatan";
$result = $conn->query($query);
$stats['perawatan'] = $result->fetch_assoc()['total'];

// Total penitipan
$query = "SELECT COUNT(*) as total FROM penitipan";
$result = $conn->query($query);
$stats['penitipan'] = $result->fetch_assoc()['total'];

// Total konsultasi
$query = "SELECT COUNT(*) as total FROM konsultasi";
$result = $conn->query($query);
$stats['konsultasi'] = $result->fetch_assoc()['total'];

// Get recent orders
$query = "SELECT p.*, pl.nama_lengkap 
          FROM pesanan p 
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
          ORDER BY p.tanggal_pesanan DESC LIMIT 5";
$recent_orders = $conn->query($query);

// Get monthly revenue data for chart
$query = "SELECT 
            DATE_FORMAT(tanggal_pesanan, '%Y-%m') as month,
            SUM(total_harga) as total
          FROM pesanan 
          WHERE status_pembayaran = 'paid'
          GROUP BY DATE_FORMAT(tanggal_pesanan, '%Y-%m')
          ORDER BY month DESC
          LIMIT 12";
$revenue_data = $conn->query($query);
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        * {
            font-family: 'Poppins', sans-serif;
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

        .revenue-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .revenue-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: left;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
            position: relative;
        }

        .revenue-card .icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .revenue-card.pendapatan .icon {
            background-color: #fff2e6;
            color: #ff7f50;
        }

        .revenue-card.pesanan .icon {
            background-color: #fff2e6;
            color: #ff7f50;
        }

        .revenue-card.grooming .icon {
            background-color: #fff2e6;
            color: #ff7f50;
        }

        .revenue-card.penitipan .icon {
            background-color: #fff2e6;
            color: #ff7f50;
        }

        .revenue-card.konsultasi .icon {
            background-color: #fff2e6;
            color: #ff7f50;
        }

        .revenue-card h6 {
            font-size: 12px;
            color: #888;
            margin: 0 0 8px 0;
            font-weight: 500;
        }

        .revenue-card .amount {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
        }

        .revenue-card .percentage {
            font-size: 11px;
            color: #10b981;
            font-weight: 500;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h6 {
            font-weight: 600;
            margin: 0;
            color: #333;
            font-size: 16px;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chart-controls select {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
        }

        .orders-summary {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item h6 {
            font-size: 14px;
            color: #333;
            margin: 0;
            font-weight: 500;
        }

        .summary-item .value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .table-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }

        .table-card h6 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-card .see-all {
            font-size: 12px;
            color: #ff7f50;
            text-decoration: none;
            font-weight: 500;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table thead th {
            background-color: #f8f9fa;
            color: #666;
            font-weight: 500;
            font-size: 11px;
            text-transform: uppercase;
            padding: 12px 8px;
            text-align: left;
            border: none;
        }

        .custom-table tbody td {
            padding: 12px 8px;
            border-bottom: 1px solid #f5f5f5;
            color: #333;
            font-size: 13px;
        }

        .custom-table tbody tr:hover {
            background-color: #fafafa;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-image {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
        }

        .rating {
            color: #ffc107;
            font-size: 12px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #ff7f50;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .revenue-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .tables-grid {
                grid-template-columns: 1fr;
            }
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
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-home me-3"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dataproduk.php">
                        <i class="fas fa-box me-3"></i> Data Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="datapesanan.php">
                        <i class="fas fa-shopping-cart me-3"></i> Data Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="datapenitipan.php">
                        <i class="fas fa-paw me-3"></i> Data Penitipan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dataperawatan.php">
                        <i class="fas fa-cut me-3"></i> Data Perawatan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="datakonsultasi.php">
                        <i class="fas fa-stethoscope me-3"></i> Data Konsultasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="datadokter.php">
                        <i class="fas fa-user-md me-3"></i> Data Dokter
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="datapelanggan.php">
                        <i class="fas fa-users me-3"></i> Data Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dataanabul.php">
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h4 class="page-title">Dashboard</h4>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($admin['nama_lengkap'], 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($admin['nama_lengkap']); ?></span>
            </div>
        </div>

        <!-- Revenue Cards -->
        <div class="revenue-cards">
            <div class="revenue-card pendapatan">
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h6>Pendapatan</h6>
                <div class="amount">Rp <?php echo number_format($stats['pendapatan'], 0, ',', '.'); ?></div>
                <div class="percentage">↗ +2%</div>
            </div>

            <div class="revenue-card pesanan">
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h6>Pesanan Produk</h6>
                <div class="amount">Rp <?php echo number_format($stats['pesanan'], 0, ',', '.'); ?></div>
                <div class="percentage">↗ +2%</div>
            </div>

            <div class="revenue-card grooming">
                <div class="icon">
                    <i class="fas fa-cut"></i>
                </div>
                <h6>Grooming</h6>
                <div class="amount">Rp <?php echo number_format($stats['perawatan'], 0, ',', '.'); ?></div>
                <div class="percentage">↗ +5%</div>
            </div>

            <div class="revenue-card penitipan">
                <div class="icon">
                    <i class="fas fa-home"></i>
                </div>
                <h6>Penitipan</h6>
                <div class="amount">Rp <?php echo number_format($stats['penitipan'], 0, ',', '.'); ?></div>
                <div class="percentage">↗ +8%</div>
            </div>

            <div class="revenue-card konsultasi">
                <div class="icon">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <h6>Konsultasi</h6>
                <div class="amount">Rp <?php echo number_format($stats['konsultasi'], 0, ',', '.'); ?></div>
                <div class="percentage">↗ +12%</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Chart Card -->
            <div class="chart-card">
                <div class="chart-header">
                    <h6>Jumlah Pesanan</h6>
                    <div class="chart-controls">
                        <span style="font-size: 12px; color: #666;">Undian</span>
                        <select>
                            <option>Bulanan</option>
                            <option>Mingguan</option>
                            <option>Harian</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>

            <!-- Orders Summary -->
            <div class="orders-summary">
                <div class="summary-item">
                    <h6>Pesanan Baru</h6>
                    <div class="value"><?php echo $stats['pesanan']; ?></div>
                </div>
                <div class="summary-item">
                    <h6>Belum Diproses</h6>
                    <div class="value"><?php echo $stats['pesanan']; ?></div>
                </div>
                <div class="summary-item">
                    <h6>Pembayaran Tertunda</h6>
                    <div class="value"><?php echo $stats['pesanan']; ?></div>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="tables-grid">
            <!-- Produk Terlaris -->
            <div class="table-card">
                <h6>
                    Produk Terlaris
                    <a href="#" class="see-all">Lihat</a>
                </h6>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Terjual</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT p.nama_produk, p.foto_utama as gambar_produk, SUM(pp.quantity) as terjual, 
                                 SUM(pp.quantity * pp.harga_satuan) as pendapatan
                                 FROM pesanan_produk pp 
                                 JOIN produk p ON pp.id_produk = p.id_produk
                                 JOIN pesanan ps ON pp.id_pesanan = ps.id_pesanan
                                 WHERE ps.status_pesanan = 'completed'
                                 GROUP BY p.id_produk 
                                 ORDER BY terjual DESC LIMIT 5";
                        $result = $conn->query($query);
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>";
                            echo "<div class='product-info'>";
                            if (!empty($row['gambar_produk'])) {
                                echo "<img src='../assets/images/products/" . htmlspecialchars($row['gambar_produk']) . "' class='product-image' alt='" . htmlspecialchars($row['nama_produk']) . "'>";
                            } else {
                                echo "<div class='product-image'>📦</div>";
                            }
                            echo "<span>" . htmlspecialchars(substr($row['nama_produk'], 0, 20)) . "...</span>";
                            echo "</div>";
                            echo "</td>";
                            echo "<td>" . $row['terjual'] . "</td>";
                            echo "<td>Rp" . number_format($row['pendapatan'], 0, ',', '.') . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Kesan Pesan Pelanggan -->
            <div class="table-card">
                <h6>
                    Kesan Pesan Pelanggan
                    <a href="#" class="see-all">Lihat</a>
                </h6>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Rating</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Sample data untuk kesan pesan pelanggan
                        $reviews = [
                            ['nama' => 'Sumanto', 'tanggal' => '17/12/2025', 'rating' => 5],
                            ['nama' => 'Sumanto', 'tanggal' => '01/12/2025', 'rating' => 4],
                            ['nama' => 'Sumanto', 'tanggal' => '17/12/2025', 'rating' => 3],
                            ['nama' => 'Sumanto', 'tanggal' => '10/12/2025', 'rating' => 5],
                            ['nama' => 'Sumanto', 'tanggal' => '17/12/2025', 'rating' => 4]
                        ];

                        foreach ($reviews as $review) {
                            echo "<tr>";
                            echo "<td>" . $review['nama'] . "</td>";
                            echo "<td>" . $review['tanggal'] . "</td>";
                            echo "<td>";
                            echo "<div class='rating'>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $review['rating']) {
                                    echo "★";
                                } else {
                                    echo "☆";
                                }
                            }
                            echo "</div>";
                            echo "</td>";
                            echo "<td>";
                            echo "<i class='fas fa-eye' style='color: #ff7f50; cursor: pointer;'></i> ";
                            echo "<i class='fas fa-edit' style='color: #28a745; cursor: pointer; margin-left: 8px;'></i>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart.js configuration
        const ctx = document.getElementById('ordersChart').getContext('2d');
        const ordersChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: [45, 52, 38, 65, 42, 75, 48, 85, 65, 78, 90, 85],
                    borderColor: '#ff7f50',
                    backgroundColor: 'rgba(255, 127, 80, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ff7f50',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            color: '#888',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#888',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 6
                    }
                }
            }
        });
    </script>
</body>

</html>