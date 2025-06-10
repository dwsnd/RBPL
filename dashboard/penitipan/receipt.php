<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: pesanan.php');
    exit();
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);
$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch order details with all related information
$query = "SELECT 
    pp.*,
    a.nama_hewan,
    a.kategori_hewan,
    a.karakteristik,
    ph.jenis_kandang,
    ph.status_penempatan,
    ph.catatan_khusus,
    DATEDIFF(pp.tanggal_keluar, pp.tanggal_masuk) as durasi,
    p.nama_lengkap as nama_pelanggan,
    p.nomor_telepon,
    p.alamat
FROM pesanan_penitipan pp
JOIN anabul a ON pp.id_anabul = a.id_anabul
LEFT JOIN penempatan_hewan ph ON pp.id_pesanan = ph.id_pesanan
JOIN pelanggan p ON pp.id_pelanggan = p.id_pelanggan
WHERE pp.id_pesanan = ? AND pp.id_pelanggan = ? AND pp.status_pesanan = 'checked_out'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_pesanan, $id_pelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: pesanan.php');
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get service type label
$service_labels = [
    'basic' => 'Basic - Kandang Standar',
    'premium' => 'Premium - Kandang Luas',
    'vip' => 'VIP - Kandang VIP'
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #<?php echo $id_pesanan; ?> - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px dashed #e5e7eb;
        }

        .receipt-body {
            margin-bottom: 2rem;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px dashed #e5e7eb;
        }

        .receipt-table {
            width: 100%;
            margin: 1rem 0;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 0.5rem;
            text-align: left;
        }

        .receipt-table th {
            background-color: #f9fafb;
        }

        @media print {
            body {
                background: white;
            }

            .receipt {
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <div class="receipt">
            <!-- Receipt Header -->
            <div class="receipt-header">
                <h1 class="text-2xl font-bold text-gray-800">Ling-Ling Pet Shop</h1>
                <p class="text-gray-600">Struk Penitipan Hewan</p>
                <p class="text-sm text-gray-500">Jl. Contoh No. 123, Kota</p>
                <p class="text-sm text-gray-500">Telp: (021) 1234-5678</p>
            </div>

            <!-- Receipt Body -->
            <div class="receipt-body">
                <!-- Order Information -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">No. Pesanan</p>
                        <p class="font-semibold">#<?php echo $id_pesanan; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal</p>
                        <p class="font-semibold"><?php echo date('d M Y H:i', strtotime($order['updated_at'])); ?></p>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Pelanggan</h3>
                    <p class="text-sm"><span class="text-gray-600">Nama:</span>
                        <?php echo htmlspecialchars($order['nama_pelanggan']); ?></p>
                    <p class="text-sm"><span class="text-gray-600">Telepon:</span>
                        <?php echo htmlspecialchars($order['nomor_telepon']); ?></p>
                    <?php if ($order['alamat']): ?>
                        <p class="text-sm"><span class="text-gray-600">Alamat:</span>
                            <?php echo htmlspecialchars($order['alamat']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Pet Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Hewan</h3>
                    <p class="text-sm"><span class="text-gray-600">Nama Hewan:</span>
                        <?php echo htmlspecialchars($order['nama_hewan']); ?></p>
                    <p class="text-sm"><span class="text-gray-600">Kategori:</span>
                        <?php echo htmlspecialchars($order['kategori_hewan']); ?></p>
                    <?php if ($order['karakteristik']): ?>
                        <p class="text-sm"><span class="text-gray-600">Karakteristik:</span>
                            <?php echo htmlspecialchars($order['karakteristik']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Service Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Layanan</h3>
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>Deskripsi</th>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $service_labels[$order['jenis_kandang']]; ?></td>
                                <td>
                                    <?php echo date('d M Y', strtotime($order['tanggal_masuk'])); ?> -
                                    <?php echo date('d M Y', strtotime($order['tanggal_keluar'])); ?>
                                </td>
                                <td><?php echo $order['durasi']; ?> hari</td>
                                <td>Rp <?php echo number_format($order['total_biaya'], 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-semibold">Total</td>
                                <td class="font-semibold">Rp
                                    <?php echo number_format($order['total_biaya'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Special Instructions -->
                <?php if ($order['catatan_khusus']): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Catatan Khusus</h3>
                        <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($order['catatan_khusus'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Receipt Footer -->
            <div class="receipt-footer">
                <p class="text-sm text-gray-600">Terima kasih telah mempercayakan hewan peliharaan Anda kepada kami</p>
                <p class="text-sm text-gray-600 mt-2">Struk ini adalah bukti pembayaran yang sah</p>
            </div>

            <!-- Print Button -->
            <div class="text-center mt-6 no-print">
                <button onclick="window.print()"
                    class="btn btn-primary bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-print me-2"></i>Cetak Struk
                </button>
                <a href="pesanan.php" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>