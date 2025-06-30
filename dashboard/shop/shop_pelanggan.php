<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Fetch user data if logged in
$user_data = [];
if (isset($_SESSION['id_pelanggan'])) {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
    }
}
?>

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

        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #f97316;
            border-radius: 0.5rem;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .product-image-container {
            position: relative;
            width: 100%;
            padding-top: 100%;
            /* Creates a square aspect ratio */
            background-color: #f8f8f8;
        }

        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
        }

        .product-info {
            padding: 0.75rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
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
        }

        /* Popup Notification Styles */
        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .popup-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require_once '../../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden">
        <!-- SHAPE BESAR KANAN -->
        <img src="../../aset/Shape2.png" class="shape-main" alt="Shape">
        <!-- SHAPE KECIL KIRI ATAS -->
        <img src="../../aset/Shape.png" class="shape-leftup" alt="Shape2">
        <!-- SHAPE KECIL KIRI BAWAH -->
        <img src="../../aset/Shape1.png" class="shape-leftdown" alt="Shape1">
        <div class="container d-flex flex-wrap align-items-center justify-content-between position-relative"
            style="z-index:2;">
            <div class="col-lg-6 mb-4 text-lg-start text-center">
                <h6 class="text-orange-500 text-base font-semibold mb-2">Ling-Ling Pet Shop</h6>
                <h1 class="text-4xl font-bold text-grey-900 leading-snug mb-3">Mereka Nggak Bisa Belanja,
                    <br>Tapi Kamu Bisa Bikin
                    <br>Mereka Bahagia!
                </h1>
                <a href="shop_pelanggan.php" class="btn btn-black text-base mt-2">Mulai Belanja</a>
            </div>
        </div>
        <img src="../../aset/cat&dog.png" class="image-catdog" alt="Hewan Peliharaan">
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
                            style="background-image: url('../../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../../aset/kucingtoko.png" alt="Kucing" class="w-48 h-48 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Kucing</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2 w-48 h-48" onclick="filterByCategory('hamster')"
                            style="background-image: url('../../aset/vectorfilterhewan.png')">
                            <div class="pet-icon item-center justify-center overflow-hidden">
                                <img src="../../aset/hamstertoko.png" alt="Hamster" class="w-38 h-38 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Hamster</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2" onclick="filterByCategory('anjing')"
                            style="background-image: url('../../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../../aset/anjingtoko.png" alt="Anjing" class="w-48 h-48 object-contain">
                            </div>
                        </div>
                        <p class="pet-name font-semibold text-base">Anjing</p>
                    </div>
                    <div>
                        <div class="service-card cursor-pointer mb-2" onclick="filterByCategory('kelinci')"
                            style="background-image: url('../../aset/vectorfilterhewan.png')">
                            <div class="pet-icon items-center justify-center overflow-hidden">
                                <img src="../../aset/kelincitoko.png" alt="Kelinci" class="w-48 h-48 object-contain">
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
                    Menampilkan <span id="currentCount">0</span> dari <span id="totalCount">0</span> hasil
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <input type="text" placeholder="Cari"
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
            <p class="text-center mt-3 text-gray-600 text-sm">Memuat Produk...</p>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <!-- Add popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

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

        // Fungsi debug untuk melihat data
        const debugLog = (message, data = null) => {
            console.log(`[DEBUG] ${message}`, data);
        };

        // Fungsi Alert/Error reusable
        const showAlert = (message, type = 'error') => {
            debugLog(`Showing alert: ${type}`, message);
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
            debugLog('Showing select category message');
            productsContainer.innerHTML = `
            <div class="col-span-full text-center py-16">
                <i class="fas fa-paw text-orange-400 text-4xl mb-3"></i>
                <p class="text-gray-600 text-base font-semibold">Silakan pilih kategori hewan peliharaan untuk melihat produk</p>
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
            debugLog('Loading products...');

            try {
                const response = await fetch('../../includes/get_products.php');
                debugLog('Response status:', response.status);

                const data = await response.json();
                debugLog('Response data:', data);

                if (data.success) {
                    allProducts = data.products;
                    debugLog('All products loaded:', allProducts.length);
                    if (allProducts.length > 0) {
                        debugLog('First product:', {
                            id: allProducts[0].id,
                            name: allProducts[0].name,
                            image: allProducts[0].image,
                            target_hewan: allProducts[0].target_hewan
                        });
                    }

                    if (!selectedCategory) {
                        showSelectCategoryMessage();
                    } else {
                        applyFilters();
                    }
                } else {
                    debugLog('Failed to load products:', data.message);
                    showAlert(`Gagal memuat produk: ${data.message}`);
                }
            } catch (error) {
                debugLog('Network error:', error);
                showAlert('Terjadi kesalahan jaringan');
            } finally {
                loadingSpinner.style.display = 'none';
                isLoading = false;
            }
        };

        // Fungsi menampilkan produk ke dalam grid sesuai filter & halaman
        const renderProducts = () => {
            debugLog('Rendering products...', {
                selectedCategory,
                filteredProductsCount: filteredProducts.length,
                currentPage
            });

            if (!selectedCategory) {
                showSelectCategoryMessage();
                return;
            }

            const start = (currentPage - 1) * productsPerPage;
            const end = start + productsPerPage;
            const productsToShow = filteredProducts.slice(start, end);

            debugLog('Products to show:', productsToShow.length);

            if (!productsToShow.length) {
                debugLog('No products to show');
                productsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500">Produk tidak ditemukan untuk kategori "${selectedCategory}"</p>
                <p class="text-gray-400 text-sm mt-2">Total produk tersedia: ${allProducts.length}</p>
                <p class="text-gray-400 text-sm">Produk setelah filter: ${filteredProducts.length}</p>
            </div>
        `;
                pagination.innerHTML = '';
                updateProductCount();
                return;
            }

            // Render produk dengan improved error handling
            productsContainer.innerHTML = productsToShow.map((product, index) => {
                const heartIconClass = product.is_favorited ? 'fas text-red-500' : 'far text-orange-400';
                return `
            <div class="product-card rounded-lg cursor-pointer" onclick="window.location.href='detail_produk.php?id=${product.id}'">
                <div class="product-image-container">
                    <img src="${product.image}" 
                         alt="${product.name}" 
                         class="product-image" 
                         onerror="handleImageError(this, '${product.image}', '${product.target_hewan}')"
                         onload="console.log('Image loaded successfully:', '${product.image}')">
                </div>
                <div class="product-info">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 leading-tight pr-2" title="${product.name}">
                            ${product.name}
                        </h3>
                        <button class="w-6 h-6 flex justify-center items-end" onclick="event.stopPropagation(); addToFavorites(${product.id}, this)">
                            <i class="${heartIconClass} fa-heart text-lg"></i>
                        </button>
                    </div>
                    <p class="text-orange-600 font-medium text-sm">Rp${parseInt(product.price).toLocaleString('id-ID')}</p>
                    <p class="text-gray-400 text-xs">Kategori: ${product.target_hewan}</p>
                </div>
            </div>
        `;
            }).join('');

            // Update pagination and count
            renderPagination();
            updateProductCount();
        };

        // Fungsi handle error gambar yang diperbaiki
        window.handleImageError = (img, originalImage, originalCategory) => {
            console.log('Image error for:', originalImage);
            console.log('Category:', originalCategory);

            // Array alternatif path untuk akses dari folder public
            const alternatives = [
                // Coba path langsung dari uploads
                originalImage,
                // Coba dengan struktur folder kategori
                `../../uploads/produk/${originalCategory.toLowerCase()}/${originalImage.split('/').pop()}`,
                // Coba tanpa kategori folder
                `../../uploads/produk/${originalImage.split('/').pop()}`,
                // Coba di folder aset
                `../../aset/produk/${originalImage.split('/').pop()}`,
                // Fallback default
                '../../aset/default-product.png'
            ];

            let tried = parseInt(img.getAttribute('data-tried') || '0');

            if (tried < alternatives.length - 1) {
                img.setAttribute('data-tried', (tried + 1).toString());
                console.log(`Trying alternative ${tried + 1}:`, alternatives[tried]);
                img.src = alternatives[tried];
            } else {
                console.log('All alternatives failed, using default image');
                img.src = '../../aset/default-product.png';
                img.removeAttribute('data-tried');
            }
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

        // Fungsi mengganti halaman produk
        window.changePage = (page) => {
            currentPage = page;
            renderProducts();
            const productsSection = document.querySelector('.py-8.bg-white');
            if (productsSection) {
                productsSection.scrollIntoView({ behavior: 'smooth' });
            }
        };

        // Fungsi filter produk berdasarkan kategori
        window.filterByCategory = (category) => {
            debugLog('Filtering by category:', category);
            selectedCategory = category.toLowerCase(); // Pastikan lowercase

            // Reset all cards to default background
            document.querySelectorAll('.service-card').forEach(card => {
                card.style.backgroundImage = "url('../../aset/vectorfilterhewan.png')";
                card.classList.remove('active');
            });

            // Set active card background
            const activeCard = document.querySelector(`.service-card[onclick="filterByCategory('${category}')"]`);
            if (activeCard) {
                activeCard.style.backgroundImage = "url('../../aset/vectorfilterhewan_pick.png')";
                activeCard.classList.add('active');
            }

            applyFilters();
        };

        // Fungsi filter pencarian dan kategori
        const applyFilters = () => {
            const searchTerm = searchInput.value.toLowerCase();
            debugLog('Applying filters:', { selectedCategory, searchTerm });
            debugLog('All products target_hewan:', allProducts.map(p => p.target_hewan));

            filteredProducts = allProducts.filter(product => {
                const categoryMatch = !selectedCategory || product.target_hewan === selectedCategory;
                const searchMatch = !searchTerm || product.name.toLowerCase().includes(searchTerm);

                debugLog(`Product: ${product.name}, Target Hewan: ${product.target_hewan}, Matches: ${categoryMatch && searchMatch}`);

                return categoryMatch && searchMatch;
            });

            debugLog('Filtered products count:', filteredProducts.length);
            currentPage = 1;
            renderProducts();
        };

        // Fungsi update tampilan jumlah produk yang ditampilkan
        const updateProductCount = () => {
            const showing = Math.min(currentPage * productsPerPage, filteredProducts.length);
            currentCount.textContent = showing;
            totalCount.textContent = filteredProducts.length;
            debugLog('Updated count:', { showing, total: filteredProducts.length });
        };

        // Fungsi menambahkan debounce pada input pencarian
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });

        // Fungsi untuk menampilkan notifikasi popup
        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popupNotification');
            popup.textContent = message;
            popup.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            popup.classList.add('show');

            // Hide popup after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        // Fungsi untuk menambah ke favorit
        function addToFavorites(productId, button) {
            <?php if (isset($_SESSION['id_pelanggan'])): ?>
                fetch('../../includes/toggle_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const heartIcon = button.querySelector('i');

                            // Update heart icon appearance
                            if (data.is_favorite) {
                                heartIcon.classList.remove('far', 'text-orange-400');
                                heartIcon.classList.add('fas', 'text-red-500');
                            } else {
                                heartIcon.classList.remove('fas', 'text-red-500');
                                heartIcon.classList.add('far', 'text-orange-400');
                            }

                            // Update the product data in memory
                            const product = allProducts.find(p => p.id === productId);
                            if (product) {
                                product.is_favorited = data.is_favorite;
                            }

                            // Update favorites count in header if available
                            const favoritesCountElement = document.querySelector('.fa-regular.fa-heart + span');
                            if (favoritesCountElement && data.favorites_count !== undefined) {
                                favoritesCountElement.textContent = data.favorites_count;
                            }

                            // Show success message
                            showPopup(data.message, 'success');
                        } else {
                            showPopup('Gagal menambahkan ke favorit: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showPopup('Terjadi kesalahan saat memproses favorit', 'error');
                    });
            <?php else: ?>
                window.location.href = '../auth/login.php';
            <?php endif; ?>
        }
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>