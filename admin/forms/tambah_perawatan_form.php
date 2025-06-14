<?php
// Get list of active customers and their pets
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul");
// Get pesanan_layanan data for grooming services
$pesanan_layanan = query("SELECT pl.*, p.nomor_pesanan, l.nama_layanan 
                         FROM pesanan_layanan pl 
                         JOIN pesanan p ON pl.id_pesanan = p.id_pesanan 
                         JOIN layanan l ON pl.id_layanan = l.id_layanan 
                         WHERE l.kategori_layanan = 'grooming'");
?>

<!-- ID Pesanan Layanan (Required - Foreign Key) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pesanan Layanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_pesanan_layanan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Pesanan Layanan --</option>
            <?php foreach ($pesanan_layanan as $pl): ?>
                <option value="<?= $pl['id_detail'] ?>">
                    <?= $pl['nomor_pesanan'] ?> - <?= $pl['nama_layanan'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Anabul (Required - Foreign Key) -->
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

<!-- Paket Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Paket Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="paket_perawatan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Paket --</option>
            <option value="basic">Basic</option>
            <option value="mix">Mix</option>
            <option value="lengkap">Lengkap</option>
        </select>
    </div>
</div>

<!-- Tanggal Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_perawatan" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Waktu Mulai -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Waktu</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="time" class="form-control" name="waktu_mulai" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Catatan Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_perawatan" style="background-color: #e0e0e0;" rows="3"
            placeholder="Catatan tambahan mengenai perawatan"></textarea>
    </div>
</div>

<!-- Status Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_perawatan" style="background-color: #e0e0e0;" required>
            <option value="scheduled">Scheduled</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>