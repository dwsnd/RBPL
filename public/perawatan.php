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

        footer {
            padding: 40px 0;
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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Grooming Bukan Sekadar
                    <br>Mandi Ini Biar Mereka
                    <br>Glow Up Total!
                </h1>
                <a href="shop.php" class="btn btn-black text-base mt-2">Mulai Perawatan Hewan</a>
            </div>
        </div>
        <img src="../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- masalah anabul -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-center font-bold text-2xl mb-8">Apakah Anabul Kesayanganmu Mengalami Masalah Ini?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $masalah = [
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Kulitnya bermasalah dengan bakteri dan jamur yang bikin tidak nyaman?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Kutu nakal muncul dan membuatnya terus menggaruk dengan gelisah?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Bulu rontok, kusut, atau terlihat tidak terawat seperti kehilangan kilaunya?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Ada kotoran membandel di area mata dan telinga yang sulit dibersihkan?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Bau kurang sedap yang mulai mengganggu pelukan hangatmu dengannya?'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Takut salah memberikan perawatan yang tepat untuk kesehatan si kecil?'
                    ]
                ];

                foreach ($masalah as $item) {
                    echo '<div class="bg-gray-200 p-4 rounded-lg h-full">
                            <div class="flex items-center h-full">
                                <div class="text-2xl text-orange-500 mr-6 flex-shrink-0">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="text-sm flex-grow">' . $item['text'] . '</p>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- info perawatan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-5/12 mb-8 lg:mb-0">
                    <img src="../aset/anjingperawatan.png" alt="Grooming Illustration" class="w-full">
                </div>
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-3">Segera Lakukan Grooming Pada Anabul Kesayangan!
                    </h2>
                    <p class="mb-2 text-base">Grooming rutin sangat penting untuk menjaga kesehatan dan kenyamanan si
                        kecil kesayangan Anda. Namun, kami memahami beberapa kendala yang mungkin
                        <br>Anda hadapi:
                    </p>

                    <?php
                    $kendala = [
                        ['icon' => 'exclamation-triangle', 'text' => 'Tidak memahami teknik grooming yang tepat dan aman untuk Anabul Anda'],
                        ['icon' => 'clock', 'text' => 'Kesibukan yang membuat Anda tidak punya waktu cukup untuk melakukan grooming sendiri'],
                        ['icon' => 'question-circle', 'text' => 'Bingung memilih produk perawatan yang aman dan sesuai untuk kondisi <br>Anabul Anda']
                    ];

                    foreach ($kendala as $item) {
                        echo '<div class="flex items-start">
                                <div class="mr-4 items-center justify-center text-lg">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="mb-2 text-base leading-relaxed">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                    <a href="https://wa.me/6288217723999"
                        class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-orange-600 transition duration-200">
                        <i class="fab fa-whatsapp text-2xl mr-2"></i>Hubungi Kami
                    </a>
                    <span class="ml-4 items-center justify-center text-base fw-semibold">Konsultasi permasalahan
                        anabulmu sekarang!</span>
                </div>
            </div>
        </div>
    </section>

    <!-- bagian promo -->
    <section class="py-20 px-16">
        <div class="container mx-auto px-4 py-10 bg-orange-100 rounded-3xl">
            <h2 class="text-center font-bold text-2xl mb-10">Ada Perawatan Apa Aja Kalau Kamu Grooming di Ling-Ling
                Petshop</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-6">
                <?php
                $services = [
                    [
                        'icon' => 'database',
                        'title' => 'Basic',
                        'description' => 'Layanan dasar yang mencakup grooming anti kutu & jamur, pemeriksaan kesehatan, serta pemberian obat cacing untuk menjaga kebersihan dan kesehatan hewan peliharaan Anda'
                    ],
                    [
                        'icon' => 'band-aid',
                        'title' => 'Mix',
                        'description' => 'Paket perawatan lengkap dengan grooming basic ditambah suntik vitamin untuk meningkatkan daya tahan tubuh dan vitalitas hewan kesayangan Anda'
                    ],
                    [
                        'icon' => 'trophy',
                        'title' => 'Complete',
                        'description' => 'Layanan premium mencakup grooming mix ditambah tes Revolution untuk deteksi dan perlindungan menyeluruh dari parasit sebagai solusi total bagi kesehatan hewan peliharaan Anda'
                    ]
                ];

                foreach ($services as $service) {
                    echo '<div class="text-center px-4">
                            <div class="text-5xl text-orange-500 mb-3">
                                <i class="fas fa-' . $service['icon'] . '"></i>
                            </div>
                            <h3 class="font-bold text-lg mb-3">' . $service['title'] . '</h3>
                            <p class="mb-0 text-sm text-justify">' . $service['description'] . '</p>
                          </div>';
                }
                ?>
            </div>

            <div class="text-center mt-8 mb-8 rounded-4xl">
                <p class="font-bold mb-4 text-sm">Benefit: Semua jenis grooming sudah termasuk gunting kuku, pembersihan
                    telinga, rapihkan bulu paw, dan bagian bokong</p>
                <a href="../auth/login.php"
                    class="inline-flex items-center justify-center bg-orange-500 text-sm text-white px-4 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-scissors text-2xl mr-2"></i>Saya Mau Booking Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>