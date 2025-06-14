<!-- Gambar -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Gambar</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="file" class="form-control" name="foto_utama" style="background-color: #e0e0e0;">
        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
        <?php if (!empty($data["foto_utama"])): ?>
            <div class="mt-2">
                <img src="../<?= $data["foto_utama"] ?>" alt="Preview" style="max-width: 200px;">
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Nama Produk -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama Produk</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_produk" style="background-color: #e0e0e0;" required
            value="<?= $data["nama_produk"] ?>">
    </div>
</div>

<!-- Kategori -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Kategori</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="kategori" style="background-color: #e0e0e0;" required>
            <option value="Makanan" <?= ($data["kategori"] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
            <option value="Perawatan" <?= ($data["kategori"] == 'Perawatan') ? 'selected' : ''; ?>>Perawatan</option>
            <option value="Aksesoris" <?= ($data["kategori"] == 'Aksesoris') ? 'selected' : ''; ?>>Aksesoris</option>
            <option value="Mainan" <?= ($data["kategori"] == 'Mainan') ? 'selected' : ''; ?>>Mainan</option>
        </select>
    </div>
</div>

<!-- Sub Kategori -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Sub Kategori</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="sub_kategori" style="background-color: #e0e0e0;" required
            value="<?= $data["sub_kategori"] ?>">
    </div>
</div>

<!-- Target Hewan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Target Hewan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="target_hewan" style="background-color: #e0e0e0;" required>
            <option value="Kucing" <?= ($data["target_hewan"] == 'Kucing') ? 'selected' : ''; ?>>Kucing</option>
            <option value="Anjing" <?= ($data["target_hewan"] == 'Anjing') ? 'selected' : ''; ?>>Anjing</option>
            <option value="Hamster" <?= ($data["target_hewan"] == 'Hamster') ? 'selected' : ''; ?>>Hamster</option>
            <option value="Kelinci" <?= ($data["target_hewan"] == 'Kelinci') ? 'selected' : ''; ?>>Kelinci</option>
        </select>
    </div>
</div>

<!-- Harga -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Harga</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="harga" style="background-color: #e0e0e0;" required
            value="<?= $data["harga"] ?>">
    </div>
</div>

<!-- Stok -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Stok</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="stok" style="background-color: #e0e0e0;" required
            value="<?= $data["stok"] ?>">
    </div>
</div>

<!-- Berat (gram) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Berat (gram)</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="berat_gram" style="background-color: #e0e0e0;" required
            value="<?= $data["berat_gram"] ?>">
    </div>
</div>

<!-- Deskripsi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Deskripsi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="deskripsi" style="background-color: #e0e0e0;" required
            rows="4"><?= $data["deskripsi"] ?></textarea>
    </div>
</div>