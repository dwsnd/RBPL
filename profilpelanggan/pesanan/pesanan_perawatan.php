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

// Ambil data pesanan pelanggan
$query = "SELECT 
            p.id_pesanan,
            p.tanggal_booking,
            p.tanggal_layanan,
            p.waktu_layanan,
            p.status_pesanan,
            p.total_harga,
            p.catatan_khusus,
            a.nama_hewan,
            a.kategori_hewan,
            a.karakteristik,
            dl.nama_layanan,
            dl.deskripsi as deskripsi_layanan
          FROM pesanan_layanan p
          JOIN anabul a ON p.id_anabul = a.id_anabul
          JOIN detail_layanan dl ON p.jenis_layanan = dl.jenis_layanan
          WHERE p.id_pelanggan = '$id_pelanggan'
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);
$pesanan_list = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pesanan_list[] = $row;
    }
}

// Function untuk format status
function formatStatus($status)
{
    $status_labels = [
        'pending' => ['Menunggu Konfirmasi', 'bg-warning', 'text-dark'],
        'confirmed' => ['Dikonfirmasi', 'bg-info', 'text-white'],
        'in_progress' => ['Sedang Diproses', 'bg-primary', 'text-white'],
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

<div class="pesanan-content">
    <?php if (empty($pesanan_list)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Belum Ada Pesanan</h4>
            <p class="text-muted mb-4">Anda belum memiliki pesanan layanan grooming</p>
            <a href="booking_form.php" class="btn btn-warning btn-lg">
                <i class="fas fa-plus me-2"></i>Buat Pesanan Pertama
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($pesanan_list as $pesanan): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm status-<?php echo $pesanan['status_pesanan']; ?>">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">
                                    #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
                                </h6>
                                <?php echo formatStatus($pesanan['status_pesanan']); ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-paw text-orange-500 me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pesanan['nama_hewan']); ?></h6>
                                    <small class="text-muted"><?php echo ucfirst($pesanan['kategori_hewan']); ?></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold text-dark"><?php echo $pesanan['nama_layanan']; ?></h6>
                                <small class="text-muted"><?php echo $pesanan['deskripsi_layanan']; ?></small>
                            </div>

                            <div class="timeline-item mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('d M Y', strtotime($pesanan['tanggal_layanan'])); ?>
                                </small>
                            </div>

                            <div class="timeline-item mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo formatWaktu($pesanan['waktu_layanan']); ?>
                                </small>
                            </div>

                            <div class="timeline-item mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Dibuat: <?php echo date('d/m/Y H:i', strtotime($pesanan['tanggal_booking'])); ?>
                                </small>
                            </div>

                            <?php if (!empty($pesanan['catatan_khusus'])): ?>
                                <div class="border-top pt-2 mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        <?php echo htmlspecialchars($pesanan['catatan_khusus']); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-dark">
                                    Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                </h5>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#detailModal<?php echo $pesanan['id_pesanan']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($pesanan['status_pesanan'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmCancel(<?php echo $pesanan['id_pesanan']; ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal<?php echo $pesanan['id_pesanan']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Detail Pesanan #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Informasi Hewan</h6>
                                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($pesanan['nama_hewan']); ?></p>
                                        <p><strong>Kategori:</strong> <?php echo ucfirst($pesanan['kategori_hewan']); ?></p>
                                        <?php if (!empty($pesanan['karakteristik'])): ?>
                                            <p><strong>Ciri Khusus:</strong>
                                                <?php echo htmlspecialchars($pesanan['karakteristik']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3">Informasi Layanan</h6>
                                        <p><strong>Layanan:</strong> <?php echo $pesanan['nama_layanan']; ?></p>
                                        <p><strong>Tanggal:</strong>
                                            <?php echo date('d M Y', strtotime($pesanan['tanggal_layanan'])); ?></p>
                                        <p><strong>Waktu:</strong> <?php echo formatWaktu($pesanan['waktu_layanan']); ?></p>
                                        <p><strong>Status:</strong> <?php echo formatStatus($pesanan['status_pesanan']); ?></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3">Deskripsi Layanan</h6>
                                        <p><?php echo $pesanan['deskripsi_layanan']; ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($pesanan['catatan_khusus'])): ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3">Catatan Khusus</h6>
                                            <p><?php echo htmlspecialchars($pesanan['catatan_khusus']); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <div class="w-100 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold text-dark">
                                        Total: Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                    </h5>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi Pembatalan -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6>Apakah Anda yakin ingin membatalkan pesanan ini?</h6>
                    <p class="text-muted">Pesanan yang dibatalkan tidak dapat dikembalikan</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="cancelForm" method="POST" action="cancel_order.php" style="display: inline;">
                    <input type="hidden" name="id_pesanan" id="cancelOrderId">
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function untuk konfirmasi pembatalan
    function confirmCancel(orderId) {
        document.getElementById('cancelOrderId').value = orderId;
        const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
        modal.show();
    }
</script>