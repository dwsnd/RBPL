<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get dokter data with additional information
$query = "SELECT d.*,
          (SELECT COUNT(*) FROM konsultasi WHERE id_dokter = d.id_dokter) as jumlah_konsultasi,
          (SELECT COUNT(*) FROM jadwal_dokter WHERE id_dokter = d.id_dokter AND status = 'aktif') as jumlah_jadwal
          FROM dokter_hewan d
          ORDER BY d.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Dokter | Ling-Ling Pet Shop</title>
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

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
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

        .data-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }

        .data-card h6 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .btn-view {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .btn-edit {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
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
                    <a class="nav-link active" href="datadokter.php">
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

    <div class="main-content">
        <div class="header">
            <h4>Data Dokter</h4>
            <div class="header-actions">
                <a href="tambah.php?type=dokter" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Dokter
                </a>
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table id="dokterTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Dokter</th>
                            <th>Lisensi</th>
                            <th>Spesialisasi</th>
                            <th>Kontak</th>
                            <th>Tarif</th>
                            <th>Jadwal</th>
                            <th>Status</th>
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
                                <td>
                                    <?php if ($row['foto_dokter']): ?>
                                        <img src="../assets/img/dokter/<?php echo $row['foto_dokter']; ?>"
                                            alt="<?php echo $row['nama_dokter']; ?>" class="img-thumbnail"
                                            style="max-width: 50px;">
                                    <?php else: ?>
                                        <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $row['nama_dokter']; ?><br>
                                    <small class="text-muted">
                                        <?php echo $row['pengalaman_tahun']; ?> tahun pengalaman
                                    </small>
                                </td>
                                <td><?php echo $row['nomor_lisensi']; ?></td>
                                <td><?php echo $row['spesialisasi']; ?></td>
                                <td>
                                    <?php echo $row['email']; ?><br>
                                    <small class="text-muted"><?php echo $row['nomor_telepon']; ?></small>
                                </td>
                                <td>
                                    Rp <?php echo number_format($row['tarif_konsultasi'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $row['jumlah_jadwal']; ?> jadwal aktif</span><br>
                                    <small class="text-muted"><?php echo $row['jumlah_konsultasi']; ?> konsultasi</small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php
                                    echo match ($row['status']) {
                                        'aktif' => 'success',
                                        'nonaktif' => 'danger',
                                        'cuti' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail.php?type=dokter&id=<?php echo $row['id_dokter']; ?>"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="ubah.php?type=dokter&id=<?php echo $row['id_dokter']; ?>"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?type=dokter&id=<?php echo $row['id_dokter']; ?>"
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
            $('#dokterTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });
    </script>
</body>

</html>