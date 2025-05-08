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

        .nav-link {
            position: relative;
            color: #000 !important;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #ffc107;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-link.active {
            color: #ffc107 !important;
        }
        .nav-link.active::after {
            transform: scaleX(1);
        }

        .nav-link:hover::after {
            transform: scaleX(1);
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .btn-black {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
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
                <?php $current = basename($_SERVER['PHP_SELF']); ?>
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2 <?php echo $current == 'index.php' ? 'active' : ''; ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link fw-semibold mx-2 dropdown-toggle <?php echo in_array($current, ['shop.php', 'perawatan.php', 'penitipan.php', 'konsultasi.php']) ? 'active' : ''; ?>"
                            href="#" role="button" data-bs-toggle="dropdown">
                            Layanan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="shopawal.php">Shop</a></li>
                            <li><a class="dropdown-item" href="perawatan.php">Perawatan</a></li>
                            <li><a class="dropdown-item" href="penitipan.php">Penitipan</a></li>
                            <li><a class="dropdown-item" href="konsultasi.php">Konsultasi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2 <?php echo $current == 'shopawal.php' ? 'active' : ''; ?>"
                            href="shopawal.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2 <?php echo $current == 'aboutawal.php' ? 'active' : ''; ?>"
                            href="aboutawal.php">About Us</a>
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
    <?php include 'hero.php'; ?>
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
                        Dari pakan, aksesoris, hingga perlengkapan grooming dan kesehatan, semua tersedia dengan harga terjangkau dan kualitas terjamin.
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
                        super duper lengkap, harganya lebih murah daripada pet shop lainnya! dan yang bikin senang pelayanannya sangat ramah
                        <br>
                        pernah grooming juga disini dan beneran worth it karena kucing aku kutunya langsung hilang 😻
                    </p>
                    <h6 class="fw-bold mb-0">Rieska Nadiya Putri</h6>
                    <p class="text-muted">Pelanggan</p>
                </div>

                <!-- gmbr -->
                <div class="col-md-6 text-center">
                    <div class="position-relative d-inline-block">
                        <img src="aset/rate.png" alt="Pelanggan" style="width: 400px; height: 350px; object-fit: cover;">
                    </div>
                    <!-- Navigasi -->
                    <div class="mt-3">
                        <button class="btn btn-dark rounded-circle me-2"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="btn btn-dark rounded-circle"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>


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