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

$query = "SELECT 
    p.id_pesanan,
    p.nomor_pesanan,
    p.status_pesanan,
    p.total_harga,
    p.catatan_pelanggan,
    p.created_at,
    pen.id_penitipan,
    pen.tanggal_checkin,
    pen.tanggal_checkout,
    pen.waktu_checkin,
    pen.waktu_checkout,
    pen.jumlah_hari,
    pen.nomor_kandang,
    pen.status_checkin,
    a.nama_hewan,
    a.spesies,
    a.ciri_khusus,
    l.nama_layanan,
    l.deskripsi as deskripsi_layanan
FROM pesanan p
JOIN penitipan pen ON p.id_pesanan = pen.id_pesanan
JOIN pesanan_layanan pl ON p.id_pesanan = pl.id_pesanan
JOIN layanan l ON pl.id_layanan = l.id_layanan AND l.jenis_layanan = 'penitipan'
LEFT JOIN anabul a ON pen.id_anabul = a.id_anabul
WHERE p.id_pelanggan = ? AND (p.jenis_pesanan = 'penitipan' OR p.jenis_pesanan = '')
ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pelanggan]);
$pesanan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Check if there are any errors
if ($stmt->errorInfo()[0] !== '00000') {
    error_log("Database error in pesanan_penitipan.php: " . print_r($stmt->errorInfo(), true));
}

function formatStatus($status)
{
    $status_labels = [
        'pending' => ['Menunggu Konfirmasi', 'bg-warning', 'text-dark'],
        'confirmed' => ['Dikonfirmasi', 'bg-info', 'text-white'],
        'checked_in' => ['Check In', 'bg-primary', 'text-white'],
        'checked_out' => ['Selesai', 'bg-success', 'text-white'],
        'cancelled' => ['Dibatalkan', 'bg-danger', 'text-white']
    ];
    $label = isset($status_labels[$status]) ? $status_labels[$status] : ['Unknown', 'bg-secondary', 'text-white'];
    return "<span class='badge {$label[1]} {$label[2]} px-3 py-2'>{$label[0]}</span>";
}

function formatDate($date, $format = 'd M Y')
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return 'Belum ditentukan';
    }
    return date($format, strtotime($date));
}

function formatTime($time, $format = 'H:i')
{
    if (empty($time) || $time === '00:00:00') {
        return '';
    }
    return date($format, strtotime($time));
}
?>

<div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Riwayat Penitipan</h4>
            <p class="text-muted mb-0">Kelola semua penitipan hewan Anda</p>
        </div>
        <?php if (!empty($pesanan_list)): ?>
            <a href="../../dashboard/penitipan/penitipan_pelanggan.php"
                class="btn btn-orange-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus me-2"></i>Pesan Penitipan
            </a>
        <?php endif; ?>
    </div>

    <!-- Booking List -->
    <div class="pesanan-content">
        <?php if (empty($pesanan_list)): ?>
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-home text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-2">Belum Ada Riwayat Penitipan</h4>
                <p class="text-muted mb-4">Anda belum memiliki riwayat penitipan hewan</p>
                <a href="../../dashboard/penitipan/penitipan_pelanggan.php"
                    class="btn btn-orange-500 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-plus me-2"></i>Pesan Penitipan Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pesanan_list as $pesanan): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all duration-300">
                            <!-- Card Header -->
                            <div class="card-header bg-white border-bottom-0 pb-0">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">
                                            #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo formatDate($pesanan['tanggal_checkin']); ?>
                                        </small>
                                    </div>
                                    <?php echo formatStatus($pesanan['status_pesanan']); ?>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body pt-0">
                                <!-- Layanan Info -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-1"><?php echo $pesanan['nama_layanan']; ?></h6>
                                    <p class="text-muted small mb-0"><?php echo $pesanan['deskripsi_layanan']; ?></p>
                                </div>

                                <!-- Pet Info -->
                                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-paw"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pesanan['nama_hewan']); ?>
                                        </h6>
                                        <small
                                            class="text-muted"><?php echo ucfirst(htmlspecialchars($pesanan['spesies'])); ?></small>
                                    </div>
                                </div>

                                <!-- Schedule Info -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-check text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted">
                                                    <?php echo formatDate($pesanan['tanggal_checkin']); ?>
                                                </small>
                                                <?php
                                                $waktu_checkin = formatTime($pesanan['waktu_checkin']);
                                                if (!empty($waktu_checkin)):
                                                    ?>
                                                    <br><small class="text-muted"><?php echo $waktu_checkin; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-times text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted">
                                                    <?php echo formatDate($pesanan['tanggal_checkout']); ?>
                                                </small>
                                                <?php
                                                $waktu_checkout = formatTime($pesanan['waktu_checkout']);
                                                if (!empty($waktu_checkout)):
                                                    ?>
                                                    <br><small class="text-muted"><?php echo $waktu_checkout; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration & Cage Info -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-info me-2"></i>
                                            <small class="text-muted">
                                                <?php echo $pesanan['jumlah_hari']; ?> hari
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-home text-info me-2"></i>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($pesanan['nomor_kandang'] ?: 'Belum ditentukan'); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small">Total Biaya</span>
                                        <h5 class="mb-0 fw-bold text-dark">
                                            Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                        </h5>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#detailModal<?php echo $pesanan['id_penitipan']; ?>">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="detailModal<?php echo $pesanan['id_penitipan']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        Detail Penitipan #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Hewan</h6>
                                                    <p><strong>Nama:</strong>
                                                        <?php echo htmlspecialchars($pesanan['nama_hewan']); ?></p>
                                                    <p><strong>Spesies:</strong>
                                                        <?php echo ucfirst(htmlspecialchars($pesanan['spesies'])); ?></p>
                                                    <?php if (!empty($pesanan['ciri_khusus'])): ?>
                                                        <p><strong>Ciri Khusus:</strong>
                                                            <?php echo htmlspecialchars($pesanan['ciri_khusus']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Penitipan</h6>
                                                    <p><strong>Layanan:</strong> <?php echo $pesanan['nama_layanan']; ?></p>
                                                    <p><strong>Nomor Kandang:</strong>
                                                        <?php echo htmlspecialchars($pesanan['nomor_kandang'] ?: 'Belum ditentukan'); ?>
                                                    </p>
                                                    <p><strong>Status Pesanan:</strong>
                                                        <?php echo formatStatus($pesanan['status_pesanan']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3 text-primary">Jadwal Penitipan</h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <p><strong>Check-in:</strong>
                                                        <?php
                                                        echo formatDate($pesanan['tanggal_checkin']);
                                                        $waktu_checkin = formatTime($pesanan['waktu_checkin']);
                                                        if (!empty($waktu_checkin)) {
                                                            echo '<br><small class="text-muted">' . $waktu_checkin . '</small>';
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Check-out:</strong>
                                                        <?php
                                                        echo formatDate($pesanan['tanggal_checkout']);
                                                        $waktu_checkout = formatTime($pesanan['waktu_checkout']);
                                                        if (!empty($waktu_checkout)) {
                                                            echo '<br><small class="text-muted">' . $waktu_checkout . '</small>';
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Durasi:</strong> <?php echo $pesanan['jumlah_hari']; ?> hari</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .btn-orange-500 {
        background-color: #f97316;
        border-color: #f97316;
    }

    .btn-orange-500:hover {
        background-color: #ea580c;
        border-color: #ea580c;
    }

    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    .badge {
        border-radius: 8px;
        font-weight: 500;
    }
</style>

<script>
    // Fungsi untuk membuka modal detail
    function openDetailModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('detailModal' + id));
        modal.show();
    }
</script>