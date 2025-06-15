<?php
// Get list of active customers
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
?>

<!-- Foto -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Foto</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="file" class="form-control" name="foto" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Nama Hewan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama Hewan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_hewan" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Jenis Hewan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Hewan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="jenis_hewan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Jenis Hewan --</option>
            <option value="Kucing">Kucing</option>
            <option value="Anjing">Anjing</option>
            <option value="Hamster">Hamster</option>
            <option value="Kelinci">Kelinci</option>
        </select>
    </div>
</div>

<!-- Ras -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Ras</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="ras" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Umur -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Umur (bulan)</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="umur" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Berat -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Berat (kg)</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" step="0.1" class="form-control" name="berat" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Pemilik -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pemilik</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_pelanggan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Pemilik --</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id_pelanggan'] ?>">
                    <?= $customer['nama'] ?> - <?= $customer['nomor_telepon'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>