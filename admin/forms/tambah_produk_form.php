<!-- Gambar -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Gambar</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="file" class="form-control" name="foto_utama" id="gambar_produk" accept="image/*" required
            onchange="previewImage(this)">
        <div class="mt-2">
            <img id="preview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none;">
        </div>
    </div>
</div>

<!-- Nama Produk -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Nama Produk</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="nama_produk" placeholder="Masukkan nama produk" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Kategori -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Kategori</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="kategori" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="Makanan">Makanan</option>
            <option value="Perawatan">Perawatan</option>
            <option value="Aksesoris">Aksesoris</option>
            <option value="Mainan">Mainan</option>
            <option value="obat">Obat</option>
        </select>
    </div>
</div>

<!-- Sub Kategori -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Sub Kategori</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="text" class="form-control" name="sub_kategori" placeholder="Masukkan sub kategori (opsional)" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Target Hewan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Target Hewan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="target_hewan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Target Hewan --</option>
            <option value="Kucing">Kucing</option>
            <option value="Anjing">Anjing</option>
            <option value="Hamster">Hamster</option>
            <option value="Kelinci">Kelinci</option>
        </select>
    </div>
</div>

<!-- Harga -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Harga</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="harga" placeholder="Masukkan harga" min="0" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Stok -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Stok</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="stok" placeholder="Masukkan jumlah stok" min="0" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Berat (gram) -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Berat (gram)</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="berat_gram" placeholder="Masukkan berat dalam gram" min="0" style="background-color: #e0e0e0;" required>
    </div>
</div>

<!-- Deskripsi -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Deskripsi</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="deskripsi" placeholder="Masukkan deskripsi produk" rows="4" style="background-color: #e0e0e0;" required></textarea>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>