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

        .fade-transition {
            transition: opacity 0.15s ease-in-out;
        }

        .fade-in {
            opacity: 1;
        }

        .fade-out {
            opacity: 0;
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
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Kita Sayang Hewan Kayak
                    <br>Keluarga Sendiri,
                    <br>Seriusan!
                </h1>
                <a href="shop.php" class="btn btn-black text-base mt-2">Mulai Belanja</a>
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
    <section class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
                <!-- Left Column - Testimonial Content -->
                <div class="space-y-2">
                    <p class="text-orange-500 font-semibold text-sm uppercase tracking-wide">Testimoni</p>
                    <h2 class="text-2xl font-bold text-gray-900">Apa kata mereka tentang kami</h2>

                    <!-- Testimonial Content Container -->
                    <div id="testimonial-content" class="fade-transition fade-in">
                        <div class="text-orange-400 text-xl mb-2">
                            <span id="star-rating">★ ★ ★ ★ ★</span>
                        </div>
                        <blockquote id="testimonial-text" class="text-gray-600 leading-relaxed text-sm mb-4">
                            "Kucing aku sakit, terus periksa kesini.. beberapa hari selanjutnya ada kemajuan, sembuh.
                            Mana mba2nya baik bgt lagiiii. Teris harganya masi terjangkauuu!🥹🫶🏻"
                        </blockquote>
                        <div class="pt-2">
                            <h4 id="customer-name" class="font-bold text-gray-900 text-base">Arsy Nisa</h4>
                            <p id="customer-role" class="text-gray-500 text-xs">Pelanggan</p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-start space-x-3 pt-4">
                        <button id="prev-btn"
                            class="w-10 h-10 bg-gray-800 hover:bg-orange-500 text-white rounded-full flex items-center justify-center transition duration-300 transform hover:scale-105">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button id="next-btn"
                            class="w-10 h-10 bg-gray-800 hover:bg-orange-500 text-white rounded-full flex items-center justify-center transition duration-300 transform hover:scale-105">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Dots Indicator -->
                    <div class="flex justify-start space-x-2 pt-3">
                        <div class="dot w-2 h-2 bg-orange-500 rounded-full cursor-pointer transition duration-300"
                            data-index="0"></div>
                        <div class="dot w-2 h-2 bg-gray-300 rounded-full cursor-pointer transition duration-300 hover:bg-orange-300"
                            data-index="1"></div>
                        <div class="dot w-2 h-2 bg-gray-300 rounded-full cursor-pointer transition duration-300 hover:bg-orange-300"
                            data-index="2"></div>
                        <div class="dot w-2 h-2 bg-gray-300 rounded-full cursor-pointer transition duration-300 hover:bg-orange-300"
                            data-index="3"></div>
                    </div>
                </div>

                <!-- Right Column - Image and Navigation -->
                <div class="text-center">
                    <div class="relative inline-block">
                        <!-- Shape background -->
                        <img src="../aset/shapetesti_about.png" alt="" class="relative z-0 w-96">
                        <!-- Customer Image Container -->
                        <div id="customer-image"
                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-56 h-56 bg-white rounded-full flex items-center justify-center overflow-hidden shadow-inner z-10">
                            <!-- Default avatar -->
                            <div
                                class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                <img src="../aset/testi_arsy.png" alt="Customer Avatar"
                                    class="w-full h-full object-cover rounded-full">
                            </div>
                        </div>

                        <!-- Decorative elements -->
                        <div class="absolute -top-3 -right-3 w-6 h-6 bg-orange-200 rounded-full animate-pulse"></div>
                        <div
                            class="absolute -bottom-4 -left-4 w-5 h-5 bg-orange-300 rounded-full animate-pulse delay-500">
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

        // Testimonial data
        const testimonials = [
            {
                text: "Kucing aku sakit, terus periksa kesini.. beberapa hari selanjutnya ada kemajuan, sembuh. Mana mba2nya baik bgt lagiiii. Teris harganya masi terjangkauuu!🥹🫶🏻",
                name: "Arsy Nisa",
                role: "Pelanggan",
                rating: 5,
                avatar: "../aset/testi_arsy.png"
            },
            {
                text: "Dokter ramah dan berpengalaman Byk kebutuhan kucing dgn harga standar disana Parkir luas",
                name: "Felixa Puryastiwi",
                role: "Pelanggan",
                rating: 5,
                avatar: "../aset/testi_felixa.png"
            },
            {
                text: "Berlangganan sejak 2015.. harga bersahabat, dokter cantik dan ramah, bener-bener mengutamakan pasien! pokoknya okeee!!!",
                name: "Arinanda Gilang",
                role: "Pelanggan",
                rating: 5,
                avatar: "../aset/testi_arinanda.png"
            },
            {
                text: "Anabulku cocok periksa di sini kalau sakit, intinya jangan nunggu parah dulu baru diperiksakan, harus perhatikan tingkah laku dan mulut anabul, kalau ada gejala aneh, muntah, ga mau makan, langsung dibawa periksa yaa😺",
                name: "Kiki Syaputri",
                role: "Pelanggan",
                rating: 4,
                avatar: "../aset/testi_kiki.png"
            }
        ];

        let currentIndex = 0;

        // Function to generate stars based on rating
        function generateStars(rating) {
            let stars = '';
            for (let i = 0; i < 5; i++) {
                if (i < rating) {
                    stars += '★ ';
                } else {
                    stars += '☆ ';
                }
            }
            return stars.trim();
        }

        // Function to update testimonial content
        function updateTestimonial(index) {
            const testimonial = testimonials[index];
            const contentDiv = document.getElementById('testimonial-content');

            // Add fade out effect
            contentDiv.classList.add('fade-out');

            setTimeout(() => {
                // Update content
                document.getElementById('star-rating').textContent = generateStars(testimonial.rating);
                document.getElementById('testimonial-text').textContent = `"${testimonial.text}"`;
                document.getElementById('customer-name').textContent = testimonial.name;
                document.getElementById('customer-role').textContent = testimonial.role;

                // Update avatar
                const avatarContainer = document.getElementById('customer-image');
                avatarContainer.innerHTML = `
                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <img src="${testimonial.avatar}" alt="Customer Avatar" class="w-full h-full object-cover rounded-full">
                    </div>
                `;

                // Update dots
                document.querySelectorAll('.dot').forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.remove('bg-gray-300');
                        dot.classList.add('bg-orange-500');
                    } else {
                        dot.classList.remove('bg-orange-500');
                        dot.classList.add('bg-gray-300');
                    }
                });

                // Add fade in effect
                contentDiv.classList.remove('fade-out');
                contentDiv.classList.add('fade-in');
            }, 150);
        }

        // Event listeners for navigation buttons
        document.getElementById('prev-btn').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
            updateTestimonial(currentIndex);
        });

        document.getElementById('next-btn').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            updateTestimonial(currentIndex);
        });

        // Event listeners for dots
        document.querySelectorAll('.dot').forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                updateTestimonial(currentIndex);
            });
        });

        // Auto-play testimonials
        let autoPlay = setInterval(() => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            updateTestimonial(currentIndex);
        }, 10000); // Change every 8 seconds

        // Pause auto-play when user interacts
        const pauseAutoPlay = () => {
            clearInterval(autoPlay);
            // Resume after 10 seconds of no interaction
            setTimeout(() => {
                autoPlay = setInterval(() => {
                    currentIndex = (currentIndex + 1) % testimonials.length;
                    updateTestimonial(currentIndex);
                }, 8000);
            }, 10000);
        };

        // Add pause functionality to all interactive elements
        document.getElementById('prev-btn').addEventListener('click', pauseAutoPlay);
        document.getElementById('next-btn').addEventListener('click', pauseAutoPlay);
        document.querySelectorAll('.dot').forEach(dot => {
            dot.addEventListener('click', pauseAutoPlay);
        });
    </script>
</body>

</html>