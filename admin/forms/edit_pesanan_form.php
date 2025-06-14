<?php
// Get list of active customers and products
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$products = query("SELECT * FROM produk");
$services = query("SELECT * FROM layanan");
$doctors = query("SELECT * FROM dokter_hewan");
?>

<!-- Pelanggan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_pelanggan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id_pelanggan'] ?>" <?= ($customer['id_pelanggan'] == $data['id_pelanggan']) ? 'selected' : '' ?>>
                    <?= $customer['nama'] ?> - <?= $customer['nomor_telepon'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Jenis Pesanan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="jenis_pesanan" style="background-color: #e0e0e0;" required>
            <option value="produk" <?= ($data['jenis_pesanan'] == 'produk') ? 'selected' : '' ?>>Produk</option>
            <option value="layanan" <?= ($data['jenis_pesanan'] == 'layanan') ? 'selected' : '' ?>>Layanan</option>
            <option value="konsultasi" <?= ($data['jenis_pesanan'] == 'konsultasi') ? 'selected' : '' ?>>Konsultasi
            </option>
            <option value="perawatan" <?= ($data['jenis_pesanan'] == 'perawatan') ? 'selected' : '' ?>>Perawatan</option>
        </select>
    </div>
</div>

<!-- Detail Pesanan (Dynamic based on jenis_pesanan) -->
<div id="detail-produk" class="detail-section"
    style="display: <?= ($data['jenis_pesanan'] == 'produk') ? 'block' : 'none' ?>;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Produk</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_produk" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id_produk'] ?>" <?= ($product['id_produk'] == $data['id_produk']) ? 'selected' : '' ?>>
                        <?= $product['nama_produk'] ?> - Rp <?= number_format($product['harga'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Jumlah</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="number" class="form-control" name="quantity" style="background-color: #e0e0e0;"
                value="<?= $data['quantity'] ?? '' ?>">
        </div>
    </div>
</div>

<div id="detail-layanan" class="detail-section"
    style="display: <?= ($data['jenis_pesanan'] == 'layanan') ? 'block' : 'none' ?>;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_layanan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Layanan --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['id_layanan'] ?>" <?= ($service['id_layanan'] == $data['id_layanan']) ? 'selected' : '' ?>>
                        <?= $service['nama_layanan'] ?> - Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div id="detail-konsultasi" class="detail-section"
    style="display: <?= ($data['jenis_pesanan'] == 'konsultasi') ? 'block' : 'none' ?>;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Dokter</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_dokter" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Dokter --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['id_dokter'] ?>" <?= ($doctor['id_dokter'] == $data['id_dokter']) ? 'selected' : '' ?>>
                        <?= $doctor['nama_dokter'] ?> - <?= $doctor['spesialisasi'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Konsultasi</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="datetime-local" class="form-control" name="tanggal_konsultasi"
                style="background-color: #e0e0e0;" value="<?= $data['tanggal_konsultasi'] ?? '' ?>">
        </div>
    </div>
</div>

<div id="detail-perawatan" class="detail-section"
    style="display: <?= ($data['jenis_pesanan'] == 'perawatan') ? 'block' : 'none' ?>;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Paket Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="paket_perawatan" style="background-color: #e0e0e0;">
                <option value="Basic" <?= ($data['paket_perawatan'] == 'Basic') ? 'selected' : '' ?>>Basic</option>
                <option value="Premium" <?= ($data['paket_perawatan'] == 'Premium') ? 'selected' : '' ?>>Premium</option>
                <option value="VIP" <?= ($data['paket_perawatan'] == 'VIP') ? 'selected' : '' ?>>VIP</option>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="datetime-local" class="form-control" name="tanggal_perawatan"
                style="background-color: #e0e0e0;" value="<?= $data['tanggal_perawatan'] ?? '' ?>">
        </div>
    </div>
</div>

<!-- Status Pesanan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pesanan" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= ($data['status_pesanan'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= ($data['status_pesanan'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
            <option value="processing" <?= ($data['status_pesanan'] == 'processing') ? 'selected' : '' ?>>Processing
            </option>
            <option value="completed" <?= ($data['status_pesanan'] == 'completed') ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= ($data['status_pesanan'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>
</div>

<!-- Status Pembayaran -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pembayaran</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pembayaran" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= ($data['status_pembayaran'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="paid" <?= ($data['status_pembayaran'] == 'paid') ? 'selected' : '' ?>>Paid</option>
            <option value="failed" <?= ($data['status_pembayaran'] == 'failed') ? 'selected' : '' ?>>Failed</option>
            <option value="refunded" <?= ($data['status_pembayaran'] == 'refunded') ? 'selected' : '' ?>>Refunded</option>
        </select>
    </div>
</div>

<!-- Total Harga -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Total Harga</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="total_harga" style="background-color: #e0e0e0;" required
            value="<?= $data['total_harga'] ?>">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisPesananSelect = document.querySelector('select[name="jenis_pesanan"]');
        const detailSections = document.querySelectorAll('.detail-section');

        function updateDetailSections() {
            const selectedType = jenisPesananSelect.value;
            detailSections.forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(`detail-${selectedType}`).style.display = 'block';
        }

        jenisPesananSelect.addEventListener('change', updateDetailSections);
    });
</script>