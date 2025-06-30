<?php
// Get list of active customers
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
?>

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

<!-- Nama Hewan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama Hewan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_hewan" style="background-color: #e0e0e0;" required
            value="<?= $data["nama_hewan"] ?? '' ?>">
    </div>
</div>

<!-- Spesies -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Spesies</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="spesies" style="background-color: #e0e0e0;" required>
            <option value="kucing" <?= (($data["spesies"] ?? '') == 'kucing') ? 'selected' : ''; ?>>Kucing</option>
            <option value="anjing" <?= (($data["spesies"] ?? '') == 'anjing') ? 'selected' : ''; ?>>Anjing</option>
            <option value="hamster" <?= (($data["spesies"] ?? '') == 'hamster') ? 'selected' : ''; ?>>Hamster</option>
            <option value="kelinci" <?= (($data["spesies"] ?? '') == 'kelinci') ? 'selected' : ''; ?>>Kelinci</option>
            <option value="lainnya" <?= (($data["spesies"] ?? '') == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
        </select>
    </div>
</div>

<!-- Ras -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Ras</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="ras" style="background-color: #e0e0e0;"
            value="<?= $data["ras"] ?? '' ?>">
    </div>
</div>

<!-- Umur Tahun & Bulan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Umur</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-4">
        <input type="number" class="form-control" name="umur_tahun" min="0" placeholder="Tahun"
            style="background-color: #e0e0e0;" value="<?= $data["umur_tahun"] ?? '' ?>">
        <small class="text-muted">Tahun</small>
    </div>
    <div class="col-sm-4">
        <input type="number" class="form-control" name="umur_bulan" min="0" max="11" placeholder="Bulan"
            style="background-color: #e0e0e0;" value="<?= $data["umur_bulan"] ?? '' ?>">
        <small class="text-muted">Bulan</small>
    </div>
</div>

<!-- Berat (kg) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Berat (kg)</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" step="0.01" class="form-control" name="berat_kg" style="background-color: #e0e0e0;"
            value="<?= $data["berat_kg"] ?? '' ?>">
    </div>
</div>

<!-- Jenis Kelamin -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Jenis Kelamin</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="jenis_kelamin" style="background-color: #e0e0e0;">
            <option value="">- Pilih -</option>
            <option value="jantan" <?= (($data["jenis_kelamin"] ?? '') == 'jantan') ? 'selected' : ''; ?>>Jantan</option>
            <option value="betina" <?= (($data["jenis_kelamin"] ?? '') == 'betina') ? 'selected' : ''; ?>>Betina</option>
        </select>
    </div>
</div>

<!-- Warna -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Warna</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="warna" style="background-color: #e0e0e0;"
            value="<?= $data["warna"] ?? '' ?>">
    </div>
</div>

<!-- Ciri Khusus -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Ciri Khusus</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="ciri_khusus" style="background-color: #e0e0e0;"
            rows="2"><?= $data["ciri_khusus"] ?? '' ?></textarea>
    </div>
</div>

<!-- Riwayat Penyakit -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Riwayat Penyakit</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="riwayat_penyakit" style="background-color: #e0e0e0;"
            rows="2"><?= $data["riwayat_penyakit"] ?? '' ?></textarea>
    </div>
</div>

<!-- Alergi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Alergi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="alergi" style="background-color: #e0e0e0;"
            rows="2"><?= $data["alergi"] ?? '' ?></textarea>
    </div>
</div>

<!-- Pemilik -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Pemilik</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" value="<?php
        $pemilik = array_filter($customers, function ($c) use ($data) {
            return $c['id_pelanggan'] == ($data['id_pelanggan'] ?? null);
        });
        $pemilik = $pemilik ? array_values($pemilik)[0] : null;
        echo $pemilik ? $pemilik['nama_lengkap'] . ' - ' . $pemilik['nomor_telepon'] : '-';
        ?>" readonly style="background-color: #e0e0e0;">
        <input type="hidden" name="id_pelanggan" value="<?= $data['id_pelanggan'] ?? '' ?>">
    </div>
</div>