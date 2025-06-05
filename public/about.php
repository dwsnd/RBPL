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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Jika Hewan Bisa Berbicara,<br>Mereka Akan Berbicara<br> Tentang Kita!</h1>
                <a href="shopawal.php" class="btn btn-black text-base mt-2">Mulai Belanja</a>
            </div>
        </div>
        <img src="../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
    </section>
    <!-- hero rampung -->

    <!-- info tentang kami -->
    <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tentang Kami</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <!-- Left Column - About & Form -->
                <div class="space-y-8">
                    <div class="mb-12">
                        <p class="text-gray-600 leading-relaxed text-sm">
                            Kami hadir sebagai solusi lengkap untuk kebutuhan hewan peliharaan Anda di Kabupaten Bantul.
                            Dari pakan, aksesoris, hingga perlengkapan grooming dan kesehatan, semua tersedia dengan
                            harga terjangkau dan kualitas terjamin.
                        </p>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-gray-200 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <p class="font-semibold mb-1 text-sm">Nama Depan</p>
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="Masukkan nama depan">
                            </div>
                            <div>
                                <p class="font-semibold mb-1 text-sm">Nama Belakang</p>
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="Masukkan nama belakang">
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold mb-1 text-sm">Alamat Email</p>
                            <input type="email"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="Masukkan alamat email">
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold mb-1 text-sm">Pesan</p>
                            <textarea
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                rows="3" placeholder="Pesan anda..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <a href="../auth/login.php"
                                class="w-1/2 mr-2 bg-orange-500 hover:bg-orange-600 text-sm text-white font-semibold py-2 px-4 rounded-lg transition duration-300 text-center">
                                Kirim Pesan
                            </a>
                            <div class="w-1/2 flex justify-center items-center text-2xl text-gray-400 gap-2"
                                id="starRating">
                                <i class="fas fa-star cursor-pointer hover:text-orange-500 transition duration-150"
                                    data-rating="1"></i>
                                <i class="fas fa-star cursor-pointer hover:text-orange-500 transition duration-150"
                                    data-rating="2"></i>
                                <i class="fas fa-star cursor-pointer hover:text-orange-500 transition duration-150"
                                    data-rating="3"></i>
                                <i class="fas fa-star cursor-pointer hover:text-orange-500 transition duration-150"
                                    data-rating="4"></i>
                                <i class="fas fa-star cursor-pointer hover:text-orange-500 transition duration-150"
                                    data-rating="5"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Contact Info -->
                <div class="space-y-2">
                    <div>
                        <p class="text-gray-600 leading-relaxed pb-10 text-sm">
                            Temukan produk terbaik untuk hewan kesayangan Anda di toko kami.
                            <br>Kunjungi langsung atau hubungi kami untuk informasi stok, promo,
                            <br>dan penawaran spesial!
                        </p>
                        <h3 class="text-2xl font-bold text-gray-900 pt-2">Jangan ragu menghubungi kami</h3>
                    </div>

                    <!-- Contact Info with Icons -->
                    <div class="space-y-3">
                        <p class="text-gray-600 leading-relaxed mb-4 text-sm">Sahabat setia Anda butuh yang terbaik?
                            Kami siap
                            membantu!
                            <br>Hubungi kami sekarang untuk solusi cepat dan produk berkualitas untuk si menggemaskan.
                            Tim Ling-Ling Pet Shop siap melayani dengan sepenuh hati!
                        </p>
                        <div class="flex items-center space-x-3 pb-3">
                            <div
                                class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Jl. Parangtritis KM 6 Jetis,
                                    Panggungharjo, Sewon, Bantul</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pb-3">
                            <div
                                class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">linglingpetshop@gmail.com</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pb-3">
                            <div
                                class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">+62 838-6705-6070</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pb-3">
                            <div
                                class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Senin–Sabtu: 9AM – 9PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Left Column - Testimonial Content -->
                <div class="space-y-3">
                    <p class="text-orange-500 font-semibold text-base uppercase tracking-wide">Testimoni</p>
                    <h2 class="text-2xl font-bold text-gray-900">Apa kata mereka tentang kami</h2>
                    <div class="text-orange-400 text-2xl mb-2">
                        ★ ★ ★ ★ ★
                    </div>
                    <blockquote class="text-gray-600 leading-relaxed text-sm">
                        "pet shop & ada dokter nya jugaa 🤩 super duperr lengkap , harganya lebih murah daripada pet
                        shop lainnya ! dan yang bikin senang pelayanannya sangaat ramah 💓💓 pernah grooming juga disini
                        dan beneran worth it karena kucing aku kutunyaa langsung hilaang 🥺💓"
                    </blockquote>
                    <div class="flex items-center justify-between">
                        <div class="pt-3">
                            <h4 class="font-bold text-gray-900">Rieska Nadiya Putri</h4>
                            <p class="text-gray-500 text-sm">Pelanggan</p>
                        </div>
                        <div class="flex justify-center space-x-4">
                            <button
                                class="w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition duration-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button
                                class="w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition duration-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Image and Navigation -->
                <div class="text-center space-y-6">
                    <div class="relative inline-block">
                        <!-- Orange circular background -->
                        <div
                            class="w-80 h-80 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center mx-auto">
                            <img src="../aset/shapetesti_about.png" alt="">
                            <!-- Placeholder for customer image -->
                            <div class="w-64 h-64 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500 text-6xl"></i>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('#starRating .fa-star');
            let currentRating = 0;

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    currentRating = rating;

                    // Reset all stars
                    stars.forEach(s => s.classList.remove('text-orange-500'));

                    // Color stars up to the clicked rating
                    stars.forEach(s => {
                        if (parseInt(s.getAttribute('data-rating')) <= rating) {
                            s.classList.add('text-orange-500');
                        }
                    });
                });

                // Hover effect
                star.addEventListener('mouseover', function () {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    stars.forEach(s => {
                        if (parseInt(s.getAttribute('data-rating')) <= rating) {
                            s.classList.add('text-orange-500');
                        } else {
                            s.classList.remove('text-orange-500');
                        }
                    });
                });

                star.addEventListener('mouseout', function () {
                    stars.forEach(s => {
                        if (parseInt(s.getAttribute('data-rating')) > currentRating) {
                            s.classList.remove('text-orange-500');
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>