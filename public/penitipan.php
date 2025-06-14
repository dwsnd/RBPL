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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Titipin? Gas! Kita Rawat
                    <br>Kayak Punya Sendiri.
                </h1>
                <a href="shop.php" class="btn btn-black text-base mt-2">Mulai Penitipan Hewan</a>
            </div>
        </div>
        <img src="../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- masalah pelanggan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-center font-bold text-2xl mb-8">Apakah Kamu Mengalami Masalah Ini?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $masalah = [
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Anabulmu mengalami stres saat harus ikut mudik/lebaran'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Merasa tidak tega meninggalkan anabulmu sendirian di rumah tanpa pengawasan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Sulit mencari orang yang bisa merawat anabulmu selama kamu pergi liburan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Khawatir jika dititipkan ke orang lain, perawatannya tidak sesuai dengan yang kamu inginkan'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Tidak memiliki kerabat atau teman yang bisa dititipi untuk menjaga anabulmu'
                    ],
                    [
                        'icon' => 'exclamation-circle',
                        'text' => 'Terbatasnya waktu mengurus kebutuhan anabulmu dengan baik'
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

    <!-- syarat penitipan -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-5/12 mb-6 lg:mb-0">
                    <img src="../aset/anjingpenitipan1.png" alt="Grooming Illustration" class="w-full">
                </div>
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-4 text-center pl-20">Syarat Penitipan Hewan
                    </h2>
                    <?php
                    $syarat = [
                        ['text' => 'Hewan dalam kondisi sehat & tidak berkutu. Kami akan melakukan pemeriksaan kondisi anjing & kucing Anda saat tiba. Hal ini bertujuan untuk menjaga keamanan & kesehatan semua hewan yang dititipkan.'],
                        ['text' => 'Jika setelah pemeriksaan ditemukan kutu atau masalah kulit, akan dilakukan pengobatan khusus segera saat kedatangan. Hewan tetap bisa dititipkan dalam ruang perawatan khusus yang terpisah dari hewan yang sehat.'],
                        ['text' => 'Selama penitipan, hewan kesayangan Anda memiliki waktu bermain bebas bersama pengunjung/hewan lain tanpa diikat atau dikandangkan, kecuali atas permintaan khusus atau dalam situasi tertentu.'],
                        ['text' => 'Anda dapat membawa perlengkapan khusus sesuai kebutuhan seperti makanan, cemilan, vitamin, mainan, dan sebagainya.']
                    ];

                    foreach ($syarat as $index => $item) {
                        $number = $index + 1;
                        echo '<div class="flex items-start pl-20">
                                <div class="flex items-center justify-center font-semibold text-base mr-4">' . $number . '</i>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-base flex-1">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- bagian fasilitas & benefit -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-7/12 lg:pl-8">
                    <h2 class="text-orange-500 font-bold text-2xl mb-4 text-center pr-20">Fasilitas & Benefit
                    </h2>
                    <?php
                    $fasilitas = [
                        ['text' => 'Jasa Antar-Jemput khusus area DIY (biaya tambahan sesuai jarak)'],
                        ['text' => 'Free grooming untuk minimal 1 minggu penitipan'],
                        ['text' => 'Full AC (Indoor Cage)'],
                        ['text' => 'Kandang Bersih dan Luas dengan satu kandang untuk satu ekor anjing/kucing, tidak dicampur dengan hewan lain'],
                        ['text' => 'Update Foto & Video harian'],
                        ['text' => 'Pembersihan Alat Makan & Minum 2x Sehari'],
                        ['text' => 'Pembersihan Toilet Setiap Hari'],
                        ['text' => 'Paramedis yang siaga untuk memantau kondisi anabul'],
                        ['text' => 'Penyediaan berbagai mainan untuk Anjing & Kucing']
                    ];

                    foreach ($fasilitas as $index => $item) {
                        $number = $index + 1;
                        echo '<div class="flex items-start">
                                <div class="flex items-center justify-center font-semibold text-base mr-4">' . $number . '</i>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-base flex-1 mr-10">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
                <div class="w-full lg:w-5/12 mb-6 lg:mb-0">
                    <img src="../aset/anjingpenitipan2.png" alt="Grooming Illustration" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- promo -->
    <section class="py-20 px-16">
        <div class="container mx-auto px-4 py-10 bg-orange-100 rounded-3xl">
            <h2 class="text-center font-bold text-2xl mb-2">Penitipan Hewan Terpercaya & Aman!</h2>
            <div class="text-center mb-8">
                <p class="font-medium text-base mb-4">Mulai Dari <strong>40 Ribu Rupiah</strong> Per Hari</p>
                <a href="../auth/login.php"
                    class="inline-flex items-center justify-center text-sm bg-orange-500 text-white px-4 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                    <i class="fas fa-heart text-2xl mr-2"></i>Booking Penitipan Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>