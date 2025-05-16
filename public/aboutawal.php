<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Hero Section */
        .hero-section {
            background: #fff;
            padding: 65px 0;
        }

        .hero-text {
            font-size: 2.5rem;
            font-weight: 750;
        }

        .nama-toko {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .hero-section .container {
            min-height: 460px;
            position: relative;
            z-index: 2;
        }

        .shape-main {
            position: absolute;
            right: 40px;
            top: 13%;
            width: 50%;
            z-index: 1;
        }

        .image-catdog {
            position: absolute;
            right: 40px;
            top: 14%;
            width: 50%;
            z-index: 2;
            transition: transform 0.3s ease;
        }

        .image-catdog:hover {
            transform: scale(1.02);
        }

        .shape-leftup {
            position: absolute;
            left: 15%;
            top: -12%;
            width: 13%;
            z-index: 1;
        }

        .shape-leftdown {
            position: absolute;
            left: 25%;
            bottom: 5%;
            width: 11%;
            z-index: 1;
        }

        .btn-black {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-black:hover {
            background: #333;
            color: #fff;
        }

        .layanan-icon img,
        .produk-icon img {
            max-height: 80px;
        }

        .section-title {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 40px 0 20px;
        }

        footer {
            padding: 40px 0;
            background-color: #f8f9fa;
        }

        .cat-img {
            width: 100%;
            max-width: 1000px;
            display: block;
            margin: auto;
        }

        .produk-card {
            text-align: center;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .produk-card img {
            max-height: 120px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden">
        <!-- SHAPE BESAR KANAN -->
        <img src="../aset/Shape2.png" class="shape-main" alt="Shape">
        <!-- SHAPE KECIL KIRI ATAS -->
        <img src="../aset/Shape.png" class="shape-leftup" alt="Shape2">
        <!-- SHAPE KECIL KIRI BAWAH -->
        <img src="../aset/Shape1.png" class="shape-leftdown" alt="Shape1">
        <div class="container d-flex flex-wrap align-items-center justify-content-between position-relative"
            style="z-index:2;">
            <div class="col-lg-6 mb-4 text-lg-start text-center">
                <h6 class="nama-toko text-warning">Ling-Ling Pet Shop</h6>
                <h1 class="hero-text mb-3">Jika Hewan Bisa Berbicara,<br>Mereka Akan Berbicara<br> Tentang Kita!</h1>
                <a href="#" class="btn btn-black mt-2">Mulai Belanja</a>
            </div>
        </div>
        <img src="../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- desc -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Deskripsi dan Form -->
                <div class="col-md-6 mb-4">
                    <h5 class="fw-bold">Tentang Kami</h5>
                    <p class="text-muted">
                        Kami hadir sebagai solusi lengkap untuk kebutuhan hewan peliharaan Anda di Kabupaten Bantul.
                        Dari pakan, aksesoris, hingga perlengkapan grooming dan kesehatan, semua tersedia dengan harga
                        terjangkau dan kualitas terjamin.
                    </p>

                    <form class="bg-light p-4 rounded">
                        <div class="row mb-3">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Nama Depan">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Nama Belakang">
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Alamat Email">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="3" placeholder="Pesan Anda..."></textarea>
                        </div>
                        <button class="btn btn-warning w-100 mb-3">Kirim Pesan</button>
                        <div class="text-center text-warning">
                            ★ ★ ★ ★ ★
                        </div>
                    </form>
                </div>

                <!-- Kontak -->
                <div class="col-md-6">
                    <p class="text-muted">
                        Temukan produk terbaik untuk hewan kesayangan Anda di toko kami.
                        Kunjungi langsung atau hubungi kami untuk informasi stok, promo, dan penawaran spesial!
                    </p>
                    <h6 class="fw-bold">Jangan ragu menghubungi kami</h6>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2">
                            <i class="fa-solid fa-location-dot text-warning me-2"></i>
                            <strong>Jl. Parangtritis KM 6 Jetis, Panggungharjo, Sewon, Bantul</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-envelope text-warning me-2"></i>
                            linglingpetshop@gmail.com
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-phone text-warning me-2"></i>
                            +62 838-6705-6070
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-clock text-warning me-2"></i>
                            Senin–Sabtu: 9AM – 9PM
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <!-- testi -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-md-6">
                    <p class="text-warning fw-bold mb-1">Testimoni</p>
                    <h5 class="fw-bold">Apa kata mereka tentang kami</h5>
                    <div class="text-warning mb-2">
                        ★ ★ ★ ★ ★
                    </div>
                    <p class="text-muted">
                        pet shop & ada dokter nya juga <br>
                        super duper lengkap, harganya lebih murah daripada pet shop lainnya! dan yang bikin senang
                        pelayanannya sangat ramah
                        <br>
                        pernah grooming juga disini dan beneran worth it karena kucing aku kutunya langsung hilang 😻
                    </p>
                    <h6 class="fw-bold mb-0">Rieska Nadiya Putri</h6>
                    <p class="text-muted">Pelanggan</p>
                </div>

                <!-- gmbr -->
                <div class="col-md-6 text-center">
                    <div class="position-relative d-inline-block">
                        <img src="aset/rate.png" alt="Pelanggan"
                            style="width: 400px; height: 350px; object-fit: cover;">
                    </div>
                    <!-- Navigasi -->
                    <div class="mt-3">
                        <button class="btn btn-dark rounded-circle me-2"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button class="btn btn-dark rounded-circle"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>