<?php
// Get list of active customers and products
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$products = query("SELECT * FROM produk");
$services = query("SELECT * FROM layanan");
$doctors = query("SELECT * FROM dokter_hewan");

// Helper function to safely get field value
function getFieldValue($data, $field, $default = '')
{
    return isset($data[$field]) ? $data[$field] : $default;
}

// Get detailed order information based on order type
$order_id = getFieldValue($data, 'id_pesanan');
$jenis_pesanan = getFieldValue($data, 'jenis_pesanan');

// Get additional details based on order type
$order_details = [];
if ($order_id && $jenis_pesanan) {
    switch ($jenis_pesanan) {
        case 'produk':
            $order_details = query("SELECT pp.*, p.nama_produk, p.harga 
                                   FROM pesanan_produk pp 
                                   JOIN produk p ON pp.id_produk = p.id_produk 
                                   WHERE pp.id_pesanan = $order_id");
            break;
        case 'penitipan':
        case 'perawatan':
            $order_details = query("SELECT pl.*, l.nama_layanan, l.harga 
                                   FROM pesanan_layanan pl 
                                   JOIN layanan l ON pl.id_layanan = l.id_layanan 
                                   WHERE pl.id_pesanan = $order_id");
            break;
        case 'konsultasi':
            $order_details = query("SELECT pl.*, l.nama_layanan, l.harga, d.nama_dokter, d.spesialisasi 
                                   FROM pesanan_layanan pl 
                                   JOIN layanan l ON pl.id_layanan = l.id_layanan 
                                   JOIN dokter_hewan d ON l.id_dokter = d.id_dokter 
                                   WHERE pl.id_pesanan = $order_id");
            break;
    }
}
?>

<!-- Pelanggan (Read-only) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <?php
        $pelanggan_info = '';
        foreach ($customers as $customer) {
            if ($customer['id_pelanggan'] == getFieldValue($data, 'id_pelanggan')) {
                $pelanggan_info = $customer['nama_lengkap'] . ' - ' . $customer['nomor_telepon'];
                break;
            }
        }
        ?>
        <input type="text" class="form-control" value="<?= $pelanggan_info ?>" readonly
            style="background-color: #f8f9fa;">
    </div>
</div>

<!-- Jenis Pesanan (Read-only) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" value="<?= ucfirst(getFieldValue($data, 'jenis_pesanan')) ?>" readonly
            style="background-color: #f8f9fa;">
    </div>
</div>

<!-- Detail Pesanan (Read-only based on jenis_pesanan) -->
<div id="detail-produk" class="detail-section"
    style="display: <?= (getFieldValue($data, 'jenis_pesanan') == 'produk') ? 'block' : 'none' ?>;">
    <?php if (!empty($order_details)): ?>
        <?php foreach ($order_details as $detail): ?>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Produk</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        value="<?= $detail['nama_produk'] . ' - Rp ' . number_format($detail['harga_satuan'], 0, ',', '.') ?>"
                        readonly style="background-color: #f8f9fa;">
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Jumlah</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value="<?= $detail['quantity'] ?>" readonly
                        style="background-color: #f8f9fa;">
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="detail-penitipan" class="detail-section"
    style="display: <?= (getFieldValue($data, 'jenis_pesanan') == 'penitipan') ? 'block' : 'none' ?>;">
    <?php if (!empty($order_details)): ?>
        <?php foreach ($order_details as $detail): ?>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Layanan Penitipan</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        value="<?= $detail['nama_layanan'] . ' - Rp ' . number_format($detail['harga_layanan'], 0, ',', '.') ?>"
                        readonly style="background-color: #f8f9fa;">
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'tanggal_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Waktu Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'waktu_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
</div>

<div id="detail-perawatan" class="detail-section"
    style="display: <?= (getFieldValue($data, 'jenis_pesanan') == 'perawatan') ? 'block' : 'none' ?>;">
    <?php if (!empty($order_details)): ?>
        <?php foreach ($order_details as $detail): ?>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Layanan Perawatan</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        value="<?= $detail['nama_layanan'] . ' - Rp ' . number_format($detail['harga_layanan'], 0, ',', '.') ?>"
                        readonly style="background-color: #f8f9fa;">
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'tanggal_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Waktu Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'waktu_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
</div>

<div id="detail-konsultasi" class="detail-section"
    style="display: <?= (getFieldValue($data, 'jenis_pesanan') == 'konsultasi') ? 'block' : 'none' ?>;">
    <?php if (!empty($order_details)): ?>
        <?php foreach ($order_details as $detail): ?>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Dokter</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        value="<?= $detail['nama_dokter'] . ' - ' . $detail['spesialisasi'] ?>" readonly
                        style="background-color: #f8f9fa;">
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <div class="col-sm-3 text-start">Layanan Konsultasi</div>
                <div class="col-sm-1 text-end">:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control"
                        value="<?= $detail['nama_layanan'] . ' - Rp ' . number_format($detail['harga_layanan'], 0, ',', '.') ?>"
                        readonly style="background-color: #f8f9fa;">
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'tanggal_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Waktu Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="<?= getFieldValue($data, 'waktu_layanan') ?>" readonly
                style="background-color: #f8f9fa;">
        </div>
    </div>
</div>

<!-- Total Harga (Read-only) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Total Harga</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control"
            value="Rp <?= number_format(getFieldValue($data, 'total_harga'), 0, ',', '.') ?>" readonly
            style="background-color: #f8f9fa;">
    </div>
</div>

<!-- Catatan Pelanggan (Read-only) -->
<?php if (getFieldValue($data, 'catatan_pelanggan')): ?>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Catatan Pelanggan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <textarea class="form-control" rows="3" readonly
                style="background-color: #f8f9fa;"><?= getFieldValue($data, 'catatan_pelanggan') ?></textarea>
        </div>
    </div>
<?php endif; ?>

<!-- Status Pesanan (Editable) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pesanan" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= (getFieldValue($data, 'status_pesanan') == 'pending') ? 'selected' : '' ?>>Pending
            </option>
            <option value="confirmed" <?= (getFieldValue($data, 'status_pesanan') == 'confirmed') ? 'selected' : '' ?>>
                Confirmed</option>
            <option value="processing" <?= (getFieldValue($data, 'status_pesanan') == 'processing') ? 'selected' : '' ?>>
                Processing</option>
            <option value="completed" <?= (getFieldValue($data, 'status_pesanan') == 'completed') ? 'selected' : '' ?>>
                Completed</option>
            <option value="cancelled" <?= (getFieldValue($data, 'status_pesanan') == 'cancelled') ? 'selected' : '' ?>>
                Cancelled</option>
        </select>
    </div>
</div>

<!-- Status Pembayaran (Editable) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pembayaran</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pembayaran" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= (getFieldValue($data, 'status_pembayaran') == 'pending') ? 'selected' : '' ?>>
                Pending</option>
            <option value="paid" <?= (getFieldValue($data, 'status_pembayaran') == 'paid') ? 'selected' : '' ?>>Paid
            </option>
            <option value="failed" <?= (getFieldValue($data, 'status_pembayaran') == 'failed') ? 'selected' : '' ?>>Failed
            </option>
            <option value="refunded" <?= (getFieldValue($data, 'status_pembayaran') == 'refunded') ? 'selected' : '' ?>>
                Refunded</option>
        </select>
    </div>
</div>

<!-- Catatan Admin (Editable) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Admin</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_admin" rows="3"
            placeholder="Tambahkan catatan admin jika diperlukan"><?= getFieldValue($data, 'catatan_admin') ?></textarea>
    </div>
</div>