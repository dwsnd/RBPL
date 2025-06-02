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

        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #f97316;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .button-pagination {
            border: 1px solid rgb(97, 87, 80);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /*Pagination*/
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination .page-link {
            background-color: white;
            border: 1px solid grey;
            color: grey;
            font-weight: 600;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: #f97316;
            border: 1px solid #f97316;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f5f5f5;
        }

        .service-card {
            transition: all 0.3s ease;
            position: relative;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        }

        .service-card:hover {
            transform: scale(1.02);
        }

        .service-card .pet-icon {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            height: 100%;
        }

        .service-card .pet-name {
            position: relative;
            z-index: 2;
            color: #1a1a1a;
        }

        .service-card.active .pet-name {
            color: #fff;
        }

        footer {
            padding: 40px 0;
            background-color: #f8f9fa;
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

    <!-- Filter hewan -->
    <section class="text-center py-5">
        <div class="container">
            <h2 class="text-center font-bold text-2xl mb-5">Pilih Produk Berdasarkan Hewan Peliharaan</h2>
            <div class="flex justify-center">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-20">
                    <div>
                        <div class="service-card cursor-pointer mb-2" onclick="filterByCategory('kucing')"
                            style="background-image: url('../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../aset/kucingtoko.png" alt="Kucing" class="w-48 h-48 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Kucing</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2 w-48 h-48" onclick="filterByCategory('hamster')"
                            style="background-image: url('../aset/vectorfilterhewan.png')">
                            <div class="pet-icon item-center justify-center overflow-hidden">
                                <img src="../aset/hamstertoko.png" alt="Hamster" class="w-38 h-38 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Hamster</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2" onclick="filterByCategory('anjing')"
                            style="background-image: url('../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../aset/anjingtoko.png" alt="Anjing" class="w-48 h-48 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Anjing</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2" onclick="filterByCategory('kelinci')"
                            style="background-image: url('../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../aset/kelincitoko.png" alt="Kelinci" class="w-48 h-48 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Kelinci</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Products Section -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4">
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="text-sm text-gray-600" id="productCount">
                    Showing <span id="currentCount">0</span> of <span id="totalCount">0</span> results
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <input type="text" placeholder="Search"
                            class="pl-8 pr-4 py-2 text-sm border border-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 w-48"
                            id="searchInput">
                        <i
                            class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                    <button
                        class="px-3 py-2 bg-orange-500 text-white text-sm rounded-md hover:bg-orange-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="productsContainer">
                <!-- Products-->
            </div>

            <!-- Pagination -->
            <div class="flex justify-center items-center mt-8 space-x-2" id="pagination">
            </div>
        </div>
    </section>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
        style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto"></div>
            <p class="text-center mt-3 text-gray-600 text-sm">Loading products...</p>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script>
        // Variabel DOM utama
        const productsContainer = document.getElementById('productsContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const searchInput = document.getElementById('searchInput');
        const currentCount = document.getElementById('currentCount');
        const totalCount = document.getElementById('totalCount');
        const pagination = document.getElementById('pagination');

        // Variabel data utama
        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        const productsPerPage = 15;
        let selectedCategory = '';
        let isLoading = false;
        let searchTimeout;

        // Fungsi Alert/Error reusable
        /*
         * Menampilkan pesan alert/error di container produk
         * @param {string} message - Pesan yang akan ditampilkan
         * @param {string} type - Jenis alert ('error' atau 'info')
         */
        const showAlert = (message, type = 'error') => {
            productsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas ${type === 'error' ? 'fa-exclamation-triangle text-red-400' : 'fa-info-circle text-blue-400'} text-3xl mb-3"></i>
                <p class="${type === 'error' ? 'text-red-500' : 'text-blue-500'}">${message}</p>
                <button onclick="loadProducts()" class="mt-3 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">
                    Coba Lagi
                </button>
            </div>
        `;
        };

        // Fungsi menampilkan pesan pilih kategori
        const showSelectCategoryMessage = () => {
            productsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-paw text-orange-400 text-4xl mb-3"></i>
                <p class="text-gray-600 font-medium">Silakan pilih kategori hewan peliharaan untuk melihat produk</p>
            </div>
        `;
            pagination.innerHTML = '';
            currentCount.textContent = '0';
            totalCount.textContent = '0';
        };

        // Fungsi mengambil data produk dari server dan menerapkan filter
        const loadProducts = async () => {
            if (isLoading) return;
            isLoading = true;
            loadingSpinner.style.display = 'flex';

            try {
                const response = await fetch('../includes/get_products.php');
                const data = await response.json();

                if (data.success) {
                    allProducts = data.products;
                    if (!selectedCategory) {
                        showSelectCategoryMessage();
                    } else {
                        applyFilters();
                    }
                } else {
                    showAlert('Gagal memuat produk');
                }
            } catch {
                showAlert('Terjadi kesalahan jaringan');
            } finally {
                loadingSpinner.style.display = 'none';
                isLoading = false;
            }
        };

        // Fungsi menampilkan produk ke dalam grid sesuai filter & halaman
        const renderProducts = () => {
            if (!selectedCategory) {
                showSelectCategoryMessage();
                return;
            }

            const start = (currentPage - 1) * productsPerPage;
            const end = start + productsPerPage;
            const productsToShow = filteredProducts.slice(start, end);

            if (!productsToShow.length) {
                productsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">Produk tidak ditemukan</p>
                </div>
            `;
                pagination.innerHTML = '';
                updateProductCount();
                return;
            }

            // Render produk dengan map, lebih efisien
            productsContainer.innerHTML = productsToShow.map(product => `
            <div class="product-card rounded-lg cursor-pointer" onclick="window.location.href='../auth/login.php'">
                <div class="aspect-square bg-gray-200 relative p-2">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-contain">
                </div>
                <div class="p-3">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-medium text-sm text-gray-800 line-clamp-2 leading-tight pr-2" title="${product.name}">
                            ${product.name}
                        </h3>
                        <button class="w-6 h-6 flex justify-center items-end" onclick="event.stopPropagation(); window.location.href='../auth/login.php'">
                            <i class="far fa-heart text-orange-400 text-lg"></i>
                        </button>
                    </div>
                    <p class="text-orange-600 font-semibold text-sm">Rp${parseInt(product.price).toLocaleString('id-ID')}</p>
                </div>
            </div>
        `).join('');
            renderPagination();
            updateProductCount();
        };

        // Fungsi tombol pagination
        const renderPagination = () => {
            const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }
            let html = '<ul class="pagination">';
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);

            if (currentPage > 1)
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;"> < </a></li>`;
            for (let i = start; i <= end; i++) {
                html += i === currentPage
                    ? `<li class="page-item active"><a class="page-link" href="#">${i}</a></li>`
                    : `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
            }
            if (currentPage < totalPages)
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;"> > </a></li>`;
            html += '</ul>';
            pagination.innerHTML = html;
        };

        // Fungsi mengganti halaman produk - @param {number} page - Nomor halaman
        window.changePage = (page) => {
            currentPage = page;
            renderProducts();
            // Scroll ke bagian produk
            const productsSection = document.querySelector('.py-8.bg-white');
            if (productsSection) {
                productsSection.scrollIntoView({ behavior: 'smooth' });
            }
        };

        // Fungsi filter produk berdasarkan kategori - @param {string} category - Nama kategori
        window.filterByCategory = (category) => {
            selectedCategory = category;
            // Reset all cards to default background
            document.querySelectorAll('.service-card').forEach(card => {
                card.style.backgroundImage = "url('../aset/vectorfilterhewan.png')";
                card.classList.remove('active');
            });
            // Set active card background
            const activeCard = document.querySelector(`.service-card[onclick="filterByCategory('${category}')"]`);
            if (activeCard) {
                activeCard.style.backgroundImage = "url('../aset/vectorfilterhewan_pick.png')";
                activeCard.classList.add('active');
            }
            applyFilters();
        };

        // Fungsi filter pencarian dan kategori
        const applyFilters = () => {
            const searchTerm = searchInput.value.toLowerCase();
            filteredProducts = allProducts.filter(product =>
                (!selectedCategory || product.category === selectedCategory) &&
                (!searchTerm || product.name.toLowerCase().includes(searchTerm))
            );
            currentPage = 1;
            renderProducts();
        };

        // Fungsi update tampilan jumlah produk yang ditampilkan
        const updateProductCount = () => {
            // Menampilkan jumlah produk yang sedang tampil dan total hasil filter
            currentCount.textContent = Math.min(currentPage * productsPerPage, filteredProducts.length);
            totalCount.textContent = filteredProducts.length;
        };

        // Fungsi menambahkan debounce pada input pencarian
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>