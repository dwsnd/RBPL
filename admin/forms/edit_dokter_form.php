<!-- Foto -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Foto</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="file" class="form-control" name="foto" style="background-color: #e0e0e0;">
        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
        <?php if (!empty($data["foto"])): ?>
            <div class="mt-2">
                <img src="../assets/img/<?= $data["foto"] ?>" alt="Preview" style="max-width: 200px;">
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Nama Dokter -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama Dokter</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_dokter" style="background-color: #e0e0e0;" required
            value="<?= $data["nama_dokter"] ?>">
    </div>
</div>

<!-- Nomor Lisensi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nomor Lisensi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nomor_lisensi" style="background-color: #e0e0e0;" required
            value="<?= $data["nomor_lisensi"] ?>">
    </div>
</div>

<!-- Spesialisasi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Spesialisasi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="spesialisasi" style="background-color: #e0e0e0;" required>
            <option value="Umum" <?= ($data["spesialisasi"] == 'Umum') ? 'selected' : ''; ?>>Umum</option>
            <option value="Bedah" <?= ($data["spesialisasi"] == 'Bedah') ? 'selected' : ''; ?>>Bedah</option>
            <option value="Gigi" <?= ($data["spesialisasi"] == 'Gigi') ? 'selected' : ''; ?>>Gigi</option>
            <option value="Kulit" <?= ($data["spesialisasi"] == 'Kulit') ? 'selected' : ''; ?>>Kulit</option>
            <option value="Mata" <?= ($data["spesialisasi"] == 'Mata') ? 'selected' : ''; ?>>Mata</option>
        </select>
    </div>
</div>

<!-- Email -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Email</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="email" class="form-control" name="email" style="background-color: #e0e0e0;" required
            value="<?= $data["email"] ?>">
    </div>
</div>

<!-- Nomor Telepon -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nomor Telepon</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="tel" class="form-control" name="nomor_telepon" style="background-color: #e0e0e0;" required
            value="<?= $data["nomor_telepon"] ?>">
    </div>
</div>

<!-- Tarif Konsultasi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tarif Konsultasi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="tarif_konsultasi" style="background-color: #e0e0e0;" required
            value="<?= $data["tarif_konsultasi"] ?>">
    </div>
</div>