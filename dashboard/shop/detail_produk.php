<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

$user_data = [];
if (isset($_SESSION['id_pelanggan'])) {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
    }

    // Get favorites count
    $favorites_query = "SELECT COUNT(*) as count FROM favorit WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($favorites_query);
    $stmt->execute([$id_pelanggan]);
    $favorites_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} else {
    $favorites_count = 0;
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$product_id) {
    header('Location: shop_pelanggan.php');
    exit();
}

// Fetch product details
$query = "SELECT * FROM produk WHERE id_produk = $product_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: shop_pelanggan.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Fetch related products (same category)
$related_query = "SELECT * FROM produk WHERE kategori = '{$product['kategori']}' AND id_produk != $product_id LIMIT 5";
$related_result = mysqli_query($conn, $related_query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c'
                        }
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .quantity-input {
            -moz-appearance: textfield;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize favorites count in header
            const favoritesCountElement = document.querySelector('.fa-regular.fa-heart + span');
            if (favoritesCountElement) {
                favoritesCountElement.textContent = <?php echo $favorites_count; ?>;
            }
        });
    </script>
</head>

<body class="bg-white font-poppins">
    <!-- Navbar -->
    <?php require_once '../../includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm">
            <ol class="flex items-center space-x-2">
                <li><a href="shop_pelanggan.php" class="text-gray-600 hover:text-orange-500 transition-colors">Toko</a>
                </li>
                <li class="text-gray-400">></li>
                <li class="text-gray-900 font-medium"><?php echo htmlspecialchars($product['nama_produk']); ?></li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Image -->
            <div class="w-full h-full flex items-center">
                <?php
                $image_path = $product['foto_utama'];
                if (!empty($image_path)) {
                    // If the path already includes uploads or assets, use it as is
                    if (strpos($image_path, 'uploads/') === 0 || strpos($image_path, 'assets/') === 0) {
                        $final_path = '../../' . $image_path;
                    } else {
                        // Try different possible paths
                        $possible_paths = [
                            '../../uploads/produk/' . $product['target_hewan'] . '/' . $image_path,
                            '../../uploads/produk/' . $image_path,
                            '../../assets/img/produk/' . $image_path,
                            $image_path
                        ];
                        $final_path = $possible_paths[0]; // Default to first path
                    }
                } else {
                    $final_path = '../../aset/default-product.png';
                }
                ?>
                <div class="w-full bg-gray-100 rounded-lg p-6 flex items-center justify-center h-[calc(100%-1rem)]">
                    <img src="<?php echo $final_path; ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>"
                        class="max-w-full max-h-full object-contain transition-transform duration-300 hover:scale-105"
                        onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6 h-full">
                <div>
                    <h1 class="text-lg lg:text-2xl font-semibold text-gray-900 mb-3">
                        <?php echo htmlspecialchars($product['nama_produk']); ?>
                    </h1>
                    <p class="text-lg lg:text-2xl font-semibold text-orange-500 mb-4">
                        Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?>
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-base font-medium text-gray-900 mb-2">Deskripsi</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-base font-medium text-gray-900 mb-2">Stok</h3>
                        <p class="text-sm text-gray-600"><?php echo $product['stok']; ?> unit tersedia</p>
                    </div>

                    <div>
                        <h3 class="text-base font-medium text-gray-900 mb-2">Jumlah</h3>
                        <div class="flex items-center space-x-2">
                            <button
                                class="w-8 h-8 flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 transition-colors rounded"
                                onclick="decreaseQuantity()">-</button>
                            <input type="number" id="quantity"
                                class="quantity-input w-16 h-8 text-center border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                value="1" min="1" max="<?php echo $product['stok']; ?>">
                            <button
                                class="w-8 h-8 flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 transition-colors rounded"
                                onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex space-x-3">
                        <button
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-base font-medium py-2 px-4 rounded-lg transition-all duration-300 hover:shadow-md hover:shadow-lg"
                            onclick="addToCart(<?php echo $product['id_produk']; ?>)">
                            <i class="fas fa-shopping-cart mr-2"></i>Tambahkan ke Keranjang
                        </button>
                        <button
                            class="w-10 h-10 flex items-center justify-center border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white transition-all duration-300 rounded-lg"
                            onclick="addToFavorites(<?php echo $product['id_produk']; ?>)">
                            <i class="<?php
                            // Check if product is in favorites
                            $check_favorite = $pdo->prepare("SELECT id_favorit FROM favorit WHERE id_pelanggan = ? AND id_produk = ?");
                            $check_favorite->execute([$_SESSION['id_pelanggan'], $product['id_produk']]);
                            echo $check_favorite->fetch() ? 'fas text-red-500' : 'far';
                            ?> fa-heart"></i>
                        </button>
                    </div>
                    <button
                        class="w-full border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white font-base font-medium py-2 px-4 rounded-lg transition-all duration-300"
                        onclick="buyNow(<?php echo $product['id_produk']; ?>)">
                        Beli Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="mt-12 bg-white rounded-xl p-6 shadow-sm">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <div class="flex space-x-8">
                    <button
                        class="tab-button py-2 px-1 border-b-2 border-orange-500 text-orange-500 font-medium text-base"
                        onclick="showTab('description', this)">
                        DESKRIPSI
                    </button>
                    <button
                        class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-orange-500 font-medium text-base transition-colors"
                        onclick="showTab('composition', this)">
                        KOMPOSISI ANALITIS
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="description-tab" class="tab-content">
                <div class="space-y-6">
                    <p class="text-gray-700 text-sm leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Keunggulan Produk:</h4>
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
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Cara Penyajian:</h4>
                            <ol class="space-y-3">
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0">1</span>
                                    <span class="text-gray-700">Berikan sesuai takaran yang dianjurkan</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0">2</span>
                                    <span class="text-gray-700">Sediakan air bersih yang cukup</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0">3</span>
                                    <span class="text-gray-700">Simpan di tempat yang sejuk dan kering</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div id="composition-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h5 class="font-medium text-gray-800 mb-2 text-base">Komposisi:</h5>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                <?php
                                if (!empty($product['komposisi'])) {
                                    echo nl2br(htmlspecialchars($product['komposisi']));
                                } else {
                                    echo "Informasi komposisi tidak tersedia";
                                }
                                ?>
                            </p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-800 mb-2 text-base">Konstituen Analitis:</h5>
                            <div class="text-gray-600 space-y-1 text-sm">
                                <?php
                                if (!empty($product['konstituen_analitis'])) {
                                    $konstituen = explode("\n", $product['konstituen_analitis']);
                                    foreach ($konstituen as $item) {
                                        if (!empty(trim($item))) {
                                            echo "<p>• " . htmlspecialchars(trim($item)) . "</p>";
                                        }
                                    }
                                } else {
                                    echo "<p>Informasi konstituen analitis tidak tersedia</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-12">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Produk Terkait</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <?php while ($related = mysqli_fetch_assoc($related_result)) {
                    $related_image_path = $related['foto_utama'];
                    if (!empty($related_image_path)) {
                        if (strpos($related_image_path, 'uploads/') === 0 || strpos($related_image_path, 'assets/') === 0) {
                            $related_final_path = '../../' . $related_image_path;
                        } else {
                            $possible_paths = [
                                '../../uploads/produk/' . $related['target_hewan'] . '/' . $related_image_path,
                                '../../uploads/produk/' . $related_image_path,
                                '../../assets/img/produk/' . $related_image_path,
                                $related_image_path
                            ];
                            $related_final_path = $possible_paths[0];
                        }
                    } else {
                        $related_final_path = '../../aset/default-product.png';
                    }
                    ?>
                    <div class="bg-white border border-orange-500 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg h-full flex flex-col"
                        onclick="window.location.href='detail_produk.php?id=<?php echo $related['id_produk']; ?>'">
                        <div class="relative w-full aspect-square bg-gray-100">
                            <img src="<?php echo $related_final_path; ?>"
                                class="absolute inset-0 w-full h-full object-contain p-2"
                                alt="<?php echo htmlspecialchars($related['nama_produk']); ?>"
                                onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 leading-tight pr-2 flex-1"
                                    title="<?php echo htmlspecialchars($related['nama_produk']); ?>">
                                    <?php echo htmlspecialchars($related['nama_produk']); ?>
                                </h3>
                                <button class="w-6 h-6 flex items-center justify-center flex-shrink-0"
                                    onclick="event.stopPropagation(); addToFavorites(<?php echo $related['id_produk']; ?>)">
                                    <i
                                        class="far fa-heart text-orange-400 text-lg hover:text-orange-500 transition-colors"></i>
                                </button>
                            </div>
                            <p class="text-orange-600 font-medium text-sm mb-1">
                                Rp<?php echo number_format($related['harga'], 0, ',', '.'); ?>
                            </p>
                            <p class="text-gray-400 text-xs mt-auto">Kategori: <?php echo $related['target_hewan']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <!-- Add popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

    <script>
        // Function to show popup notification
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

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        function increaseQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.getAttribute('max'));
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        }

        function addToCart(productId) {
            const quantity = document.getElementById('quantity').value;
            const maxStock = parseInt(document.getElementById('quantity').getAttribute('max'));

            if (parseInt(quantity) > maxStock) {
                showPopup('Jumlah melebihi stok yang tersedia', 'error');
                return;
            }

            if (parseInt(quantity) < 1) {
                showPopup('Jumlah minimal 1', 'error');
                return;
            }

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in header
                        const cartCountElement = document.querySelector('.fa-cart-shopping + span');
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cart_count;
                        }

                        // Show success message
                        showPopup(data.message);
                    } else {
                        // Show error message
                        showPopup(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
                });
        }

        function buyNow(productId) {
            const quantity = document.getElementById('quantity').value;
            const maxStock = parseInt(document.getElementById('quantity').getAttribute('max'));

            if (parseInt(quantity) > maxStock) {
                showPopup('Jumlah melebihi stok yang tersedia', 'error');
                return;
            }

            if (parseInt(quantity) < 1) {
                showPopup('Jumlah minimal 1', 'error');
                return;
            }

            // Redirect to checkout page in profilpelanggan/keranjang folder with product already added
            window.location.href = `../../profilpelanggan/keranjang/checkout_keranjang.php?product_id=${productId}&quantity=${quantity}`;
        }

        function addToFavorites(productId) {
            fetch('add_to_favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update heart icon
                        const heartIcon = document.querySelector(`button[onclick="addToFavorites(${productId})"] i`);
                        if (heartIcon) {
                            if (data.is_favorite) {
                                heartIcon.classList.remove('far');
                                heartIcon.classList.add('fas');
                                heartIcon.classList.add('text-red-500');
                            } else {
                                heartIcon.classList.remove('fas');
                                heartIcon.classList.remove('text-red-500');
                                heartIcon.classList.add('far');
                            }
                        }

                        // Update favorites count in header
                        const favoritesCountElement = document.querySelector('.fa-regular.fa-heart + span');
                        if (favoritesCountElement) {
                            favoritesCountElement.textContent = data.favorites_count;
                        }

                        // Show success message
                        showPopup(data.message);
                    } else {
                        // Show error message
                        showPopup(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Terjadi kesalahan saat memproses favorit', 'error');
                });
        }

        function showTab(tabId, button) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Remove active styles from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-orange-500', 'text-orange-500');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(tabId + '-tab').classList.remove('hidden');

            // Add active styles to clicked button
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('border-orange-500', 'text-orange-500');
        }
    </script>
</body>

</html>