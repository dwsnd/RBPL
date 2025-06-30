<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

$query = "SELECT 
    p.id_pesanan,
    p.nomor_pesanan,
    p.tanggal_pesanan,
    p.status_pesanan,
    p.status_pembayaran,
    p.total_harga,
    p.metode_pembayaran,
    pp.id_detail,
    pp.quantity,
    pp.harga_satuan,
    pp.subtotal,
    pr.nama_produk,
    pr.harga,
    pr.foto_utama
FROM pesanan p
JOIN pesanan_produk pp ON p.id_pesanan = pp.id_pesanan
JOIN produk pr ON pp.id_produk = pr.id_produk
WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'produk'
ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pelanggan]);
$pesanan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Riwayat Pesanan Produk</h4>
            <p class="text-muted mb-0">Kelola semua pesanan produk Anda</p>
        </div>
        <?php if (!empty($pesanan_list)): ?>
            <a href="../../dashboard/shop/shop_pelanggan.php" class="btn btn-orange-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus me-2"></i>Belanja Produk
            </a>
        <?php endif; ?>
    </div>

    <!-- Booking List -->
    <div class="pesanan-content">
        <?php if (empty($pesanan_list)): ?>
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-2">Belum Ada Riwayat Pesanan Produk</h4>
                <p class="text-muted mb-4">Anda belum memiliki riwayat pesanan produk</p>
                <a href="../../dashboard/shop/shop_pelanggan.php"
                    class="btn btn-orange-500 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-plus me-2"></i>Belanja Produk Pertama
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
                                            <?php echo date('d M Y', strtotime($pesanan['tanggal_pesanan'])); ?>
                                        </small>
                                    </div>
                                    <?php echo formatStatus($pesanan['status_pesanan']); ?>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body pt-0">
                                <!-- Product Info -->
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <?php
                                        $image_path = $pesanan['foto_utama'];
                                        if (!empty($image_path)) {
                                            // Clean up the path
                                            $image_path = trim($image_path);
                                            $image_path = str_replace('\\', '/', $image_path);

                                            // If the path doesn't start with uploads/, add it
                                            if (!str_starts_with($image_path, 'uploads/')) {
                                                $image_path = 'uploads/produk/' . $image_path;
                                            }

                                            // Add ../../ to make it relative to profilpelanggan folder
                                            $final_image_path = '../../' . $image_path;
                                        } else {
                                            $final_image_path = '../../aset/default-product.png';
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($final_image_path); ?>"
                                            alt="<?php echo htmlspecialchars($pesanan['nama_produk']); ?>" class="rounded"
                                            style="width: 60px; height: 60px; object-fit: cover;"
                                            onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1">
                                            <?php echo htmlspecialchars($pesanan['nama_produk']); ?>
                                        </h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-sort-numeric-up text-primary me-2"></i>
                                            <small class="text-muted">
                                                Jumlah: <?php echo number_format($pesanan['quantity']); ?> item
                                            </small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tag text-success me-2"></i>
                                            <small class="text-muted">
                                                Harga Satuan:
                                                Rp<?php echo number_format($pesanan['harga_satuan'], 0, ',', '.'); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Info -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($pesanan['tanggal_pesanan'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($pesanan['tanggal_pesanan'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div class="bg-light rounded p-2 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Subtotal:</span>
                                        <span class="fw-bold text-success">
                                            Rp <?php echo number_format($pesanan['subtotal'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small">Total Pesanan</span>
                                        <h5 class="mb-0 fw-bold text-dark">
                                            Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                        </h5>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <?php if ($pesanan['status_pembayaran'] === 'pending'): ?>
                                            <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal"
                                                data-bs-target="#bayarModal">
                                                <i class="fas fa-money-bill-wave me-1"></i>Bayar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="sudahBayarAjax(<?php echo $pesanan['id_pesanan']; ?>, this)">
                                                <i class="fas fa-check-circle me-1"></i>Sudah Bayar
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#detailModal<?php echo $pesanan['id_detail']; ?>">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="detailModal<?php echo $pesanan['id_detail']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        Detail Pesanan Produk
                                        #<?php echo str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Produk</h6>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <?php
                                                        $modal_image_path = $pesanan['foto_utama'];
                                                        if (!empty($modal_image_path)) {
                                                            // Clean up the path
                                                            $modal_image_path = trim($modal_image_path);
                                                            $modal_image_path = str_replace('\\', '/', $modal_image_path);

                                                            // If the path doesn't start with uploads/, add it
                                                            if (!str_starts_with($modal_image_path, 'uploads/')) {
                                                                $modal_image_path = 'uploads/produk/' . $modal_image_path;
                                                            }

                                                            // Add ../../ to make it relative to profilpelanggan folder
                                                            $modal_final_image_path = '../../' . $modal_image_path;
                                                        } else {
                                                            $modal_final_image_path = '../../aset/default-product.png';
                                                        }
                                                        ?>
                                                        <img src="<?php echo htmlspecialchars($modal_final_image_path); ?>"
                                                            alt="<?php echo htmlspecialchars($pesanan['nama_produk']); ?>"
                                                            class="rounded me-3"
                                                            style="width: 80px; height: 80px; object-fit: cover;"
                                                            onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                                                        <div>
                                                            <h6 class="fw-bold mb-1">
                                                                <?php echo htmlspecialchars($pesanan['nama_produk']); ?>
                                                            </h6>
                                                            <p class="text-muted small mb-0">Produk hewan peliharaan</p>
                                                        </div>
                                                    </div>
                                                    <p><strong>Harga Satuan:</strong> Rp
                                                        <?php echo number_format($pesanan['harga_satuan'], 0, ',', '.'); ?>
                                                    </p>
                                                    <p><strong>Jumlah:</strong>
                                                        <?php echo number_format($pesanan['quantity']); ?> item</p>
                                                    <p><strong>Subtotal:</strong> Rp
                                                        <?php echo number_format($pesanan['subtotal'], 0, ',', '.'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3 text-primary">Informasi Pesanan</h6>
                                                    <p><strong>Nomor Pesanan:</strong> <?php echo $pesanan['nomor_pesanan']; ?>
                                                    </p>
                                                    <p><strong>Tanggal Pesanan:</strong>
                                                        <?php echo date('d M Y H:i', strtotime($pesanan['tanggal_pesanan'])); ?>
                                                    </p>
                                                    <p><strong>Status:</strong>
                                                        <?php echo formatStatus($pesanan['status_pesanan']); ?></p>
                                                    <p><strong>Metode Pembayaran:</strong>
                                                        <?php
                                                        if (isset($pesanan['metode_pembayaran'])) {
                                                            if ($pesanan['metode_pembayaran'] === 'transfer') {
                                                                echo 'Transfer';
                                                            } elseif ($pesanan['metode_pembayaran'] === 'cod') {
                                                                echo 'Cash on Delivery (COD)';
                                                            } else {
                                                                echo 'Tidak diketahui';
                                                            }
                                                        } else {
                                                            echo 'Tidak diketahui';
                                                        }
                                                        ?>
                                                    </p>
                                                    <p><strong>Total:</strong> Rp
                                                        <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                                    </p>
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

<!-- Modal Bayar (Popup) -->
<div class="modal fade" id="bayarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Informasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Transfer Bank BRI</strong>
                    <div class="input-group mt-2 mb-3">
                        <input type="text" class="form-control" id="briNumber" value="023601063285505" readonly>
                        <button class="btn btn-outline-secondary" type="button"
                            onclick="copyInputValue('briNumber')">Salin Nomor</button>
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Dana / OVO / Gopay</strong>
                    <div class="input-group mt-2">
                        <input type="text" class="form-control" id="ewalletNumber" value="081229496101" readonly>
                        <button class="btn btn-outline-secondary" type="button"
                            onclick="copyInputValue('ewalletNumber')">Salin Nomor</button>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0" style="font-size: 0.95rem;">
                    Setelah melakukan pembayaran, silakan konfirmasi ke admin melalui WhatsApp atau menu konfirmasi
                    pembayaran.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
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

    // Fungsi untuk salin hanya nomor rekening/ewallet
    function copyInputValue(inputId) {
        var input = document.getElementById(inputId);
        var value = input.value;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(value).then(function () {
                if (typeof showPopup === 'function') showPopup('Nomor berhasil disalin!', 'success');
            }).catch(function (err) {
                // Fallback jika gagal
                fallbackCopy(value);
                if (typeof showPopup === 'function') showPopup('Gagal menyalin dengan clipboard API, mencoba fallback.', 'error');
                console.log('Clipboard API error:', err);
            });
        } else {
            fallbackCopy(value);
        }
    }

    function fallbackCopy(value) {
        try {
            var tempInput = document.createElement('input');
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999);
            var success = document.execCommand('copy');
            document.body.removeChild(tempInput);
            if (success) {
                if (typeof showPopup === 'function') showPopup('Nomor berhasil disalin!', 'success');
            } else {
                alert('Gagal menyalin nomor. Silakan salin manual.');
            }
        } catch (e) {
            alert('Gagal menyalin nomor. Silakan salin manual.');
            console.log('Fallback copy error:', e);
        }
    }

    // Fungsi untuk notifikasi sudah bayar
    function sudahBayarNotif() {
        if (typeof showPopup === 'function') {
            showPopup('Terima kasih, pembayaran Anda akan segera diverifikasi.', 'success');
        } else {
            alert('Terima kasih, pembayaran Anda akan segera diverifikasi.');
        }
    }

    function sudahBayarAjax(id_pesanan, btn) {
        alert('Fungsi sudahBayarAjax terpanggil, id_pesanan: ' + id_pesanan);
        if (!confirm('Apakah Anda yakin sudah melakukan pembayaran?')) return;
        btn.disabled = true;
        alert('Mengirim request ke update_status_pembayaran.php...');
        fetch('update_status_pembayaran.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_pesanan=${id_pesanan}`
        })
            .then(res => {
                alert('Response status: ' + res.status);
                return res.json();
            })
            .then(data => {
                alert('Response data: ' + JSON.stringify(data));
                if (data.success) {
                    if (typeof showPopup === 'function') showPopup('Status pembayaran berhasil diupdate. Menunggu konfirmasi admin.', 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    btn.disabled = false;
                    if (typeof showPopup === 'function') showPopup('Gagal update status pembayaran!', 'error');
                    alert('Gagal update: ' + (data.message || 'Unknown error'));
                }
            })
            .catch((err) => {
                btn.disabled = false;
                if (typeof showPopup === 'function') showPopup('Gagal update status pembayaran!', 'error');
                alert('AJAX error: ' + err);
                console.log('AJAX error:', err);
            });
    }
</script>