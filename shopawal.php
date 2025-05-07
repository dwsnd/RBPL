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

        .nav-link.active {
            color: #ffc107 !important;
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

        .nav-link.active::after {
            transform: scaleX(1);
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            background: #fff;
            padding: 60px 0;
        }

        .hero-text {
            font-size: 2rem;
            font-weight: 700;
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
    <section class="hero-section text-center text-lg-start">
        <div class="container d-flex flex-wrap align-items-center justify-content-between">
            <div class="col-lg-6 mb-4">
                <h6 class="text-warning">Ling-Ling Pet Shop</h6>
                <h1 class="hero-text">Belajar Praktis untuk<br>Kebutuhan Hewan<br>Peliharaan Anda</h1>
                <a href="#" class="btn btn-black mt-3">Mulai Belanja</a>
            </div>
            <div class="col-lg-5">
                <img src="aset/hro.png" class="img-fluid" alt="Hewan Peliharaan">
            </div>
        </div>
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
                    <div class="rounded-4 p-4 shadow-sm">
                        <img src="aset/kucingg.png" alt="Shop" class="img-fluid w-100" style="width: 80px;">
                        <p class="fw-semibold mb-0">Kucing</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm">
                        <img src="aset/hamster.png" alt="Perawatan" class="img-fluid w-100" style="width: 80px;">
                        <p class="fw-semibold mb-0">Hamster</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm">
                        <img src="aset/anjeng.png" alt="Penitipan" class="img-fluid w-100" style="width: 80px;">
                        <p class="fw-semibold mb-0">Anjing</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rounded-4 p-4 shadow-sm">
                        <img src="aset/kelinci.png" alt="Konsultasi" class="img-fluid w-100" style="width: 80px;">
                        <p class="fw-semibold mb-0">Kelinci</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container py-5">
        <!-- row1  -->
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3 justify-content-center">
            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body text-center">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- row2 -->
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3 justify-content-center mt-3">
            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body text-center">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100">
                    <img src="aset/produk.png" class="card-img-top" alt="aksesoris">
                    <div class="card-body">
                        <h6 class="mb-1">Makanan Kucing 600gram</h6>
                        <p class="text-muted mb-0">Rp.60.000</p>
                    </div>
                </div>
            </div>
        </div>
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