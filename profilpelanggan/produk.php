<?php
/**
 * Contoh penggunaan sistem favorit di halaman produk
 * File: pages/produk/index.php
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once '../../includes/db.php';

// Include favorite functions and product card component
require_once '../profil/favorit_functions.php';
require_once '../profil/product_card.php';

// Get user ID if logged in
$pelanggan_id = isset($_SESSION['id_pelanggan']) ? $_SESSION['id_pelanggan'] : null;

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// Get products with favorite status
$products = getProductsForDisplay($pdo, $pelanggan_id, $category, null, $search);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ling-Ling Pet Shop - Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Produk Kami</h1>

            <!-- Filter and Search -->
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <!-- Search -->
                <div class="flex-1">
                    <form method="GET" class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                            placeholder="Cari produk..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <?php if ($category): ?>
                            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Category Filter -->
                <div class="md:w-48">
                    <select name="category" onchange="filterByCategory(this.value)"
                        class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        <option value="kucing" <?= $category === 'kucing' ? 'selected' : '' ?>>Kucing</option>
                        <option value="anjing" <?= $category === 'anjing' ? 'selected' : '' ?>>Anjing</option>
                        <option value="hamster" <?= $category === 'hamster' ? 'selected' : '' ?>>Hamster</option>
                        <option value="kelinci" <?= $category === 'kelinci' ? 'selected' : '' ?>>Kelinci</option>
                    </select>
                </div>
            </div>

            <!-- Results Info -->
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>Menampilkan <?= count($products) ?> produk</span>
                <?php if ($pelanggan_id): ?>
                    <a href="../profil/favorit.php" class="text-orange-600 hover:text-orange-700">
                        <i class="fas fa-heart mr-1"></i>
                        Lihat Favorit (<?= getFavoriteCount($pdo, $pelanggan_id) ?>)
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <?php
            $empty_title = $search ? "Tidak ditemukan produk untuk '$search'" : "Belum ada produk";
            $empty_message = $search ? "Coba kata kunci lain atau jelajahi kategori yang berbeda." : "Produk akan segera tersedia.";
            renderEmptyState($empty_title, $empty_message, !$search);
            ?>
        <?php else: ?>
            <?php renderProductGrid($products, $pelanggan_id, true); ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <!-- Include favorite JavaScript -->
    <script src="../profil/favorite.js"></script>

    <!-- Additional Scripts -->
    <script>
        function filterByCategory(category) {
            const url = new URL(window.location);
            if (category) {
                url.searchParams.set('category', category);
            } else {
                url.searchParams.delete('category');
            }
            window.location.href = url.toString();
        }

        function addToCart(productId) {
            // Implement add to cart functionality
            fetch('../keranjang/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.favoriteManager?.showNotification('Produk ditambahkan ke keranjang', 'success');
                    } else {
                        window.favoriteManager?.showNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.favoriteManager?.showNotification('Terjadi kesalahan sistem', 'error');
                });
        }

        function viewProduct(productId) {
            window.location.href = `detail.php?id=${productId}`;
        }

        // Auto-submit search form with debounce
        let searchTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (e.target.value.length >= 3 || e.target.value.length === 0) {
                    e.target.form.submit();
                }
            }, 500);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>