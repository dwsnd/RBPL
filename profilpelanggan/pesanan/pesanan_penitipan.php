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

<div class="pesanan-content">
    <?php if (empty($orders)): ?>
        <div class="no-data">
            <i class="fas fa-paw"></i>
            <div class="mb-3">
                <strong>Belum ada riwayat penitipan</strong>
            </div>
            <p class="mb-4">Riwayat penitipan hewan peliharaan Anda akan tampil di sini</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <span class="pesanan-id">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        #<?= htmlspecialchars($order['id_pesanan']) ?>
                    </span>
                    <span class="pesanan-status status-<?= strtolower($order['status_pesanan']) ?>">
                        <?= $status_labels[$order['status_pesanan']] ?>
                    </span>
                </div>
                <div class="pesanan-detail">
                    <strong>Penitipan Hewan</strong><br>
                    <small class="text-muted">
                        <i class="fas fa-paw mr-1"></i>
                        Hewan: <?= htmlspecialchars($order['nama_hewan']) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Check-in: <?= date('d/m/Y', strtotime($order['tanggal_masuk'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar-times mr-1"></i>
                        Check-out: <?= date('d/m/Y', strtotime($order['tanggal_keluar'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        Durasi: <?= $order['durasi'] ?> hari
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-home mr-1"></i>
                        Paket: <?= ucfirst($order['jenis_kandang']) ?>
                    </small><br>
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Rp <?= number_format($order['total_biaya'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>