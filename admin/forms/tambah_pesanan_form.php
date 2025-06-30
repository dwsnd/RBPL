<?php
// Get list of active customers and products
$customers = query("SELECT * FROM pelanggan WHERE status = 'aktif'");
$products = query("SELECT * FROM produk WHERE status = 'aktif'");
$services = query("SELECT * FROM layanan WHERE status = 'aktif'");
$doctors = query("SELECT * FROM dokter_hewan");
$anabul = query("SELECT * FROM anabul WHERE status = 'aktif'");
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
            <option value="penitipan">Penitipan</option>
            <option value="perawatan">Perawatan</option>
            <option value="konsultasi">Konsultasi</option>
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
                    <option value="<?= $product['id_produk'] ?>" data-harga="<?= $product['harga'] ?>">
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
            <input type="number" class="form-control" name="quantity" style="background-color: #e0e0e0;" min="1"
                value="1">
        </div>
    </div>
</div>

<div id="detail-penitipan" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Layanan Penitipan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_layanan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Layanan Penitipan --</option>
                <?php foreach ($services as $service): ?>
                    <?php if ($service['jenis_layanan'] == 'penitipan'): ?>
                        <option value="<?= $service['id_layanan'] ?>" data-harga="<?= $service['harga'] ?>">
                            <?= $service['nama_layanan'] ?> - Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Check-in</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="date" class="form-control" name="tanggal_checkin" style="background-color: #e0e0e0;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Check-out</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="date" class="form-control" name="tanggal_checkout" style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<div id="detail-perawatan" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Layanan Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_layanan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Layanan Perawatan --</option>
                <?php foreach ($services as $service): ?>
                    <?php if ($service['jenis_layanan'] == 'perawatan'): ?>
                        <option value="<?= $service['id_layanan'] ?>" data-harga="<?= $service['harga'] ?>">
                            <?= $service['nama_layanan'] ?> - Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Tanggal Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="date" class="form-control" name="tanggal_perawatan" style="background-color: #e0e0e0;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Waktu Perawatan</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="time" class="form-control" name="waktu_perawatan" style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<div id="detail-konsultasi" class="detail-section" style="display: none;">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Layanan Konsultasi</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <select class="form-control" name="id_layanan" style="background-color: #e0e0e0;">
                <option value="">-- Pilih Layanan Konsultasi --</option>
                <?php foreach ($services as $service): ?>
                    <?php if ($service['jenis_layanan'] == 'konsultasi'): ?>
                        <option value="<?= $service['id_layanan'] ?>" data-harga="<?= $service['harga'] ?>">
                            <?= $service['nama_layanan'] ?> - Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
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
            <input type="date" class="form-control" name="tanggal_konsultasi" style="background-color: #e0e0e0;">
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-start">Waktu Konsultasi</div>
        <div class="col-sm-1 text-end">:</div>
        <div class="col-sm-8">
            <input type="time" class="form-control" name="waktu_konsultasi" style="background-color: #e0e0e0;">
        </div>
    </div>
</div>

<!-- Metode Pembayaran -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Metode Pembayaran</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <select class="form-control" name="metode_pembayaran" style="background-color: #e0e0e0;" required>
            <option value="">-- Pilih Metode Pembayaran --</option>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
            <option value="debit">Debit</option>
            <option value="credit">Credit</option>
            <option value="ewallet">E-Wallet</option>
        </select>
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
            <option value="completed" selected>Completed</option>
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
        <input type="number" class="form-control" name="total_harga" style="background-color: #e0e0e0;" required
            readonly>
    </div>
</div>

<!-- Catatan Pelanggan -->
<div class="row mb-3 align-items-center">
    <div class="col-sm-3 text-start">Catatan Pelanggan</div>
    <div class="col-sm-1 text-end">:</div>
    <div class="col-sm-8">
        <textarea class="form-control" name="catatan_pelanggan" rows="3" style="background-color: #e0e0e0;"
            placeholder="Catatan dari pelanggan (opsional)"></textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle customer type selection
        const tipePelangganSelect = document.getElementById('tipe_pelanggan');
        const existingCustomerForm = document.getElementById('existing-customer-form');
        const newCustomerForm = document.getElementById('new-customer-form');

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

        // Pre-load all pets data
        const allPets = <?= json_encode($anabul) ?>;

        customerSelect.addEventListener('change', function () {
            if (this.value) {
                // Filter pets for selected customer
                const customerPets = allPets.filter(pet => pet.id_pelanggan == this.value);

                anabulSelect.innerHTML = '<option value="">-- Pilih Anabul --</option>';
                customerPets.forEach(pet => {
                    anabulSelect.innerHTML += `
                        <option value="${pet.id_anabul}">
                            ${pet.nama_hewan} - ${pet.jenis_hewan} (${pet.ras})
                        </option>
                    `;
                });
            } else {
                anabulSelect.innerHTML = '<option value="">-- Pilih Anabul --</option>';
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

        // Calculate total price when product/quantity or service changes
        const productSelect = document.querySelector('select[name="id_produk"]');
        const quantityInput = document.querySelector('input[name="quantity"]');
        const serviceSelects = document.querySelectorAll('select[name="id_layanan"]');
        const totalInput = document.querySelector('input[name="total_harga"]');

        function calculateTotal() {
            let total = 0;

            // Calculate product total
            if (productSelect && quantityInput) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption.value) {
                    const price = parseInt(selectedOption.dataset.harga);
                    const quantity = parseInt(quantityInput.value) || 0;
                    total = price * quantity;
                }
            }

            // Calculate service total
            serviceSelects.forEach(select => {
                if (select.style.display !== 'none') {
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption.value) {
                        total = parseInt(selectedOption.dataset.harga);
                    }
                }
            });

            if (totalInput) {
                totalInput.value = total;
            }
        }

        if (productSelect) productSelect.addEventListener('change', calculateTotal);
        if (quantityInput) quantityInput.addEventListener('input', calculateTotal);
        serviceSelects.forEach(select => {
            select.addEventListener('change', calculateTotal);
        });
    });
</script>