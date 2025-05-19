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

        .form-control:focus,
        select:focus,
        input:focus {
            border-color: #fd7e14 !important;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25) !important;
        }


        select.form-control {
            color: #888;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23000' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 2.5rem !important;
        }

        select.form-control option {
            color: #888 !important;
        }

        select.form-control option:checked {
            color: #000 !important;
        }

        select.form-control option:first-child {
            color: #888 !important;
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
                    echo '<div class="bg-gray-200 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="text-2xl text-orange-500 mr-3">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="mb-0">' . $item['text'] . '</p>
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
                    <h2 class="text-orange-500 font-bold text-2xl mb-4">Segera Lakukan Grooming Pada Anabul Kesayangan!
                    </h2>
                    <p class="mb-6">Grooming rutin sangat penting untuk menjaga kesehatan dan kenyamanan si kecil
                        kesayangan Anda. Namun, kami memahami beberapa kendala yang mungkin Anda hadapi:</p>

                    <?php
                    $kendala = [
                        ['icon' => 'exclamation-triangle', 'text' => 'Tidak memahami teknik grooming yang tepat dan aman untuk Anabul Anda'],
                        ['icon' => 'clock', 'text' => 'Kesibukan yang membuat Anda tidak punya waktu cukup untuk melakukan grooming sendiri'],
                        ['icon' => 'question-circle', 'text' => 'Bingung memilih produk perawatan yang aman dan sesuai untuk kondisi Anabul Anda']
                    ];

                    foreach ($kendala as $item) {
                        echo '<div class="flex items-center mb-4 mr-2">
                                <div class="mr-3">
                                    <i class="fas fa-' . $item['icon'] . '"></i>
                                </div>
                                <p class="mb-0">' . $item['text'] . '</p>
                              </div>';
                    }
                    ?>

                    <a href="#"
                        class="inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                        <i class="fab fa-whatsapp text-xl mr-2"></i>Hubungi Kami
                    </a>
                    <span class="ml-4 fw-semibold">Konsultasi permasalahan anabulmu sekarang!</span>
                </div>
            </div>
        </div>
    </section>

    <!-- bagian perawatan -->
    <section class="py-12">
        <div class="container mx-auto px-4 py-8 bg-orange-100 rounded-4xl">
            <h2 class="text-center font-bold text-2xl mb-12">Ada Perawatan Apa Aja Kalau Kamu Grooming di Ling-Ling
                Petshop</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-6">
                <?php
                $services = [
                    [
                        'icon' => 'database',
                        'title' => 'Basic',
                        'description' => 'Layanan grooming anti kutu, grooming anti jamur, pemeriksaan kesehatan, dan pemberian obat cacing untuk hewan peliharaan Anda'
                    ],
                    [
                        'icon' => 'band-aid',
                        'title' => 'Mix',
                        'description' => 'Grooming basic + suntik vitamin untuk kesehatan optimal hewan peliharaan Anda'
                    ],
                    [
                        'icon' => 'trophy',
                        'title' => 'Complete',
                        'description' => 'Perawatan menyeluruh yang terdiri dari grooming mix + tes Revolution untuk pemeriksaan parasit pada hewan kesayangan Anda'
                    ]
                ];

                foreach ($services as $service) {
                    echo '<div class="text-center px-4">
                            <div class="text-4xl text-orange-500 mb-4">
                                <i class="fas fa-' . $service['icon'] . '"></i>
                            </div>
                            <h3 class="font-bold text-xl mb-4">' . $service['title'] . '</h3>
                            <p class="mb-0">' . $service['description'] . '</p>
                          </div>';
                }
                ?>
            </div>

            <div class="text-center mt-8 mb-8 rounded-4xl">
                <p class="font-bold mb-6">Benefit: Semua jenis grooming sudah termasuk gunting kuku, pembersihan
                    telinga, rapihkan bulu paw, dan bagian bokong</p>
                <a href="#booking-form"
                    class="inline-flex items-center justify-center bg-orange-500 text-white px-6 py-2 rounded font-semibold hover:bg-orange-600 transition duration-200">
                    <i class="fab fa-whatsapp text-xl mr-2"></i>Saya Mau Booking Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- bagian form booking -->
    <section id="booking-form" class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
                <div class="bg-orange-500 text-white p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2">Form Layanan Grooming</h3>
                    <p>Layanan perawatan untuk hewan kesayangan Anda</p>
                </div>

                <div class="p-8">
                    <form action="../auth/login.php" method="post" class="space-y-6" id="bookingForm">
                        <div class="container mx-auto">
                            <?php
                            $formFields = [
                                'nama_lengkap' => [
                                    'label' => 'Nama Pelanggan',
                                    'type' => 'text',
                                    'placeholder' => 'Masukkan nama lengkap Anda',
                                    'required' => true
                                ],
                                'nomor_telepon' => [
                                    'label' => 'Nomor Telepon',
                                    'type' => 'tel',
                                    'placeholder' => 'Masukkan nomor telepon Anda',
                                    'required' => true
                                ],
                                'pet_name' => [
                                    'label' => 'Nama Hewan Peliharaan',
                                    'type' => 'text',
                                    'placeholder' => 'Masukkan nama hewan peliharaan',
                                    'required' => true
                                ],
                                'pet_category' => [
                                    'label' => 'Kategori Hewan',
                                    'type' => 'select',
                                    'options' => [
                                        '' => 'Pilih kategori hewan',
                                        'kucing' => 'Kucing',
                                        'anjing' => 'Anjing',
                                        'kelinci' => 'Kelinci',
                                        'hamster' => 'Hamster'
                                    ]
                                ],
                                'pet_special' => ['label' => 'Ciri-Ciri Khusus Hewan (Opsional)', 'type' => 'text', 'placeholder' => 'Contoh: Warna bulu, ukuran, atau kondisi khusus'],
                                'service_type' => [
                                    'label' => 'Layanan Perawatan',
                                    'type' => 'select',
                                    'options' => [
                                        '' => 'Pilih layanan grooming',
                                        'basic' => 'Basic (Rp 150.000)',
                                        'mix' => 'Mix (Rp 200.000)',
                                        'complete' => 'Complete (Rp 250.000)'
                                    ]
                                ],
                                'service_date' => [
                                    'label' => 'Tanggal Perawatan',
                                    'type' => 'date',
                                    'placeholder' => 'Pilih tanggal perawatan',
                                    'min' => date('Y-m-d')
                                ],
                                'service_time' => [
                                    'label' => 'Waktu Perawatan',
                                    'type' => 'select',
                                    'options' => [
                                        '' => 'Pilih waktu grooming',
                                        'pagi' => '08:00 - 10:00',
                                        'siang' => '10:00 - 12:00',
                                        'sore' => '13:00 - 15:00',
                                        'sore-akhir' => '15:00 - 17:00'
                                    ]
                                ],
                                'groomer' => [
                                    'label' => 'Groomer',
                                    'type' => 'select',
                                    'options' => [
                                        '' => 'Pilih groomer',
                                        'andi' => 'Andi (Rp 50.000)',
                                        'budi' => 'Budi (Rp 45.000)',
                                        'cindy' => 'Cindy (Rp 55.000)',
                                        'dina' => 'Dina (Rp 50.000)'
                                    ]
                                ],
                                'total_price' => ['label' => 'Total Harga', 'type' => 'text', 'placeholder' => 'Rp0', 'readonly' => true],
                                'payment_method' => [
                                    'label' => 'Pembayaran',
                                    'type' => 'select',
                                    'options' => [
                                        '' => 'Pilih metode pembayaran',
                                        'cash' => 'Cash',
                                        'transfer' => 'Transfer Bank',
                                        'qris' => 'QRIS',
                                        'ewallet' => 'E-Wallet'
                                    ]
                                ]
                            ];

                            foreach ($formFields as $name => $field) {
                                echo '<div class="row mb-3 align-items-center">';
                                echo '<label class="col-md-4 col-form-label font-medium">' . $field['label'] . '</label>';
                                echo '<div class="col-md-8">';
                                $class = 'form-control w-full p-3 border rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200';
                                if ($field['type'] === 'select') {
                                    echo '<select name="' . $name . '" class="' . $class . '" ' .
                                        (isset($field['required']) ? 'required' : '') . '>';
                                    foreach ($field['options'] as $value => $label) {
                                        echo '<option value="' . $value . '">' . $label . '</option>';
                                    }
                                    echo '</select>';
                                } else {
                                    echo '<input type="' . $field['type'] . '" name="' . $name . '" 
                                      class="' . $class . '" 
                                      placeholder="' . $field['placeholder'] . '" 
                                      ' . (isset($field['readonly']) ? 'readonly' : '') . '
                                      ' . (isset($field['required']) ? 'required' : '') . '>';
                                }
                                echo '</div></div>';
                            }
                            ?>
                        </div>

                        <div class="flex justify-between mt-8">
                            <button type="reset"
                                class="px-4 py-3 border !border-orange-400 text-orange-500 rounded-lg font-semibold hover:bg-orange-100 transition duration-200">
                                Reset
                            </button>
                            <button type="submit"
                                class="px-4 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition duration-200">
                                Pesan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('bookingForm');
            const serviceType = form.querySelector('select[name="service_type"]');
            const groomer = form.querySelector('select[name="groomer"]');
            const totalPrice = form.querySelector('input[name="total_price"]');
            const petCategory = form.querySelector('select[name="pet_category"]');
            const serviceTime = form.querySelector('select[name="service_time"]');
            const paymentMethod = form.querySelector('select[name="payment_method"]');

            // Function to handle select color changes
            function handleSelectColor(select) {
                select.style.color = select.value ? '#000' : '#888';
            }

            // Add event listeners for all select elements
            [serviceType, groomer, petCategory, serviceTime, paymentMethod].forEach(select => {
                select.addEventListener('change', function () {
                    handleSelectColor(this);
                });
                // Set initial color
                handleSelectColor(select);
            });

            const hargaPerawatan = {
                'basic': 150000,
                'mix': 200000,
                'complete': 250000
            };

            const hargaGroomer = {
                'andi': 50000,
                'budi': 45000,
                'cindy': 55000,
                'dina': 50000
            };

            function updateTotalPrice() {
                const selectedService = serviceType.value;
                const selectedGroomer = groomer.value;

                if (selectedService && selectedGroomer) {
                    const total = hargaPerawatan[selectedService] + hargaGroomer[selectedGroomer];
                    totalPrice.value = 'Rp ' + total.toLocaleString('id-ID');
                } else {
                    totalPrice.value = 'Rp0';
                }
            }

            serviceType.addEventListener('change', updateTotalPrice);
            groomer.addEventListener('change', updateTotalPrice);
        });
    </script>
</body>

</html>