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

$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch user's orders with pet and placement details
$query = "SELECT 
    pp.*,
    a.nama_hewan,
    a.kategori_hewan,
    ph.jenis_kandang,
    ph.status_penempatan,
    DATEDIFF(pp.tanggal_keluar, pp.tanggal_masuk) as durasi
FROM pesanan_penitipan pp
JOIN anabul a ON pp.id_anabul = a.id_anabul
LEFT JOIN penempatan_hewan ph ON pp.id_pesanan = ph.id_pesanan
WHERE pp.id_pelanggan = ?
ORDER BY pp.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_pelanggan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}

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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pesanan Penitipan - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .order-card {
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Daftar Pesanan Penitipan</h2>
                        <p class="text-gray-600">Kelola dan pantau status penitipan hewan peliharaan Anda</p>
                    </div>
                    <a href="penitipan_pelanggan.php" class="btn btn-primary bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus-circle me-2"></i>Buat Pesanan Baru
                    </a>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-list text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 mb-4">Anda belum memiliki pesanan penitipan hewan</p>
                        <a href="penitipan_pelanggan.php" class="btn btn-primary bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus-circle me-2"></i>Buat Pesanan Baru
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Orders List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-6">
                                    <!-- Order Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                <?php echo htmlspecialchars($order['nama_hewan']); ?>
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                <?php echo htmlspecialchars($order['kategori_hewan']); ?>
                                            </p>
                                        </div>
                                        <span class="status-badge bg-<?php echo $status_colors[$order['status_pesanan']]; ?>-100 text-<?php echo $status_colors[$order['status_pesanan']]; ?>-800">
                                            <?php echo $status_labels[$order['status_pesanan']]; ?>
                                        </span>
                                    </div>

                                    <!-- Order Details -->
                                    <div class="space-y-3 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">ID Pesanan:</span>
                                            <span class="font-medium">#<?php echo $order['id_pesanan']; ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Tanggal Masuk:</span>
                                            <span class="font-medium"><?php echo date('d M Y', strtotime($order['tanggal_masuk'])); ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Tanggal Keluar:</span>
                                            <span class="font-medium"><?php echo date('d M Y', strtotime($order['tanggal_keluar'])); ?></span>
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
                                            <span class="text-gray-600">Total Biaya:</span>
                                            <span class="font-medium">Rp <?php echo number_format($order['total_biaya'], 0, ',', '.'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="timeline mb-4">
                                        <div class="timeline-item <?php echo in_array($order['status_pesanan'], ['pending', 'confirmed', 'checked_in', 'checked_out']) ? 'completed' : ''; ?>">
                                            <div class="text-sm">
                                                <span class="font-medium">Pesanan Dibuat</span>
                                                <p class="text-gray-600"><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="timeline-item <?php echo in_array($order['status_pesanan'], ['confirmed', 'checked_in', 'checked_out']) ? 'completed' : ''; ?>">
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
                                        <div class="timeline-item <?php echo in_array($order['status_pesanan'], ['checked_in', 'checked_out']) ? 'completed' : ''; ?>">
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
                                        <div class="timeline-item <?php echo $order['status_pesanan'] === 'checked_out' ? 'completed' : ''; ?>">
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

                                    <!-- Action Buttons -->
                                    <div class="flex justify-between items-center pt-4 border-t">
                                        <?php if ($order['status_pesanan'] === 'pending'): ?>
                                            <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['id_pesanan']; ?>)">
                                                <i class="fas fa-times-circle me-1"></i>Batalkan
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status_pesanan'] === 'checked_out'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="viewReceipt(<?php echo $order['id_pesanan']; ?>)">
                                                <i class="fas fa-receipt me-1"></i>Lihat Struk
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn btn-info btn-sm" onclick="viewDetails(<?php echo $order['id_pesanan']; ?>)">
                                            <i class="fas fa-info-circle me-1"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                // Implement cancel order functionality
                window.location.href = `cancel_order.php?id=${orderId}`;
            }
        }

        function viewReceipt(orderId) {
            // Implement view receipt functionality
            window.location.href = `receipt.php?id=${orderId}`;
        }

        function viewDetails(orderId) {
            // Implement view details functionality
            window.location.href = `order_details.php?id=${orderId}`;
        }
    </script>
</body>

</html>