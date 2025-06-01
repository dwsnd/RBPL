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

        .product-card {
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: scale(1.02);
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
            <h2 class="text-center font-bold text-2xl mb-4">Pilih Produk Berdasarkan Hewan Peliharaan</h2>
            <div class="flex justify-center">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl">
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('kucing')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="../aset/kucingtoko.png" alt="Kucing" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Kucing</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('hamster')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="../aset/hamstertoko.png" alt="Hamster" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Hamster</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('anjing')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="../aset/anjingtoko.png" alt="Anjing" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Anjing</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('kelinci')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="../aset/kelincitoko.png" alt="Kelinci" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Kelinci</p>
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
                            class="pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500 w-48"
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
                <!-- Products will be loaded here -->
            </div>

            <!-- Pagination -->
            <div class="flex justify-center items-center mt-8 space-x-2" id="pagination">
                <!-- Pagination will be loaded here -->
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
                    applyFilters();
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
            <div class="product-card bg-white rounded-lg overflow-hidden border border-orange-200">
                <div class="aspect-square bg-gray-100 relative p-2">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-contain">
                    <button class="absolute top-2 right-2 w-6 h-6 bg-white rounded-full shadow-sm flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="far fa-heart text-gray-400 text-xs"></i>
                    </button>
                </div>
                <div class="p-3">
                    <h3 class="font-medium text-xs text-gray-800 mb-1 line-clamp-2 leading-tight" title="${product.name}">
                        ${product.name}
                    </h3>
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
            let html = '';
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);

            if (currentPage > 1)
                html += `<button onclick="changePage(${currentPage - 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">‹</button>`;
            for (let i = start; i <= end; i++) {
                html += i === currentPage
                    ? `<button class="px-3 py-1 text-sm bg-orange-500 text-white rounded">${i}</button>`
                    : `<button onclick="changePage(${i})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">${i}</button>`;
            }
            if (currentPage < totalPages)
                html += `<button onclick="changePage(${currentPage + 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">›</button>`;
            pagination.innerHTML = html;
        };

        // Fungsi mengganti halaman produk - @param {number} page - Nomor halaman
        window.changePage = (page) => {
            currentPage = page;
            renderProducts();
            window.scrollTo({ top: 300, behavior: 'smooth' });
        };

        // Fungsi filter produk berdasarkan kategori - @param {string} category - Nama kategori
        window.filterByCategory = (category) => {
            selectedCategory = category;
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