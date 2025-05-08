<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
    </style>
</head>
<body>
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
</body>
</html>