<?php
// Get list of active customers and their pets
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul");
?>

<!-- ID Penitipan (hidden) -->
<input type="hidden" name="id" value="<?= $data['id_penitipan'] ?? '' ?>">

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

<!-- Tanggal Check-in -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Check-in</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_checkin" value="<?= $data['tanggal_checkin'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Tanggal Check-out -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Check-out</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_checkout" value="<?= $data['tanggal_checkout'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Catatan Khusus -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Khusus</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_khusus" style="background-color: #e0e0e0;"
            rows="3"><?= $data['catatan_checkin'] ?></textarea>
    </div>
</div>

<!-- Status -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= ($data['status_checkin'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="diproses" <?= ($data['status_checkin'] == 'diproses' || $data['status_checkin'] == 'checked_in') ? 'selected' : '' ?>>Check-in</option>
            <option value="selesai" <?= ($data['status_checkin'] == 'selesai' || $data['status_checkin'] == 'checked_out') ? 'selected' : '' ?>>Check-out</option>
            <option value="dibatalkan" <?= ($data['status_checkin'] == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan
            </option>
        </select>
    </div>
</div>

<!-- Nomor Kandang -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nomor Kandang</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nomor_kandang"
            value="<?= htmlspecialchars($data['nomor_kandang'] ?? '') ?>" style="background-color: #e0e0e0;">
    </div>
</div>