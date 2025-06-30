<?php
// Get list of active customers, their pets, and doctors
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$pets = query("SELECT * FROM anabul WHERE status = 'aktif'");
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
                <option value="<?= $pet['id_anabul'] ?>" <?= ($pet['id_anabul'] == $data['id_anabul']) ? 'selected' : '' ?>>
                    <?= $pet['nama_hewan'] ?> (<?= $pet['spesies'] ?> - <?= $pet['ras'] ?>)
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
        <input type="date" class="form-control" name="tanggal_konsultasi" value="<?= $data['tanggal_kontrol'] ?? '' ?>"
            style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Gejala -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Gejala</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="gejala" style="background-color: #e0e0e0;" rows="3"
            required><?= $data['gejala'] ?? '' ?></textarea>
    </div>
</div>

<!-- Keluhan Utama -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Keluhan Utama</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="keluhan_utama" style="background-color: #e0e0e0;" rows="3"
            required><?= $data['keluhan_utama'] ?? '' ?></textarea>
    </div>
</div>

<!-- Durasi Gejala -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Durasi Gejala</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="durasi_gejala" value="<?= $data['durasi_gejala'] ?? '' ?>"
            style="background-color: #e0e0e0;" required>
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
            <option value="darurat" <?= ($data['tingkat_keparahan'] == 'darurat') ? 'selected' : '' ?>>Darurat</option>
        </select>
    </div>
</div>

<!-- Status Konsultasi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Konsultasi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status" style="background-color: #e0e0e0;" required>
            <option value="scheduled" <?= ($data['status_konsultasi'] == 'scheduled') ? 'selected' : '' ?>>Terjadwal
            </option>
            <option value="ongoing" <?= ($data['status_konsultasi'] == 'ongoing') ? 'selected' : '' ?>>Sedang Berlangsung
            </option>
            <option value="completed" <?= ($data['status_konsultasi'] == 'completed') ? 'selected' : '' ?>>Selesai</option>
            <option value="cancelled" <?= ($data['status_konsultasi'] == 'cancelled') ? 'selected' : '' ?>>Dibatalkan
            </option>
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