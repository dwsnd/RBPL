<?php
session_start();
require_once '../includes/db.php';


// Ngambil product_id dari url parameter
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$product_id) {
    header('Location: shop.php');
    exit();
}

try {
    // Fetch product details
    $query = "SELECT * FROM produk WHERE id_produk = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: shop.php');
        exit();
    }

    // Fetch related products (kategori)
    $related_query = "SELECT * FROM produk WHERE category = ? AND id_produk != ? LIMIT 4";
    $stmt = $pdo->prepare($related_query);
    $stmt->execute([$product['category'], $product_id]);
    $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error in detail_produk.php: " . $e->getMessage());
    header('Location: shop.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($product['name']); ?> - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        footer {
            padding: 40px 0;
        }

        /* WebKit browsers (Chrome, Safari, Edge) */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <?php require_once '../includes/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-2">
        <nav class="flex text-sm text-gray-600">
            <a href="shop.php" class="hover:text-orange-500 transition-colors">Beranda</a>
            <span class="mx-2">></span>
            <a href="shop.php" class="hover:text-orange-500 transition-colors">Produk</a>
            <span class="mx-2">></span>
            <span class="text-gray-900"><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>
    </div>

    <!-- Product Detail Section -->
    <div class="container mx-auto px-4 py-4">
        <div class="grid lg:grid-cols-2 gap-8 bg-white rounded-xl shadow-sm p-6">
            <!-- Product Image -->
            <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center border border-gray-200">
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="max-h-64 w-auto object-contain hover:scale-105 transition-transform duration-300">
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-2xl lg:text-2xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h1>
                    <div class="text-xl font-bold text-orange-500 mb-4">
                        Rp<?php echo number_format($product['price'], 0, ',', '.'); ?>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kuantitas</label>
                    <div class="flex items-center">
                        <button type="button"
                            class="w-8 h-8 border border-gray-300 rounded-l-md bg-white hover:bg-gray-50 flex items-center justify-center transition-colors"
                            onclick="decreaseQuantity()">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                            class="w-16 h-8 border-t border-b border-gray-300 text-center font-medium text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <button type="button"
                            class="w-8 h-8 border border-gray-300 rounded-r-md bg-white hover:bg-gray-50 flex items-center justify-center transition-colors"
                            onclick="increaseQuantity()">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                    <small class="text-gray-500 text-sm">Stok tersedia: <?php echo $product['stock']; ?></small>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                        onclick="addToCart()">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Add to Cart
                    </button>
                    <button
                        class="flex-1 border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                        onclick="buyNow()">
                        Buy Now
                    </button>
                </div>

                <!-- Product Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Informasi Produk</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kategori:</span>
                            <span class="font-medium"><?php echo ucfirst($product['category']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stok:</span>
                            <span class="font-medium"><?php echo $product['stock']; ?> unit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Berat:</span>
                            <span class="font-medium">1kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="container mx-auto px-4 py-2">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <div class="flex space-x-8">
                    <button class="tab-button py-2 px-4 border-b-2 border-orange-500 text-orange-500 font-medium"
                        onclick="showTab('description', this)">
                        DESKRIPSI
                    </button>
                    <button
                        class="tab-button py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium transition-colors"
                        onclick="showTab('composition', this)">
                        KOMPOSISI ANALITIS
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="description-tab" class="tab-content active">
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed mb-6 text-base">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Keunggulan Produk:</h4>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span class="text-gray-700">Nutrisi lengkap dan seimbang</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span class="text-gray-700">Mudah dicerna</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span class="text-gray-700">Meningkatkan kesehatan</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span class="text-gray-700">Rasa yang disukai hewan</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Cara Penyajian:</h4>
                            <ol class="space-y-3">
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white text-sm font-medium rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0">1</span>
                                    <span class="text-gray-700">Berikan sesuai takaran yang dianjurkan</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white text-sm font-medium rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0">2</span>
                                    <span class="text-gray-700">Sediakan air bersih yang cukup</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white text-sm font-medium rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0">3</span>
                                    <span class="text-gray-700">Simpan di tempat yang sejuk dan kering</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div id="composition-tab" class="tab-content">
                <div class="prose max-w-none">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">KOMPOSISI ANALITIS:</h4>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="bg-gray-50 rounded-lg">
                            <h5 class="font-medium text-gray-900 mb-3">Bahan Utama:</h5>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                Fish meat, Corn gluten meal, Chicken meal, Dried egg product, Chicken fat, Soy protein
                                isolate, Animal fat (dengan pengawet tocopherol campuran), Wheat flour, Wheat middlings,
                                Corn meal, Brewers dried yeast, Natural flavors, Potassium chloride, Salt, Choline
                                chloride, Vitamin E supplement, Taurine, Zinc sulfate, Ferrous sulfate,
                                L-ascorbyl-2-polyphosphate, Vitamin A supplement, Calcium pantothenate, Thiamine
                                mononitrate, Copper sulfate, Riboflavin supplement, Vitamin B-12 supplement, Pyridoxine
                                hydrochloride, Folic acid, Vitamin D-3 supplement, Calcium iodate, Biotin, Menadione
                                sodium bisulfite complex, Sodium selenite.
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900 mb-3">Konstituen Analitis:</h5>
                            <div class="text-gray-700 text-sm space-y-1">
                                <p>• Protein kasar (min) 18%</p>
                                <p>• Serat Kasar (max) 4%</p>
                                <p>• Kadar Air (maks) 10%</p>
                                <p>• Kalsium (min) 0.9%</p>
                                <p>• Vitamin E (min) 250 IU/kg</p>
                                <p>• Taurine 0.18%</p>
                                <p>• Vitamin C (min) 50 mg/kg</p>
                                <p>• Asam Lemak Omega-6 (min) 3.5%</p>
                                <p>• Asam lemak Omega-3 (min) 0.3%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (count($related_products) > 0): ?>
        <div class="container mx-auto px-4 py-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terkait</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($related_products as $related_product): ?>
                    <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer group"
                        onclick="window.location.href='detail_produk.php?id=<?php echo $related_product['id_produk']; ?>'">
                        <div class="aspect-square bg-gray-100 rounded-t-lg p-4 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($related_product['image']); ?>"
                                alt="<?php echo htmlspecialchars($related_product['name']); ?>"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-medium text-gray-800 text-sm leading-tight line-clamp-2 flex-1 pr-2"
                                    title="<?php echo htmlspecialchars($related_product['name']); ?>">
                                    <?php echo htmlspecialchars($related_product['name']); ?>
                                </h3>
                                <button class="p-1 hover:bg-gray-100 rounded transition-colors"
                                    onclick="event.stopPropagation();">
                                    <i class="far fa-heart text-orange-400 text-sm"></i>
                                </button>
                            </div>
                            <p class="text-orange-500 font-semibold">
                                Rp<?php echo number_format($related_product['price'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <!-- Custom Confirmation Modal -->
    <div id="customConfirmModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
            <div class="text-xl font-bold text-gray-900 mb-4 text-center" id="confirmMessage"></div>
            <div class="flex justify-end space-x-3">
                <button id="confirmCancelBtn"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-full hover:bg-gray-100 transition-colors text-sm">
                    Batal
                </button>
                <button id="confirmOKBtn"
                    class="px-6 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors text-sm">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
        // Confirmation modal function
        function showCustomConfirm(message, callback) {
            const modal = document.getElementById('customConfirmModal');
            const messageEl = document.getElementById('confirmMessage');
            const cancelBtn = document.getElementById('confirmCancelBtn');
            const okBtn = document.getElementById('confirmOKBtn');

            messageEl.textContent = message;
            modal.classList.remove('hidden');

            const handleConfirm = () => {
                modal.classList.add('hidden');
                callback(true);
                cleanup();
            };

            const handleCancel = () => {
                modal.classList.add('hidden');
                callback(false);
                cleanup();
            };

            const cleanup = () => {
                okBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            okBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);
        }

        // Quantity functions
        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.max);

            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        }

        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);

            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }

        // Tab functions
        function showTab(tabName, buttonElement) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-orange-500', 'text-orange-500');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active class to clicked button
            buttonElement.classList.remove('border-transparent', 'text-gray-500');
            buttonElement.classList.add('border-orange-500', 'text-orange-500');
        }

        // Cart functions
        function addToCart() {
            <?php if (isset($_SESSION['id_pelanggan'])): ?>
                const quantity = document.getElementById('quantity').value;
                const productId = <?php echo $product_id; ?>;

                // Add AJAX call to add product to cart
                fetch('../includes/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showCustomConfirm('Produk berhasil ditambahkan ke keranjang!', (result) => {
                                if (result) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            showCustomConfirm('Gagal menambahkan produk ke keranjang.', (result) => {
                                if (result) {
                                    window.location.reload();
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showCustomConfirm('Terjadi kesalahan.', (result) => {
                            if (result) {
                                window.location.reload();
                            }
                        });
                    });
            <?php else: ?>
                showCustomConfirm('Silakan login terlebih dahulu.', (result) => {
                    if (result) {
                        window.location.href = '../auth/login.php';
                    }
                });
            <?php endif; ?>
        }

        function buyNow() {
            <?php if (isset($_SESSION['id_pelanggan'])): ?>
                const quantity = document.getElementById('quantity').value;
                const productId = <?php echo $product_id; ?>;
                window.location.href = `../profilpelanggan/keranjang.php?product_id=${productId}&quantity=${quantity}`;
            <?php else: ?>
                showCustomConfirm('Silakan login terlebih dahulu.', (result) => {
                    if (result) {
                        window.location.href = '../auth/login.php';
                    }
                });
            <?php endif; ?>
        }

        function toggleWishlist() {
            <?php if (isset($_SESSION['id_pelanggan'])): ?>
                const heartIcon = document.querySelector('.heart-btn i');
                const isFavorite = heartIcon.classList.contains('fas');

                if (isFavorite) {
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                } else {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                }

                // Add AJAX call to toggle wishlist
                fetch('../includes/toggle_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=<?php echo $product_id; ?>`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            // Revert the icon change if failed
                            if (isFavorite) {
                                heartIcon.classList.remove('far');
                                heartIcon.classList.add('fas');
                            } else {
                                heartIcon.classList.remove('fas');
                                heartIcon.classList.add('far');
                            }
                        }
                    });
            <?php else: ?>
                alert('Silakan login terlebih dahulu.');
                window.location.href = '../auth/login.php';
            <?php endif; ?>
        }
    </script>
</body>

</html>