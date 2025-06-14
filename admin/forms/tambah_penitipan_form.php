<?php
// Get list of active customers and their pets
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul");
?>

<!-- Pelanggan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_pelanggan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id_pelanggan'] ?>">
                    <?= $customer['nama_lengkap'] ?> - <?= $customer['nomor_telepon'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Anabul -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Anabul</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_anabul" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Anabul --</option>
            <?php foreach ($pets as $pet): ?>
                <option value="<?= $pet['id_anabul'] ?>">
                    <?= $pet['nama_hewan'] ?> (<?= $pet['spesies'] ?> - <?= $pet['ras'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Tanggal Check-in -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Check-in</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_checkin" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Tanggal Check-out -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Check-out</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_checkout" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Catatan Khusus -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Khusus</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_khusus" style="background-color: #e0e0e0;" rows="3"></textarea>
    </div>
</div>

<!-- Status -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status" style="background-color: #e0e0e0;" required>
            <option value="pending">Pending</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="dibatalkan">Dibatalkan</option>
        </select>
    </div>
</div>