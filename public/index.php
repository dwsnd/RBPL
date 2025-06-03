<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Hero Section */
        .hero-section {
            background: #fff;
            padding: 65px 0;
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
                <h6 class="text-orange-500 text-base font-semibold mb-2">Ling-Ling Pet Shop</h6>
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Belajar Praktis untuk<br>Kebutuhan Hewan<br>Peliharaan Anda</h1>
                <a href="#" class="btn btn-black text-base mt-2">Mulai Belanja</a>
            </div>
        </div>
        <img src="../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- Layanan Kami -->
    <section class="text-center py-4">
        <div class="container">
            <h3 class="section-title mb-4 font-bold text-2xl">
                <i class="fa-solid fa-paw text-warning"></i> Layanan Kami
            </h3>
            <div class="row justify-content-center">
                <div class="col-md-3 mb-3">
                    <a href="shopawal.php" class="text-decoration-none text-dark">
                        <div class="card mx-auto" style="width: 17rem; background-color: #e0e0e0;">
                            <div class="d-flex flex-column h-100">
                                <img src="../aset/shop_index.png" class="card-img-top" alt="Shop"
                                    style="height: 120px; object-fit: contain; padding: 10px;">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-medium">Shop</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="perawatan.php" class="text-decoration-none text-dark">
                        <div class="card mx-auto" style="width: 17rem; background-color: #e0e0e0;">
                            <div class="d-flex flex-column h-100">
                                <img src="../aset/perawatan_index.png" class="card-img-top" alt="Perawatan"
                                    style="height: 120px; object-fit: contain; padding: 10px;">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-medium">Perawatan</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="penitipan.php" class="text-decoration-none text-dark">
                        <div class="card mx-auto" style="width: 17rem; background-color: #e0e0e0;">
                            <div class="d-flex flex-column h-100">
                                <img src="../aset/penitipan_index.png" class="card-img-top" alt="Penitipan"
                                    style="height: 120px; object-fit: contain; padding: 10px;">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-medium">Penitipan</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="konsultasi.php" class="text-decoration-none text-dark">
                        <div class="card mx-auto" style="width: 17rem; background-color: #e0e0e0;">
                            <div class="d-flex flex-column h-100">
                                <img src="../aset/konsultasi_index.png" class="card-img-top" alt="Konsultasi"
                                    style="height: 120px; object-fit: contain; padding: 10px;">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-medium">Konsultasi</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Konsultasi -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0 ml-16">
                    <div class="position-relative">
                        <img src="../aset/konsulgambar_index.png" class="img-fluid" alt="Konsultasi">
                    </div>
                </div>
                <div class="col-lg-6 ml-10">
                    <h2 class="fw-bold text-2xl mb-4">Segera Hubungi Kami dan Dapatkan <span class="text-orange-500">Solusi
                            Terbaik</span></h2>
                    <p class="text-muted">Jika hewan kesayangan Anda sakit, butuh perawatan, atau layanan darurat,
                        kami siap membantu. Dapatkan konsultasi, pengobatan, dan grooming dari tim profesional. Hubungi
                        kami sekarang!</p>
                    <a href="../auth/login.php" class="btn btn-black mt-3">Memesan Jadwal</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Kami -->
    <div class="container text-center py-4">
        <h3 class="section-title mb-4 font-bold text-2xl">
            <i class="fa-solid fa-paw text-warning"></i> Produk Kami
        </h3>
        <div class="row justify-content-center">
            <div class="col-md-3 mb-3">
                <a href="shopawal.php" class="text-decoration-none text-dark">
                    <div class="card mx-auto h-100" style="width: 17rem;">
                        <div class="d-flex flex-column h-100">
                            <img src="../aset/aksesoris.jpg" class="card-img-top" alt="aksesoris"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <h6 class="mb-1 fw-bold text-medium">Aksesoris</h6>
                                    <p class="text-muted small mb-0">84 produk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="shopawal.php" class="text-decoration-none text-dark">
                    <div class="card mx-auto h-100" style="width: 17rem;">
                        <div class="d-flex flex-column h-100">
                            <img src="../aset/makanan.png" class="card-img-top" alt="makanan"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <h6 class="mb-1 fw-bold text-medium">Makanan</h6>
                                    <p class="text-muted small mb-0">64 produk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="shopawal.php" class="text-decoration-none text-dark">
                    <div class="card mx-auto h-100" style="width: 17rem;">
                        <div class="d-flex flex-column h-100">
                            <img src="../aset/pasir.png" class="card-img-top" alt="pasir"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <h6 class="mb-1 fw-bold text-medium">Pasir</h6>
                                    <p class="text-muted small mb-0">22 produk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="shopawal.php" class="text-decoration-none text-dark">
                    <div class="card mx-auto h-100" style="width: 17rem;">
                        <div class="d-flex flex-column h-100">
                            <img src="../aset/vitamin.jpg" class="card-img-top" alt="vitamin"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <h6 class="mb-1 fw-bold text-medium">Vitamin</h6>
                                    <p class="text-muted small mb-0">16 produk</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Gambar Kucing -->
    <section>
        <img src="../aset/kucingindex.png" alt="Kucing" class="img-fluid w-full">
    </section>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>