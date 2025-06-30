<!-- Nama -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_lengkap" style="background-color: #e0e0e0;" required
            value="<?= $data["nama_lengkap"] ?>">
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

<!-- Alamat -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Alamat</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="alamat" style="background-color: #e0e0e0;" required
            rows="3"><?= $data["alamat"] ?></textarea>
    </div>
</div>

<!-- Status -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status" style="background-color: #e0e0e0;" required>
            <option value="aktif" <?= ($data["status"] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
            <option value="nonaktif" <?= ($data["status"] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
        </select>
    </div>
</div>