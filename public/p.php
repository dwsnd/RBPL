<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop - Toko Hewan Peliharaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
    </style>
</head>

<body class="bg-gray-50">
    <!-- Services Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-paw text-orange-500 mr-2"></i>
                    <strong>Layanan Kami</strong>
                </h2>
                <p class="text-gray-600 text-sm">Pilih Produk Berdasarkan Hewan Peliharaan</p>
            </div>

            <div class="flex justify-center">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl">
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="aset/kucingg.png" alt="Kucing" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Kucing</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('hamster')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="aset/hamster.png" alt="Hamster" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Hamster</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('anjing')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="aset/anjeng.png" alt="Anjing" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Anjing</p>
                    </div>
                    <div class="service-card bg-white rounded-lg p-4 shadow-sm border cursor-pointer text-center"
                        onclick="filterByCategory('kelinci')">
                        <div
                            class="w-16 h-16 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center overflow-hidden">
                            <img src="aset/kelinci.png" alt="Kelinci" class="w-12 h-12 object-contain">
                        </div>
                        <p class="font-semibold text-sm text-gray-800">Kelinci</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-8 bg-gray-50">
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

    <script>
        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        let productsPerPage = 15;
        let selectedCategory = '';
        let isLoading = false;

        // Load products from database
        const loadProducts = async () => {
            if (isLoading) return;
            isLoading = true;

            document.getElementById('loadingSpinner').style.display = 'flex';

            try {
                const response = await fetch('get_products.php');
                const data = await response.json();

                if (data.success) {
                    allProducts = data.products;
                    filteredProducts = [...allProducts];
                    renderProducts();
                    updateProductCount();
                } else {
                    console.error('Error loading products:', data.message);
                    showError('Failed to load products');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Network error occurred');
            } finally {
                document.getElementById('loadingSpinner').style.display = 'none';
                isLoading = false;
            }
        };

        // Render products
        const renderProducts = () => {
            const container = document.getElementById('productsContainer');
            const startIndex = (currentPage - 1) * productsPerPage;
            const endIndex = startIndex + productsPerPage;
            const productsToShow = filteredProducts.slice(startIndex, endIndex);

            if (productsToShow.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No products found</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = productsToShow.map(product => `
                <div class="product-card bg-white rounded-lg overflow-hidden border border-orange-200">
                    <div class="aspect-square bg-gray-100 relative p-2">
                        <img src="${product.image || 'aset/produk.png'}" alt="${product.name}" class="w-full h-full object-contain">
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
        };

        // Render pagination
        const renderPagination = () => {
            const container = document.getElementById('pagination');
            const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let paginationHTML = '';

            // Show max 5 page numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);

            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            // Previous button
            if (currentPage > 1) {
                paginationHTML += `<button onclick="changePage(${currentPage - 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">‹</button>`;
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    paginationHTML += `<button class="px-3 py-1 text-sm bg-orange-500 text-white rounded">${i}</button>`;
                } else {
                    paginationHTML += `<button onclick="changePage(${i})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">${i}</button>`;
                }
            }

            // Next button
            if (currentPage < totalPages) {
                paginationHTML += `<button onclick="changePage(${currentPage + 1})" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">›</button>`;
            }

            container.innerHTML = paginationHTML;
        };

        // Change page
        const changePage = (page) => {
            currentPage = page;
            renderProducts();
            window.scrollTo({ top: 300, behavior: 'smooth' });
        };

        // Filter by category
        const filterByCategory = (category) => {
            selectedCategory = category;
            applyFilters();
        };

        // Apply filters
        const applyFilters = () => {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            filteredProducts = allProducts.filter(product => {
                const matchesCategory = !selectedCategory || product.category === selectedCategory;
                const matchesSearch = !searchTerm || product.name.toLowerCase().includes(searchTerm);
                return matchesCategory && matchesSearch;
            });

            currentPage = 1;
            renderProducts();
            updateProductCount();
        };

        // Update product count
        const updateProductCount = () => {
            const currentCount = Math.min(currentPage * productsPerPage, filteredProducts.length);
            document.getElementById('currentCount').textContent = currentCount;
            document.getElementById('totalCount').textContent = filteredProducts.length;
        };

        // Show error message
        const showError = (message) => {
            const container = document.getElementById('productsContainer');
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-3"></i>
                    <p class="text-red-500">${message}</p>
                    <button onclick="loadProducts()" class="mt-3 px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">
                        Try Again
                    </button>
                </div>
            `;
        };

        // Event listeners
        document.getElementById('searchInput').addEventListener('input', () => {
            setTimeout(applyFilters, 300); // Debounce search
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
        });
    </script>
</body>

</html>