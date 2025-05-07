<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ling-Ling Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-shadow {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Hero Section */
        .hero-section {
            background: #fff;
            padding: 60px 0;
        }

        .hero-text {
            font-size: 2.5rem;
            font-weight: 700;
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
            top: 12%;
            width: 50%;
            z-index: 1;
        }

        .image-catdog {
            position: absolute;
            right: 40px;
            top: 12%;
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
    </style>
</head>

<body>

    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 navbar-shadow">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-paw"></i> Ling-Ling Pet Shop</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2 active" href="#">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link fw-semibold mx-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Layanan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Shop</a></li>
                            <li><a class="dropdown-item" href="#">Perawatan</a></li>
                            <li><a class="dropdown-item" href="#">Penitipan</a></li>
                            <li><a class="dropdown-item" href="#">Konsultasi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2" href="shopawal.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2" href="aboutawal.php">About Us</a>
                    </li>
                </ul>

                <!-- Tombol Login dan Sign Up -->
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-warning me-2 fw-semibold">Login</a>
                    <a href="#" class="btn btn-warning text-white fw-semibold">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden">
        <!-- SHAPE BESAR KANAN -->
        <img src="aset/Shape2.png" class="shape-main" alt="Shape">
        <!-- SHAPE KECIL KIRI ATAS -->
        <img src="aset/Shape.png" class="shape-leftup" alt="Shape2">
        <!-- SHAPE KECIL KIRI BAWAH -->
        <img src="aset/Shape1.png" class="shape-leftdown" alt="Shape1">
        <div class="container d-flex flex-wrap align-items-center justify-content-between position-relative"
            style="z-index:2;">
            <div class="col-lg-6 mb-4 text-lg-start text-center">
                <h6 class="nama-toko text-warning">Ling-Ling Pet Shop</h6>
                <h1 class="hero-text mb-4">Belajar Praktis untuk<br>Kebutuhan Hewan<br>Peliharaan Anda</h1>
                <a href="#" class="btn btn-black mt-3">Mulai Belanja</a>
            </div>
        </div>
        <img src="aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- Layanan Kami -->
    <section class="text-center py-5">
        <div class="container">
            <h3 class="section-title mb-4">
                <i class="fa-solid fa-paw text-warning"></i> <strong>Layanan Kami</strong>
            </h3>
            <div class="row g-3 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm" style="background-color: #e0e0e0;">
                        <img src="aset/shop.png" alt="Shop" class="img-fluid mb-2" style="width: 80px;">
                        <p class="fw-semibold mb-0">Shop</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm" style="background-color: #e0e0e0;">
                        <img src="aset/perawatan.png" alt="Perawatan" class="img-fluid mb-2" style="width: 80px;">
                        <p class="fw-semibold mb-0">Perawatan</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm" style="background-color: #e0e0e0;">
                        <img src="aset/penitipan.png" alt="Penitipan" class="img-fluid mb-2" style="width: 80px;">
                        <p class="fw-semibold mb-0">Penitipan</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm" style="background-color: #e0e0e0;">
                        <img src="aset/konsultasi.png" alt="Konsultasi" class="img-fluid mb-2" style="width: 80px;">
                        <p class="fw-semibold mb-0">Konsultasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Konsultasi -->
    <section class="py-5 bg-light">
        <div class="container d-flex flex-wrap align-items-center justify-content-between">
            <div class="col-lg-5 mb-4">
                <img src="aset/solusi.png" class="img-fluid rounded" alt="Konsultasi">
            </div>
            <div class="col-lg-6">
                <h5 class="fw-bold">Segera Hubungi Kami dan Dapatkan Solusi Terbaik</h5>
                <p>Jika hewan kesayangan Anda sakit, butuh perawatan, atau layanan darurat, kami siap membantu. Dapatkan konsultasi, pengobatan, dan grooming dari tim profesional. Hubungi kami sekarang!</p>
                <a href="#" class="btn btn-black">Memesan Jadwal</a>
            </div>
        </div>
    </section>

    <!-- Produk Kami -->
    <div class="container text-center">
        <h3 class="section-title mb-3">
            <i class="fa-solid fa-paw text-warning"></i> Produk Kami
        </h3>
        <div class="row">
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="aset/aksesoris.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6>Aksesoris</h6>
                        <p class="text-muted">84 produk</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="aset/makanan.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6>Makanan</h6>
                        <p class="text-muted">64 produk</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="aset/pasir.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6>Pasir</h6>
                        <p class="text-muted">22 produk</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="aset/vitamin.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6>Vitamin</h6>
                        <p class="text-muted">16 produk</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

    <!-- Gambar Kucing -->
    <div class="py-4">
        <img src="aset/kucing.png" alt="Kucing" class="cat-img">
    </div>

    <!-- Footer -->
    <footer>
        <div class="container d-flex flex-wrap justify-content-between align-items-start">
            <div class="col-md-6 mb-3">
                <h6><i class="fa-solid fa-paw"></i> Ling-Ling Pet Shop</h6>
                <p>Jl. Parangtritis KM 6 Jetis, Panggung Harjo, Sewon, Bantul<br>
                    Jl. Samas KM 2 Kanutan, Sumbermulyo, Bambanglipuro, Bantul<br>
                    Jl. Raya Berbah Pelem Lor, Baturetno, Banguntapan, Bantul</p>
            </div>
            <div class="col-md-3">
                <h6>Jam Buka</h6>
                <p>Senin - Sabtu<br>09.00 - 21.00<br>+62 838-6705-6070</p>
                <div>
                    <a href="#"><i class="fab fa-facebook me-2"></i></a>
                    <a href="#"><i class="fab fa-instagram me-2"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <small>© Copyright Ling-Ling Pet Shop 2025.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>