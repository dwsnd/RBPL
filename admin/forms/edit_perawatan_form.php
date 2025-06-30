<?php
// Get list of active customers and their pets
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul WHERE status = 'aktif'");
?>

<!-- Pelanggan (readonly) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control"
            value="<?= htmlspecialchars(($data['nama_lengkap'] ?? '') . ' - ' . ($data['nomor_telepon'] ?? '')) ?>"
            readonly style="background-color: #e0e0e0;">
        <input type="hidden" name="id_pelanggan" value="<?= $data['id_pelanggan'] ?? '' ?>">
    </div>
</div>

<!-- Anabul (readonly) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Anabul</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control"
            value="<?= htmlspecialchars(($data['nama_hewan'] ?? '') . ' (' . ($data['spesies'] ?? '') . ' - ' . ($data['ras'] ?? '') . ')') ?>"
            readonly style="background-color: #e0e0e0;">
        <input type="hidden" name="id_anabul" value="<?= $data['id_anabul'] ?? '' ?>">
    </div>
</div>

<!-- Jenis Layanan (readonly) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Layanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_layanan'] ?? '') ?>" readonly
            style="background-color: #e0e0e0;">
    </div>
</div>

<!-- Tanggal Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_perawatan" value="<?= $data['tanggal_perawatan'] ?? '' ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Waktu Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Waktu Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="time" class="form-control" name="waktu_mulai" value="<?= $data['waktu_mulai'] ?? '' ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Catatan Khusus -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Khusus</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan" style="background-color: #e0e0e0;"
            rows="3"><?= $data['catatan'] ?? '' ?></textarea>
    </div>
</div>

<!-- Status -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pesanan" style="background-color: #e0e0e0;" required>
            <option value="scheduled" <?= ($data['status_pesanan'] == 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
            <option value="in_progress" <?= ($data['status_pesanan'] == 'in_progress') ? 'selected' : '' ?>>In Progress
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
            <option value="paid" <?= ($data['status_pembayaran'] == 'paid') ? 'selected' : '' ?>>Paid</option>
            <option value="pending" <?= ($data['status_pembayaran'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="failed" <?= ($data['status_pembayaran'] == 'failed') ? 'selected' : '' ?>>Failed</option>
            <option value="refunded" <?= ($data['status_pembayaran'] == 'refunded') ? 'selected' : '' ?>>Refunded</option>
        </select>
    </div>
</div>