<?php
// Get list of active customers, their pets, and doctors
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul");
$doctors = query("SELECT * FROM dokter");
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

<!-- Dokter -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Dokter</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="id_dokter" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Dokter --</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= $doctor['id_dokter'] ?>" <?= ($doctor['id_dokter'] == $data['id_dokter']) ? 'selected' : '' ?>>
                    <?= $doctor['nama_dokter'] ?> (<?= $doctor['spesialisasi'] ?> - Rp
                    <?= number_format($doctor['tarif_konsultasi'], 0, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Tanggal Konsultasi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tanggal Konsultasi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="date" class="form-control" name="tanggal_konsultasi" value="<?= $data['tanggal_konsultasi'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Waktu Konsultasi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Waktu Konsultasi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="time" class="form-control" name="waktu_konsultasi" value="<?= $data['waktu_konsultasi'] ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Gejala Utama -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Gejala Utama</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="gejala_utama" style="background-color: #e0e0e0;" rows="3"
            required><?= $data['gejala_utama'] ?></textarea>
    </div>
</div>

<!-- Perubahan Perilaku -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Perubahan Perilaku</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="perubahan_perilaku" style="background-color: #e0e0e0;"
            rows="3"><?= $data['perubahan_perilaku'] ?></textarea>
    </div>
</div>

<!-- Durasi Gejala -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Durasi Gejala</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="durasi_gejala" style="background-color: #e0e0e0;" required>
            <option value="kurang_1_hari" <?= ($data['durasi_gejala'] == 'kurang_1_hari') ? 'selected' : '' ?>>Kurang dari
                1 hari</option>
            <option value="1_3_hari" <?= ($data['durasi_gejala'] == '1_3_hari') ? 'selected' : '' ?>>1-3 hari</option>
            <option value="4_7_hari" <?= ($data['durasi_gejala'] == '4_7_hari') ? 'selected' : '' ?>>4-7 hari</option>
            <option value="lebih_7_hari" <?= ($data['durasi_gejala'] == 'lebih_7_hari') ? 'selected' : '' ?>>Lebih dari 7
                hari</option>
        </select>
    </div>
</div>

<!-- Tingkat Keparahan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tingkat Keparahan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="tingkat_keparahan" style="background-color: #e0e0e0;" required>
            <option value="ringan" <?= ($data['tingkat_keparahan'] == 'ringan') ? 'selected' : '' ?>>Ringan</option>
            <option value="sedang" <?= ($data['tingkat_keparahan'] == 'sedang') ? 'selected' : '' ?>>Sedang</option>
            <option value="berat" <?= ($data['tingkat_keparahan'] == 'berat') ? 'selected' : '' ?>>Berat</option>
        </select>
    </div>
</div>

<!-- Informasi Tambahan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Informasi Tambahan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="informasi_tambahan" style="background-color: #e0e0e0;"
            rows="3"><?= $data['informasi_tambahan'] ?></textarea>
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