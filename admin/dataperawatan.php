<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT pr.*, 
          pl.id_pesanan,
          p.nomor_pesanan,
          p.tanggal_pesanan,
          p.status_pembayaran,
          pel.nama_lengkap as nama_pelanggan,
          pel.email as email_pelanggan,
          pel.nomor_telepon as telepon_pelanggan,
          a.nama_hewan,
          a.spesies,
          a.ras,
          l.nama_layanan,
          l.kategori_layanan
          FROM perawatan pr
          JOIN pesanan_layanan pl ON pr.id_pesanan_layanan = pl.id_detail
          JOIN pesanan p ON pl.id_pesanan = p.id_pesanan
          JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
          JOIN anabul a ON pr.id_anabul = a.id_anabul
          JOIN layanan l ON pl.id_layanan = l.id_layanan
          ORDER BY pr.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Perawatan | Ling-Ling Pet Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="index.php">
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
                    <a class="nav-link active" href="dataperawatan.php">
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
            <h4>Data Perawatan</h4>
            <div class="header-actions">
                <a href="tambah.php?type=perawatan" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Perawatan
                </a>
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table id="perawatanTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Hewan</th>
                            <th>Layanan</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nomor_pesanan']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_perawatan'])); ?></td>
                                <td>
                                    <?php echo $row['nama_pelanggan']; ?><br>
                                    <small class="text-muted">
                                        <?php echo $row['email_pelanggan']; ?><br>
                                        <?php echo $row['telepon_pelanggan']; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo $row['nama_hewan']; ?><br>
                                    <small class="text-muted">
                                        <?php echo ucfirst($row['spesies']); ?>
                                        <?php echo $row['ras'] ? ' - ' . $row['ras'] : ''; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo $row['nama_layanan']; ?><br>
                                    <small class="text-muted"><?php echo ucfirst($row['kategori_layanan']); ?></small>
                                </td>
                                <td><?php echo ucfirst($row['paket_perawatan']); ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                    echo match (
                                    strtolower($row['status_pesanan'] ?? '')
                                    ) {
                                        'scheduled' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($row['status_pesanan'] ?? '-')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php
                                    echo match ($row['status_pembayaran']) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>">
                                        <?php echo ucfirst($row['status_pembayaran']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="ubah.php?type=perawatan&id=<?php echo $row['id_perawatan']; ?>"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?type=perawatan&id=<?php echo $row['id_perawatan']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#perawatanTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });
    </script>
</body>

</html>