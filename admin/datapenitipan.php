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

$query = "SELECT 
            pen.id_penitipan,
            pen.tanggal_checkin,
            pen.tanggal_checkout,
            pen.jumlah_hari,
            pen.nomor_kandang,
            pen.status_checkin,
            pen.kontak_darurat,
            pen.catatan_checkin,
            pen.catatan_checkout,
            pen.waktu_checkin,
            pen.waktu_checkout,
            pen.created_at,
            pen.updated_at,
            COALESCE(pl.nama_lengkap, 'Data tidak tersedia') as nama_pelanggan,
            COALESCE(a.nama_hewan, 'Data tidak tersedia') as nama_hewan,
            COALESCE(ps.total_harga, 0) as total_harga,
            COALESCE(ps.status_pesanan, 'pending') as status_penitipan
          FROM penitipan pen
          LEFT JOIN pesanan ps ON pen.id_pesanan = ps.id_pesanan
          LEFT JOIN pelanggan pl ON ps.id_pelanggan = pl.id_pelanggan 
          LEFT JOIN anabul a ON pen.id_anabul = a.id_anabul 
          ORDER BY pen.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    echo "<div class='alert alert-danger'>Query Error: " . $conn->error . "</div>";
    // Coba query yang lebih sederhana untuk debugging
    $simple_query = "SELECT * FROM penitipan ORDER BY created_at DESC";
    $simple_result = $conn->query($simple_query);

    if ($simple_result) {
        echo "<div class='alert alert-info'>Data penitipan ditemukan: " . $simple_result->num_rows . " baris</div>";
        echo "<h5>Data Raw dari tabel penitipan:</h5>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-sm'>";
        echo "<thead><tr>";
        // Tampilkan header kolom
        $fields = $simple_result->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr></thead><tbody>";

        // Tampilkan data
        while ($row = $simple_result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    }
} else {
    $row_count = $result->num_rows;

    if ($row_count == 0) {
        // Cek apakah ada data di tabel penitipan
        $check_query = "SELECT COUNT(*) as total FROM penitipan";
        $check_result = $conn->query($check_query);
        $check_data = $check_result->fetch_assoc();

        echo "<div class='alert alert-warning'>Total data di tabel penitipan: " . $check_data['total'] . "</div>";

        if ($check_data['total'] > 0) {
            echo "<div class='alert alert-info'>Data ada di tabel penitipan, tapi JOIN dengan tabel lain gagal. Mencoba query tanpa JOIN...</div>";

            // Query tanpa JOIN untuk melihat data mentah
            $no_join_query = "SELECT 
                                id_penitipan,
                                id_pesanan,
                                id_anabul,
                                tanggal_checkin,
                                tanggal_checkout,
                                jumlah_hari,
                                nomor_kandang,
                                status_checkin,
                                kontak_darurat
                              FROM penitipan 
                              ORDER BY created_at DESC";
            $no_join_result = $conn->query($no_join_query);

            if ($no_join_result && $no_join_result->num_rows > 0) {
                echo "<h5>Data Penitipan (tanpa JOIN):</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped'>";
                echo "<thead><tr>";
                echo "<th>ID</th><th>ID Pesanan</th><th>ID Anabul</th><th>Check-in</th><th>Check-out</th><th>Hari</th><th>Kandang</th><th>Status</th><th>Kontak</th>";
                echo "</tr></thead><tbody>";

                while ($row = $no_join_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id_penitipan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['id_pesanan'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['id_anabul'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_checkin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_checkout']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['jumlah_hari']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomor_kandang'] ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row['status_checkin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kontak_darurat']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            }
        }
    }
}

function labelStatusPenitipan($status)
{
    $map = [
        'pending' => ['label' => 'Pending', 'class' => 'bg-secondary'],
        'checked_in' => ['label' => 'Check-in', 'class' => 'bg-info'],
        'checked_out' => ['label' => 'Check-out', 'class' => 'bg-success'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-danger']
    ];
    $s = strtolower($status);
    return $map[$s] ?? ['label' => ucfirst($status), 'class' => 'bg-secondary'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Penitipan | Ling-Ling Pet Shop</title>
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

        .content-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
            margin-bottom: 20px;
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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
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
                    <a class="nav-link active" href="datapenitipan.php">
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
            <h4>Data Penitipan</h4>
            <div class="header-actions">
                <a href="tambah.php?type=penitipan" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Penitipan
                </a>
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table id="penitipanTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Penitipan</th>
                            <th>Pelanggan</th>
                            <th>Anabul</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Jumlah Hari</th>
                            <th>Kandang</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['id_penitipan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                    <td><?php echo $row['tanggal_checkin'] ? date('d/m/Y', strtotime($row['tanggal_checkin'])) : '-'; ?>
                                    </td>
                                    <td><?php echo $row['tanggal_checkout'] ? date('d/m/Y', strtotime($row['tanggal_checkout'])) : '-'; ?>
                                    </td>
                                    <td><?php echo ($row['jumlah_hari'] ?? 0) . ' hari'; ?></td>
                                    <td><?php echo htmlspecialchars($row['nomor_kandang'] ?? '-'); ?></td>
                                    <td>Rp <?php echo number_format($row['total_harga'] ?? 0, 0, ',', '.'); ?></td>
                                    <td>
                                        <?php $statusInfo = labelStatusPenitipan($row['status_checkin'] ?? 'pending'); ?>
                                        <span class="badge <?= $statusInfo['class'] ?>">
                                            <?= $statusInfo['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="ubah.php?type=penitipan&id=<?php echo $row['id_penitipan']; ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?type=penitipan&id=<?php echo $row['id_penitipan']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                        } else {
                            echo "<tr><td colspan='12' class='text-center'>Tidak ada data penitipan</td></tr>";
                        }
                        ?>
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
            $('#penitipanTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });
    </script>
</body>

</html>