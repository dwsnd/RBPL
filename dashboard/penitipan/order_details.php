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
    p.nomor_telepon
FROM pesanan_penitipan pp
JOIN anabul a ON pp.id_anabul = a.id_anabul
LEFT JOIN penempatan_hewan ph ON pp.id_pesanan = ph.id_pesanan
JOIN pelanggan p ON pp.id_pelanggan = p.id_pelanggan
WHERE pp.id_pesanan = ? AND pp.id_pelanggan = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_pesanan, $id_pelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: pesanan.php');
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get status colors and labels
$status_colors = [
    'pending' => 'warning',
    'confirmed' => 'info',
    'checked_in' => 'primary',
    'checked_out' => 'success',
    'cancelled' => 'danger'
];

$status_labels = [
    'pending' => 'Menunggu Konfirmasi',
    'confirmed' => 'Dikonfirmasi',
    'checked_in' => 'Check In',
    'checked_out' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

$placement_status_labels = [
    'assigned' => 'Kandang Ditetapkan',
    'occupied' => 'Di Kandang',
    'released' => 'Dilepas'
];

// Fetch notifications
$notif_query = "SELECT * FROM notifikasi_penitipan WHERE id_pesanan = ? ORDER BY tanggal_kirim DESC";
$notif_stmt = mysqli_prepare($conn, $notif_query);
mysqli_stmt_bind_param($notif_stmt, "i", $id_pesanan);
mysqli_stmt_execute($notif_stmt);
$notifications = mysqli_stmt_get_result($notif_stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pesanan #<?php echo $id_pesanan; ?> - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .detail-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #fd7e14;
        }

        .timeline-item.active::before {
            background: #fd7e14;
        }

        .timeline-item.completed::before {
            background: #28a745;
            border-color: #28a745;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <!-- Main Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan #<?php echo $id_pesanan; ?></h2>
                        <p class="text-gray-600">Informasi lengkap tentang pesanan penitipan hewan Anda</p>
                    </div>
                    <a href="pesanan.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <!-- Order Status -->
                <div class="detail-card p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                <?php echo htmlspecialchars($order['nama_hewan']); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($order['kategori_hewan']); ?>
                            </p>
                        </div>
                        <span
                            class="status-badge bg-<?php echo $status_colors[$order['status_pesanan']]; ?>-100 text-<?php echo $status_colors[$order['status_pesanan']]; ?>-800">
                            <?php echo $status_labels[$order['status_pesanan']]; ?>
                        </span>
                    </div>

                    <!-- Timeline -->
                    <div class="timeline mt-6">
                        <div
                            class="timeline-item <?php echo in_array($order['status_pesanan'], ['pending', 'confirmed', 'checked_in', 'checked_out']) ? 'completed' : ''; ?>">
                            <div class="text-sm">
                                <span class="font-medium">Pesanan Dibuat</span>
                                <p class="text-gray-600">
                                    <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        <div
                            class="timeline-item <?php echo in_array($order['status_pesanan'], ['confirmed', 'checked_in', 'checked_out']) ? 'completed' : ''; ?>">
                            <div class="text-sm">
                                <span class="font-medium">Pesanan Dikonfirmasi</span>
                                <p class="text-gray-600">
                                    <?php
                                    if (in_array($order['status_pesanan'], ['confirmed', 'checked_in', 'checked_out'])) {
                                        echo date('d M Y H:i', strtotime($order['updated_at']));
                                    } else {
                                        echo 'Menunggu konfirmasi';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div
                            class="timeline-item <?php echo in_array($order['status_pesanan'], ['checked_in', 'checked_out']) ? 'completed' : ''; ?>">
                            <div class="text-sm">
                                <span class="font-medium">Check In</span>
                                <p class="text-gray-600">
                                    <?php
                                    if (in_array($order['status_pesanan'], ['checked_in', 'checked_out'])) {
                                        echo date('d M Y H:i', strtotime($order['tanggal_masuk']));
                                    } else {
                                        echo 'Menunggu check in';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div
                            class="timeline-item <?php echo $order['status_pesanan'] === 'checked_out' ? 'completed' : ''; ?>">
                            <div class="text-sm">
                                <span class="font-medium">Check Out</span>
                                <p class="text-gray-600">
                                    <?php
                                    if ($order['status_pesanan'] === 'checked_out') {
                                        echo date('d M Y H:i', strtotime($order['tanggal_keluar']));
                                    } else {
                                        echo 'Menunggu check out';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pet Information -->
                    <div class="detail-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-paw me-2 text-orange-500"></i>Informasi Hewan
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Nama Hewan:</span>
                                <span class="font-medium"><?php echo htmlspecialchars($order['nama_hewan']); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Kategori:</span>
                                <span
                                    class="font-medium"><?php echo htmlspecialchars($order['kategori_hewan']); ?></span>
                            </div>
                            <?php if ($order['karakteristik']): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Karakteristik:</span>
                                    <span
                                        class="font-medium"><?php echo htmlspecialchars($order['karakteristik']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Boarding Details -->
                    <div class="detail-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-home me-2 text-orange-500"></i>Detail Penitipan
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tanggal Masuk:</span>
                                <span
                                    class="font-medium"><?php echo date('d M Y', strtotime($order['tanggal_masuk'])); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tanggal Keluar:</span>
                                <span
                                    class="font-medium"><?php echo date('d M Y', strtotime($order['tanggal_keluar'])); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Durasi:</span>
                                <span class="font-medium"><?php echo $order['durasi']; ?> hari</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Paket:</span>
                                <span class="font-medium"><?php echo ucfirst($order['jenis_kandang']); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Status Penempatan:</span>
                                <span
                                    class="font-medium"><?php echo $placement_status_labels[$order['status_penempatan']]; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="detail-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-user me-2 text-orange-500"></i>Informasi Pelanggan
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Nama:</span>
                                <span
                                    class="font-medium"><?php echo htmlspecialchars($order['nama_pelanggan']); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Nomor Telepon:</span>
                                <span
                                    class="font-medium"><?php echo htmlspecialchars($order['nomor_telepon']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="detail-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-money-bill me-2 text-orange-500"></i>Informasi Pembayaran
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Biaya:</span>
                                <span class="font-medium">Rp
                                    <?php echo number_format($order['total_biaya'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Status Pembayaran:</span>
                                <span
                                    class="font-medium"><?php echo $order['status_pembayaran'] ?? 'Belum Dibayar'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <?php if ($order['catatan_khusus']): ?>
                    <div class="detail-card p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-clipboard-list me-2 text-orange-500"></i>Catatan Khusus
                        </h3>
                        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($order['catatan_khusus'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Notifications -->
                <?php if (mysqli_num_rows($notifications) > 0): ?>
                    <div class="detail-card p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-bell me-2 text-orange-500"></i>Notifikasi
                        </h3>
                        <div class="space-y-4">
                            <?php while ($notif = mysqli_fetch_assoc($notifications)): ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-circle text-orange-500 text-xs mt-1"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-800"><?php echo htmlspecialchars($notif['pesan']); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo date('d M Y H:i', strtotime($notif['tanggal_kirim'])); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center mt-6">
                    <?php if ($order['status_pesanan'] === 'pending'): ?>
                        <button class="btn btn-danger" onclick="cancelOrder(<?php echo $id_pesanan; ?>)">
                            <i class="fas fa-times-circle me-2"></i>Batalkan Pesanan
                        </button>
                    <?php endif; ?>

                    <?php if ($order['status_pesanan'] === 'checked_out'): ?>
                        <button class="btn btn-primary" onclick="viewReceipt(<?php echo $id_pesanan; ?>)">
                            <i class="fas fa-receipt me-2"></i>Lihat Struk
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                window.location.href = `cancel_order.php?id=${orderId}`;
            }
        }

        function viewReceipt(orderId) {
            window.location.href = `receipt.php?id=${orderId}`;
        }
    </script>
</body>

</html>