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
    p.created_at,
    k.id_konsultasi,
    k.keluhan_utama,
    k.gejala,
    k.durasi_gejala,
    k.tanggal_kontrol,
    k.status_konsultasi,
    a.nama_hewan,
    a.spesies,
    a.ciri_khusus,
    d.nama_dokter,
    d.spesialisasi,
    d.tarif_konsultasi
FROM pesanan p
JOIN konsultasi k ON p.id_pesanan = k.id_pesanan
LEFT JOIN anabul a ON k.id_anabul = a.id_anabul
LEFT JOIN dokter_hewan d ON k.id_dokter = d.id_dokter
WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'konsultasi'
ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pelanggan]);
$pesanan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to format status
function formatStatus($status)
{
    // Handle empty or null status
    if (empty($status) || $status === '') {
        $status = 'pending';
    }

    $status_labels = [
        'pending' => ['Menunggu Konfirmasi', 'bg-warning', 'text-dark'],
        'scheduled' => ['Terjadwal', 'bg-info', 'text-white'],
        'ongoing' => ['Sedang Berlangsung', 'bg-primary', 'text-white'],
        'completed' => ['Selesai', 'bg-success', 'text-white'],
        'cancelled' => ['Dibatalkan', 'bg-danger', 'text-white'],
        'confirmed' => ['Dikonfirmasi', 'bg-info', 'text-white'],
        'processing' => ['Sedang Diproses', 'bg-primary', 'text-white']
    ];
    $label = isset($status_labels[$status]) ? $status_labels[$status] : ['Menunggu Konfirmasi', 'bg-warning', 'text-dark'];
    return "<span class='badge {$label[1]} {$label[2]} px-3 py-2'>{$label[0]}</span>";
}

// Function to format durasi gejala
function formatDurasiGejala($durasi)
{
    if ($durasi === 'kurang_dari_1_hari')
        return '&lt; 1 hari';
    if ($durasi === 'lebih_dari_1_minggu')
        return '&gt; 1 minggu';
    // Ganti underscore dengan spasi dan angka dengan strip
    $durasi = str_replace('_', ' ', $durasi);
    $durasi = str_replace('1-3 hari', '1-3 hari', $durasi); // handle khusus jika ada
    return $durasi;
}
?>

<div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Riwayat Konsultasi</h4>
            <p class="text-muted mb-0">Kelola semua konsultasi Anda</p>
        </div>
        <?php if (!empty($pesanan_list)): ?>
            <a href="../../dashboard/konsultasi/konsultasi_pelanggan.php"
                class="btn btn-orange-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus me-2"></i>Buat Konsultasi Baru
            </a>
        <?php endif; ?>
    </div>

    <!-- Booking List -->
    <div class="pesanan-content">
        <?php if (empty($pesanan_list)): ?>
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-stethoscope text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-2">Belum Ada Riwayat Konsultasi</h4>
                <p class="text-muted mb-4">Anda belum memiliki riwayat konsultasi dengan dokter hewan</p>
                <a href="../../dashboard/konsultasi/konsultasi_pelanggan.php"
                    class="btn btn-orange-500 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-plus me-2"></i>Buat Konsultasi Pertama
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
                                            <?php echo date('d M Y', strtotime($pesanan['tanggal_kontrol'])); ?>
                                        </small>
                                    </div>
                                    <?php echo formatStatus($pesanan['status_konsultasi']); ?>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body pt-0">
                                <!-- Layanan Info -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-1">Konsultasi Dokter Hewan</h6>
                                    <p class="text-muted small mb-0">Keluhan:
                                        <?php echo htmlspecialchars($pesanan['keluhan_utama']); ?>
                                    </p>
                                </div>

                                <!-- Doctor Info -->
                                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pesanan['nama_dokter']); ?>
                                        </h6>
                                        <small
                                            class="text-muted"><?php echo htmlspecialchars($pesanan['spesialisasi']); ?></small>
                                    </div>
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
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($pesanan['tanggal_kontrol'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <small class="text-muted">
                                                Durasi: <?php echo formatDurasiGejala($pesanan['durasi_gejala']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <?php if (!empty($pesanan['gejala'])): ?>
                                    <div class="border-top pt-3 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-sticky-note text-warning me-2 mt-1"></i>
                                            <div>
                                                <small class="text-muted fw-semibold">Gejala:</small>
                                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($pesanan['gejala']); ?>
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
                                            Rp <?php echo number_format($pesanan['tarif_konsultasi'], 0, ',', '.'); ?>
                                        </h5>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#detailModal<?php echo $pesanan['id_konsultasi']; ?>">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="detailModal<?php echo $pesanan['id_konsultasi']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        Detail Konsultasi #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
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
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Dokter</h6>
                                                    <p><strong>Nama:</strong>
                                                        <?php echo htmlspecialchars($pesanan['nama_dokter']); ?></p>
                                                    <p><strong>Spesialisasi:</strong>
                                                        <?php echo htmlspecialchars($pesanan['spesialisasi']); ?></p>
                                                    <p><strong>Status:</strong>
                                                        <?php echo formatStatus($pesanan['status_konsultasi']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3 text-primary">Jadwal Konsultasi</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <p><strong>Tanggal:</strong>
                                                        <?php echo date('d M Y', strtotime($pesanan['tanggal_kontrol'])); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Durasi Gejala:</strong>
                                                        <?php echo formatDurasiGejala($pesanan['durasi_gejala']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3 text-primary">Informasi Konsultasi</h6>
                                            <p><strong>Keluhan Utama:</strong>
                                                <?php echo htmlspecialchars($pesanan['keluhan_utama']); ?></p>
                                            <p><strong>Gejala:</strong> <?php echo htmlspecialchars($pesanan['gejala']); ?></p>
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
        min-height: 400px;
        display: flex;
        flex-direction: column;
    }

    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-footer {
        margin-top: auto;
    }

    .badge {
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-md-6 {
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 576px) {
        .col-12 {
            margin-bottom: 1rem;
        }

        .card {
            min-height: 350px;
        }
    }

    /* Ensure text doesn't overflow */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Better spacing for card content */
    .card-body .mb-3:last-child {
        margin-bottom: 0 !important;
    }

    .card-footer {
        padding: 1rem;
        background-color: #fff;
        border-top: 1px solid rgba(0, 0, 0, .125);
    }
</style>

<script>
    // Fungsi untuk membuka modal detail
    function openDetailModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('detailModal' + id));
        modal.show();
    }
</script>