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

// Ambil data pesanan perawatan milik pelanggan ini
$query = "SELECT 
    p.id_pesanan,
    p.nomor_pesanan,
    p.tanggal_layanan,
    p.waktu_layanan,
    p.status_pesanan,
    p.total_harga,
    p.catatan_pelanggan,
    pl.id_detail,
    pl.harga_layanan,
    pl.catatan_khusus,
    a.nama_hewan,
    a.spesies,
    a.ciri_khusus,
    l.nama_layanan,
    l.deskripsi as deskripsi_layanan
FROM pesanan p
JOIN pesanan_layanan pl ON p.id_pesanan = pl.id_pesanan
JOIN layanan l ON pl.id_layanan = l.id_layanan
LEFT JOIN anabul a ON pl.id_anabul = a.id_anabul
WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'perawatan'
ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pelanggan]);
$pesanan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function untuk format status
function formatStatus($status)
{
    $status_labels = [
        'pending' => ['Menunggu Konfirmasi', 'bg-warning', 'text-dark'],
        'confirmed' => ['Dikonfirmasi', 'bg-info', 'text-white'],
        'processing' => ['Sedang Diproses', 'bg-primary', 'text-white'],
        'completed' => ['Selesai', 'bg-success', 'text-white'],
        'cancelled' => ['Dibatalkan', 'bg-danger', 'text-white']
    ];

    $label = isset($status_labels[$status]) ? $status_labels[$status] : ['Unknown', 'bg-secondary', 'text-white'];
    return "<span class='badge {$label[1]} {$label[2]} px-3 py-2'>{$label[0]}</span>";
}

// Function untuk format waktu
function formatWaktu($waktu)
{
    $waktu_labels = [
        'pagi' => '08:00 - 10:00',
        'siang' => '10:00 - 12:00',
        'sore' => '13:00 - 15:00',
        'sore-akhir' => '15:00 - 17:00'
    ];
    return isset($waktu_labels[$waktu]) ? $waktu_labels[$waktu] : $waktu;
}
?>

<div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Riwayat Perawatan</h4>
            <p class="text-muted mb-0">Kelola semua layanan grooming hewan Anda</p>
        </div>
        <?php if (!empty($pesanan_list)): ?>
            <a href="../../dashboard/perawatan/perawatan_pelanggan.php"
                class="btn btn-orange-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus me-2"></i>Pesan Perawatan
            </a>
        <?php endif; ?>
    </div>

    <!-- Booking List -->
    <div class="pesanan-content">
        <?php if (empty($pesanan_list)): ?>
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-cut text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-2">Belum Ada Riwayat Perawatan</h4>
                <p class="text-muted mb-4">Anda belum memiliki riwayat layanan grooming</p>
                <a href="../../dashboard/perawatan/perawatan_pelanggan.php"
                    class="btn btn-orange-500 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-plus me-2"></i>Pesan Perawatan Pertama
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
                                            <?php echo date('d M Y', strtotime($pesanan['tanggal_layanan'])); ?>
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
                                        <small class="text-muted"><?php echo ucfirst($pesanan['spesies']); ?></small>
                                    </div>
                                </div>

                                <!-- Schedule Info -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($pesanan['tanggal_layanan'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <small class="text-muted">
                                                <?php
                                                if (!empty($pesanan['waktu_layanan'])) {
                                                    echo formatWaktu($pesanan['waktu_layanan']);
                                                } else {
                                                    echo 'Waktu belum ditentukan';
                                                }
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <?php if (!empty($pesanan['catatan_khusus'])): ?>
                                    <div class="border-top pt-3 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-sticky-note text-warning me-2 mt-1"></i>
                                            <div>
                                                <small class="text-muted fw-semibold">Catatan Khusus:</small>
                                                <p class="text-muted small mb-0">
                                                    <?php echo htmlspecialchars($pesanan['catatan_khusus']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small">Total Biaya</span>
                                        <h5 class="mb-0 fw-bold text-dark">
                                            Rp <?php echo number_format($pesanan['harga_layanan'], 0, ',', '.'); ?>
                                        </h5>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#detailModal<?php echo htmlspecialchars($pesanan['id_pesanan']); ?>">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="detailModal<?php echo htmlspecialchars($pesanan['id_pesanan']); ?>"
                        tabindex=" -1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        Detail Perawatan #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
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
                                                    <p><strong>Spesies:</strong> <?php echo ucfirst($pesanan['spesies']); ?></p>
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
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Layanan</h6>
                                                    <p><strong>Layanan:</strong> <?php echo $pesanan['nama_layanan']; ?></p>
                                                    <p><strong>Tanggal:</strong>
                                                        <?php echo date('d M Y', strtotime($pesanan['tanggal_layanan'])); ?></p>
                                                    <p><strong>Waktu:</strong>
                                                        <?php
                                                        if (!empty($pesanan['waktu_layanan'])) {
                                                            echo formatWaktu($pesanan['waktu_layanan']);
                                                        } else {
                                                            echo 'Waktu belum ditentukan';
                                                        }
                                                        ?>
                                                    </p>
                                                    <p><strong>Status:</strong>
                                                        <?php echo formatStatus($pesanan['status_pesanan']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3 text-primary">Deskripsi Layanan</h6>
                                            <p class="text-muted"><?php echo $pesanan['deskripsi_layanan']; ?></p>
                                        </div>
                                    </div>
                                    <?php if (!empty($pesanan['catatan_khusus'])): ?>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3 text-primary">Catatan Khusus</h6>
                                                <p class="text-muted"><?php echo htmlspecialchars($pesanan['catatan_khusus']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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