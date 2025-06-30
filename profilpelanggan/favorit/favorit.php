<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location:../auth/login.php");
    exit();
}

// Database connection
require_once '../../includes/db.php';

// ================= HELPER FUNCTIONS =================
function isFavorited($pdo, $pelanggan_id, $product_id)
{
    try {
        $query = "SELECT id_favorit FROM favorit WHERE id_pelanggan = ? AND id_produk = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pelanggan_id, $product_id]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Error checking favorite status: " . $e->getMessage());
        return false;
    }
}

function getFavoriteProducts($pdo, $pelanggan_id, $limit = null)
{
    try {
        $query = "SELECT p.*, f.created_at as favorit_date 
                  FROM produk p 
                  INNER JOIN favorit f ON p.id_produk = f.id_produk 
                  WHERE f.id_pelanggan = ? 
                  ORDER BY f.created_at DESC";
        if ($limit) {
            $query .= " LIMIT " . (int) $limit;
        }
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pelanggan_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting favorite products: " . $e->getMessage());
        return [];
    }
}
// ================= KOMPONEN TAMPILAN =================
function renderProductCard($produk, $pelanggan_id = null, $show_favorite = true)
{
    global $pdo;
    $is_logged_in = isset($_SESSION['id_pelanggan']) && $pelanggan_id;
    $is_favorited = false;
    if ($is_logged_in && $show_favorite) {
        $is_favorited = isFavorited($pdo, $pelanggan_id, $produk['id_produk']);
    }
    $heart_class = $is_favorited ? 'favorited text-red-500' : 'not-favorited text-gray-300';

    // Path gambar berdasarkan data database yang sudah berisi path lengkap
    $image_path = '';
    if (!empty($produk['foto_utama'])) {
        // Clean up the path
        $image_path = trim($produk['foto_utama']);
        $image_path = str_replace('\\', '/', $image_path);

        // If the path doesn't start with uploads/, add it
        if (!str_starts_with($image_path, 'uploads/')) {
            $image_path = 'uploads/produk/' . $image_path;
        }

        // Add ../../ to make it relative to profilpelanggan folder
        $image_path = '../../' . $image_path;
    }

    ?>
    <div class="product-card bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 h-full flex flex-col"
        data-product-id="<?= $produk['id_produk'] ?>">
        <!-- Product Image Container with Padding -->
        <div class="p-3 bg-gray-50">
            <div class="relative flex-shrink-0 rounded-lg overflow-hidden">
                <?php if (!empty($produk['foto_utama'])): ?>
                    <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>"
                        class="w-full h-56 object-contain bg-white"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        data-original="<?= htmlspecialchars($produk['foto_utama'] ?? '') ?>"
                        data-target="<?= htmlspecialchars($produk['target_hewan'] ?? '') ?>">
                    <!-- Fallback Icon Paw -->
                    <div class="w-full h-56 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center rounded-lg"
                        style="display: none;">
                        <div class="text-center">
                            <i class="fas fa-paw text-6xl text-orange-300 mb-3 animate-pulse"></i>
                            <p class="text-sm text-gray-500 font-medium">Gambar tidak tersedia</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Default Icon Paw -->
                    <div
                        class="w-full h-56 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-paw text-6xl text-orange-300 mb-3 animate-pulse"></i>
                            <p class="text-sm text-gray-500 font-medium">Gambar tidak tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Favorite Button -->
                <?php if ($is_logged_in && $show_favorite): ?>
                    <button
                        class="heart-btn <?= $heart_class ?> absolute top-3 right-3 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center hover:bg-gray-50 transition-all duration-200"
                        data-product-id="<?= $produk['id_produk'] ?>" onclick="toggleFavorite(<?= $produk['id_produk'] ?>)"
                        title="<?= $is_favorited ? 'Hapus dari favorit' : 'Tambah ke favorit' ?>">
                        <i class="fas fa-heart text-lg"></i>
                    </button>
                <?php endif; ?>

                <!-- Category Badge -->
                <?php if (!empty($produk['target_hewan'])): ?>
                    <span class="absolute top-3 left-3 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                        <?= ucfirst(htmlspecialchars($produk['target_hewan'])) ?>
                    </span>
                <?php endif; ?>

                <!-- Stock Badge -->
                <?php if ($produk['stok'] <= 5 && $produk['stok'] > 0): ?>
                    <span
                        class="absolute bottom-3 left-3 px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                        Stok Terbatas
                    </span>
                <?php elseif ($produk['stok'] <= 0): ?>
                    <span class="absolute bottom-3 left-3 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                        Habis
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Product Info -->
        <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 min-h-[2.5rem] text-sm"
                title="<?= htmlspecialchars($produk['nama_produk']) ?>">
                <?= htmlspecialchars($produk['nama_produk']) ?>
            </h3>

            <div class="flex items-center justify-between mb-2">
                <span class="text-lg font-bold text-orange-600">
                    Rp<?= number_format($produk['harga'], 0, ',', '.') ?>
                </span>
                <span class="text-xs text-gray-500">
                    Stok: <span
                        class="font-medium <?= $produk['stok'] <= 5 ? 'text-orange-600' : 'text-green-600' ?>"><?= $produk['stok'] ?></span>
                </span>
            </div>

            <?php if (!empty($produk['deskripsi'])): ?>
                <p class="text-xs text-gray-600 mb-3 line-clamp-2 flex-1">
                    <?= htmlspecialchars(substr($produk['deskripsi'], 0, 80)) ?>
                    <?= strlen($produk['deskripsi']) > 80 ? '...' : '' ?>
                </p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-auto">
                <?php if ($produk['stok'] > 0): ?>
                    <button
                        class="flex-1 bg-orange-500 text-white py-2 px-3 rounded-md hover:bg-orange-600 transition-colors font-medium text-xs"
                        onclick="addToCart(<?= $produk['id_produk'] ?>)">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Keranjang
                    </button>
                <?php else: ?>
                    <button class="flex-1 bg-gray-400 text-white py-2 px-3 rounded-md cursor-not-allowed font-medium text-xs"
                        disabled>
                        <i class="fas fa-times mr-1"></i>
                        Habis
                    </button>
                <?php endif; ?>
                <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    onclick="viewProduct(<?= $produk['id_produk'] ?>)" title="Lihat Detail">
                    <i class="fas fa-eye text-gray-600 text-sm"></i>
                </button>
            </div>
        </div>
    </div>
    <?php
}
function renderProductGrid($products, $pelanggan_id = null, $show_favorite = true, $grid_classes = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5')
{
    if (empty($products)) {
        renderEmptyState();
        return;
    }
    ?>
    <div class="grid <?= $grid_classes ?> gap-4 md:gap-6">
        <?php foreach ($products as $produk): ?>
            <?php renderProductCard($produk, $pelanggan_id, $show_favorite); ?>
        <?php endforeach; ?>
    </div>
    <?php
}
function renderEmptyState($title = 'Belum ada produk favorit', $message = 'Yuk, tambahkan produk kesukaan Anda ke favorit untuk memudahkan pencarian!', $show_button = true)
{
    ?>
    <div class="text-center py-16">
        <div class="mb-6">
            <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 mb-3"><?= htmlspecialchars($title) ?></h3>
        <p class="text-gray-500 mb-6"><?= htmlspecialchars($message) ?></p>
        <?php if ($show_button): ?>
            <a href="../produk/"
                class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Jelajahi Produk
            </a>
        <?php endif; ?>
    </div>
    <?php
}
// ================= END HELPER & KOMPONEN =================

// Ambil data pelanggan
$pelanggan_id = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data produk favorit
$data_favorit = getFavoriteProducts($pdo, $pelanggan_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            height: fit-content;
            max-height: 100vh;
            overflow-y: auto;
            border-radius: 0.5rem;
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

        .product-card {
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        .heart-btn {
            transition: all 0.3s ease;
        }

        .heart-btn:hover {
            transform: scale(1.1);
        }

        .heart-btn.favorited {
            color: #ef4444;
        }

        .heart-btn.not-favorited {
            color: #d1d5db;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Product card improvements */
        .product-card {
            min-height: 400px;
        }

        .product-card img {
            transition: transform 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .product-card {
                min-height: 350px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require_once '../../includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-4 md:px-6 pb-4 max-w-7xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-4 md:p-6 border border-grey-100">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Favorit Saya</h2>
                    <span class="text-sm text-gray-500"><?= count($data_favorit) ?> produk</span>
                </div>

                <!-- Favorit Products Section -->
                <div class="mt-6">
                    <?php
                    if (empty($data_favorit)) {
                        renderEmptyState();
                    } else {
                        renderProductGrid($data_favorit, $pelanggan_id, true);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once '../../includes/footer.php'; ?>

    <!-- Add popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

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
                    class="px-6 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors text-sm">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

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

        // Custom confirmation modal function
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

        // Function to add to cart
        function addToCart(productId) {
            fetch('../dashboard/shop/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPopup('Produk berhasil ditambahkan ke keranjang', 'success');
                    } else {
                        showPopup(data.message || 'Gagal menambahkan ke keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Terjadi kesalahan sistem', 'error');
                });
        }

        // Function to view product detail
        function viewProduct(productId) {
            window.location.href = `../dashboard/shop/detail_produk.php?id=${productId}`;
        }

        // Function to toggle favorite
        function toggleFavorite(productId) {
            showCustomConfirm('Apakah Anda yakin ingin menghapus item ini dari favorit?', (result) => {
                if (result) {
                    fetch('toggle_favorit.php', {
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
                                // Find the heart button and update its state
                                const heartBtn = document.querySelector(`button[onclick="toggleFavorite(${productId})"] i`);
                                const productCard = heartBtn.closest('.product-card');

                                if (data.action === 'removed') {
                                    // Remove the product card with animation
                                    productCard.style.transition = 'all 0.3s ease';
                                    productCard.style.transform = 'scale(0.8)';
                                    productCard.style.opacity = '0';

                                    setTimeout(() => {
                                        productCard.remove();

                                        // Update product count
                                        const countElement = document.querySelector('.text-sm.text-gray-500');
                                        const currentCount = parseInt(countElement.textContent.split(' ')[0]);
                                        countElement.textContent = `${currentCount - 1} produk`;

                                        // Check if no products left
                                        const remainingProducts = document.querySelectorAll('.product-card');
                                        if (remainingProducts.length === 0) {
                                            location.reload(); // Reload to show empty state
                                        }
                                    }, 300);

                                    showPopup('Produk dihapus dari favorit', 'error');
                                }
                            } else {
                                showPopup(data.message || 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showPopup('Terjadi kesalahan sistem', 'error');
                        });
                }
            });
        }

        // Check for notifications on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Check for URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const message = urlParams.get('message');

            if (success === 'true' && message) {
                showPopup(decodeURIComponent(message), 'success');

                // Clean URL parameters
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>