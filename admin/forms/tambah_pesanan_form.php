<?php
// Get list of active customers and products
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$products = query("SELECT * FROM produk");
$services = query("SELECT * FROM layanan");
$doctors = query("SELECT * FROM dokter_hewan");
?>

<!-- Tipe Pelanggan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Tipe Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" id="tipe_pelanggan" name="tipe_pelanggan" style="background-color: #e0e0e0;"
            required>
            <option value="">-- Pilih Tipe Pelanggan --</option>
            <option value="existing">Pelanggan Terdaftar</option>
            <option value="new">Pelanggan Baru</option>
        </select>
    </div>
</div>

<!-- Form Pelanggan Terdaftar -->
<div id="existing-customer-form" style="display: none;">
    <!-- Pelanggan -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Pelanggan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" id="id_pelanggan" name="id_pelanggan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['id_pelanggan'] ?>">
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
            <select class="form-control" id="id_anabul" name="id_anabul" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Anabul --</option>
            </select>
            <small class="text-muted">Atau isi manual di bawah</small>
        </div>
    </div>
</div>

<!-- Form Pelanggan Baru -->
<div id="new-customer-form" style="display: none;">
    <!-- Nama Lengkap -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Nama Lengkap</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="nama_lengkap" style="background-color: #e0e0e0;">
        </div>
    </div>

    <!-- Nomor Telepon -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Nomor Telepon</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="nomor_telepon" style="background-color: #e0e0e0;">
        </div>
    </div>

    <!-- Alamat -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Alamat</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <textarea class="form-control" name="alamat" style="background-color: #e0e0e0;"></textarea>
        </div>
    </div>
</div>

<!-- Form Anabul Manual -->
<div id="manual-anabul-form" class="mt-3">
    <h5>Data Anabul</h5>
    <!-- Nama Anabul -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Nama Anabul</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="nama_anabul" style="background-color: #e0e0e0;">
        </div>
    </div>

    <!-- Jenis Anabul -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Jenis Anabul</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="jenis_anabul" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Jenis --</option>
                <option value="Kucing">Kucing</option>
                <option value="Anjing">Anjing</option>
                <option value="Kelinci">Kelinci</option>
                <option value="Hamster">Hamster</option>
                <option value="Burung">Burung</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Ras -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Ras</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="ras_anabul" style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<!-- Jenis Pesanan -->
<div class="row mb-3 align-items-center mt-4">
    <div class="col-sm-3 text-start">Jenis Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="jenis_pesanan" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Jenis Pesanan --</option>
            <option value="produk">Produk</option>
            <option value="layanan">Layanan</option>
            <option value="konsultasi">Konsultasi</option>
            <option value="perawatan">Perawatan</option>
        </select>
    </div>
</div>

<!-- Detail Pesanan (Dynamic based on jenis_pesanan) -->
<div id="detail-produk" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Produk</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_produk" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id_produk'] ?>">
                        <?= $product['nama_produk'] ?> - Rp <?= number_format($product['harga'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Jumlah</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="number" class="form-control" name="quantity" style="background-color: #e0e0e0;" min="1">
        </div>
    </div>
</div>

<div id="detail-layanan" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Layanan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_layanan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Layanan --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['id_layanan'] ?>">
                        <?= $service['nama_layanan'] ?> - Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div id="detail-konsultasi" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Dokter</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_dokter" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Dokter --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['id_dokter'] ?>">
                        <?= $doctor['nama_dokter'] ?> - <?= $doctor['spesialisasi'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Konsultasi</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="datetime-local" class="form-control" name="tanggal_konsultasi"
                style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<div id="detail-perawatan" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Paket Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="paket_perawatan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Paket --</option>
                <option value="Basic">Basic</option>
                <option value="Premium">Premium</option>
                <option value="VIP">VIP</option>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="datetime-local" class="form-control" name="tanggal_perawatan"
                style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<!-- Status Pesanan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pesanan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pesanan" style="background-color: #e0e0e0;" required>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<!-- Status Pembayaran -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Status Pembayaran</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="status_pembayaran" style="background-color: #e0e0e0;" required>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
            <option value="refunded">Refunded</option>
        </select>
    </div>
</div>

<!-- Total Harga -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Total Harga</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <input type="number" class="form-control" name="total_harga" style="background-color: #e0e0e0;" required>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle customer type selection
        const tipePelangganSelect = document.getElementById('tipe_pelanggan');
        const existingCustomerForm = document.getElementById('existing-customer-form');
        const newCustomerForm = document.getElementById('new-customer-form');
        const manualAnabulForm = document.getElementById('manual-anabul-form');

        tipePelangganSelect.addEventListener('change', function () {
            if (this.value === 'existing') {
                existingCustomerForm.style.display = 'block';
                newCustomerForm.style.display = 'none';
            } else if (this.value === 'new') {
                existingCustomerForm.style.display = 'none';
                newCustomerForm.style.display = 'block';
            } else {
                existingCustomerForm.style.display = 'none';
                newCustomerForm.style.display = 'none';
            }
        });

        // Handle customer selection and load their pets
        const customerSelect = document.getElementById('id_pelanggan');
        const anabulSelect = document.getElementById('id_anabul');

        customerSelect.addEventListener('change', function () {
            if (this.value) {
                // Fetch pets for selected customer using AJAX
                fetch(`get_pets.php?customer_id=${this.value}`)
                    .then(response => response.json())
                    .then(pets => {
                        anabulSelect.innerHTML = '<option value="">-- Pilih Anabul --</option>';
                        pets.forEach(pet => {
                            anabulSelect.innerHTML += `
                            <option value="${pet.id_anabul}">
                                ${pet.nama_anabul} - ${pet.jenis_anabul} (${pet.ras_anabul})
                            </option>
                        `;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Handle order type selection
        const jenisPesananSelect = document.querySelector('select[name="jenis_pesanan"]');
        const detailSections = document.querySelectorAll('.detail-section');

        function updateDetailSections() {
            const selectedType = jenisPesananSelect.value;
            detailSections.forEach(section => {
                section.style.display = 'none';
            });
            if (selectedType) {
                document.getElementById(`detail-${selectedType}`).style.display = 'block';
            }
        }

        jenisPesananSelect.addEventListener('change', updateDetailSections);

        // Calculate total price when product or quantity changes
        const productSelect = document.querySelector('select[name="id_produk"]');
        const quantityInput = document.querySelector('input[name="quantity"]');
        const totalInput = document.querySelector('input[name="total_harga"]');

        function calculateTotal() {
            if (productSelect && quantityInput && totalInput) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption.value) {
                    const price = parseInt(selectedOption.text.split('Rp ')[1].replace(/\./g, ''));
                    const quantity = parseInt(quantityInput.value) || 0;
                    totalInput.value = price * quantity;
                }
            }
        }

        if (productSelect) productSelect.addEventListener('change', calculateTotal);
        if (quantityInput) quantityInput.addEventListener('input', calculateTotal);
    });
</script>