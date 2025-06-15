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
                <option value="<?= $customer['id_pelanggan'] ?>" <?= ($customer['id_pelanggan'] == $data['id_pelanggan']) ? 'selected' : '' ?>>
                    <?= $customer['nama'] ?> - <?= $customer['nomor_telepon'] ?>
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
                <option value="<?= $pet['id_anabul'] ?>" <?= ($pet['id_anabul'] == $data['id_anabul']) ? 'selected' : '' ?>>
                    <?= $pet['nama_hewan'] ?> (<?= $pet['jenis_hewan'] ?> - <?= $pet['ras'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Jenis Layanan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Layanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="jenis_layanan" style="background-color: #e0e0e0;" required>
            <option value="grooming" <?= ($data['jenis_layanan'] == 'grooming') ? 'selected' : '' ?>>Grooming</option>
            <option value="mandi" <?= ($data['jenis_layanan'] == 'mandi') ? 'selected' : '' ?>>Mandi</option>
            <option value="potong_kuku" <?= ($data['jenis_layanan'] == 'potong_kuku') ? 'selected' : '' ?>>Potong Kuku
            </option>
            <option value="potong_bulu" <?= ($data['jenis_layanan'] == 'potong_bulu') ? 'selected' : '' ?>>Potong Bulu
            </option>
            <option value="spa" <?= ($data['jenis_layanan'] == 'spa') ? 'selected' : '' ?>>Spa</option>
        </select>
    </div>
</div>

<!-- Tanggal Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_perawatan" value="<?= $data['tanggal_perawatan'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Waktu Perawatan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Waktu Perawatan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="time" class="form-control" name="waktu_perawatan" value="<?= $data['waktu_perawatan'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Catatan Khusus -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Khusus</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_khusus" style="background-color: #e0e0e0;"
            rows="3"><?= $data['catatan_khusus'] ?></textarea>
    </div>
</div>

<!-- Status -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status" style="background-color: #e0e0e0;" required>
            <option value="pending" <?= ($data['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="diproses" <?= ($data['status'] == 'diproses') ? 'selected' : '' ?>>Diproses</option>
            <option value="selesai" <?= ($data['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
            <option value="dibatalkan" <?= ($data['status'] == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
        </select>
    </div>
</div>