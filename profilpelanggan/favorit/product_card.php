<?php
/**
 * Product Card Component dengan Tombol Favorit
 * Include file ini di halaman yang menampilkan produk
 */

// Pastikan ada database connection dan session
if (!isset($pdo)) {
    require_once '../../includes/db.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include favorite functions
require_once 'favorit_functions.php';

/**
 * Render product card with favorite functionality
 */
function renderProductCard($produk, $pelanggan_id = null, $show_favorite = true)
{
    global $pdo;

    // Check if user is logged in untuk favorite functionality
    $is_logged_in = isset($_SESSION['id_pelanggan']) && $pelanggan_id;
    $is_favorited = false;

    if ($is_logged_in && $show_favorite) {
        $is_favorited = isFavorited($pdo, $pelanggan_id, $produk['id']);
    }

    $heart_class = $is_favorited ? 'favorited text-red-500' : 'not-favorited text-gray-300';
    ?>

    <div class="product-card bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
        data-product-id="<?= $produk['id'] ?>">
        <!-- Product Image -->
        <div class="relative">
            <img src="../../uploads/produk/<?= htmlspecialchars($produk['image'] ?: 'default.jpg') ?>"
                alt="<?= htmlspecialchars($produk['name']) ?>" class="w-full h-48 object-cover">

            <?php if ($is_logged_in && $show_favorite): ?>
                <!-- Favorite Button -->
                <button
                    class="heart-btn <?= $heart_class ?> absolute top-3 right-3 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center hover:bg-gray-50 transition-all duration-200"
                    data-product-id="<?= $produk['id'] ?>"
                    title="<?= $is_favorited ? 'Hapus dari favorit' : 'Tambah ke favorit' ?>">
                    <i class="fas fa-heart text-lg"></i>
                </button>
            <?php endif; ?>

            <!-- Category Badge -->
            <?php if (!empty($produk['category'])): ?>
                <span class="absolute top-3 left-3 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                    <?= ucfirst(htmlspecialchars($produk['category'])) ?>
                </span>
            <?php endif; ?>

            <!-- Stock Badge -->
            <?php if ($produk['stock'] <= 5 && $produk['stock'] > 0): ?>
                <span class="absolute bottom-3 left-3 px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                    Stok Terbatas
                </span>
            <?php elseif ($produk['stock'] <= 0): ?>
                <span class="absolute bottom-3 left-3 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                    Habis
                </span>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="p-4">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 min-h-[3rem]"
                title="<?= htmlspecialchars($produk['name']) ?>">
                <?= htmlspecialchars($produk['name']) ?>
            </h3>

            <div class="flex items-center justify-between mb-3">
                <span class="text-lg font-bold text-orange-600">
                    Rp<?= number_format($produk['price'], 0, ',', '.') ?>
                </span>
                <span class="text-sm text-gray-500">
                    Stok: <span
                        class="font-medium <?= $produk['stock'] <= 5 ? 'text-orange-600' : 'text-green-600' ?>"><?= $produk['stock'] ?></span>
                </span>
            </div>

            <?php if (!empty($produk['description'])): ?>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    <?= htmlspecialchars(substr($produk['description'], 0, 100)) ?>
                    <?= strlen($produk['description']) > 100 ? '...' : '' ?>
                </p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <?php if ($produk['stock'] > 0): ?>
                    <button
                        class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 transition-colors font-medium text-sm"
                        onclick="addToCart(<?= $produk['id'] ?>)">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Tambah ke Keranjang
                    </button>
                <?php else: ?>
                    <button class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed font-medium text-sm"
                        disabled>
                        <i class="fas fa-times mr-1"></i>
                        Stok Habis
                    </button>
                <?php endif; ?>

                <button class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    onclick="viewProduct(<?= $produk['id'] ?>)" title="Lihat Detail">
                    <i class="fas fa-eye text-gray-600"></i>
                </button>
            </div>

            <!-- Additional Info -->
            <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
                <span>Ditambahkan: <?= date('d M Y', strtotime($produk['created_at'])) ?></span>
                <?php if (isset($produk['favorit_date'])): ?>
                    <span class="text-orange-500">★ Favorit: <?= date('d M Y', strtotime($produk['favorit_date'])) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
}

/**
 * Render grid of products
 */
function renderProductGrid($products, $pelanggan_id = null, $show_favorite = true, $grid_classes = 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4')
{
    if (empty($products)) {
        renderEmptyState();
        return;
    }
    ?>

    <div class="grid <?= $grid_classes ?> gap-6">
        <?php foreach ($products as $produk): ?>
            <?php renderProductCard($produk, $pelanggan_id, $show_favorite); ?>
        <?php endforeach; ?>
    </div>

    <?php
}

/**
 * Render empty state
 */
function renderEmptyState($title = 'Belum ada produk', $message = 'Produk akan segera tersedia.', $show_button = true)
{
    ?>

    <div class="text-center py-16">
        <div class="mb-6">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
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

/**
 * Get products with favorite status for logged in user
 */
function getProductsForDisplay($pdo, $pelanggan_id = null, $category = null, $limit = null, $search = null)
{
    try {
        $query = "SELECT p.*";
        $params = [];

        if ($pelanggan_id) {
            $query .= ", (SELECT COUNT(*) FROM favorit f WHERE f.id_pelanggan = ? AND f.id_produk = p.id) as is_favorited";
            $params[] = $pelanggan_id;
        }

        $query .= " FROM produk p WHERE 1=1";

        if ($category) {
            $query .= " AND p.category = ?";
            $params[] = $category;
        }

        if ($search) {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $search_term = "%$search%";
            $params[] = $search_term;
            $params[] = $search_term;
        }

        $query .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $query .= " LIMIT " . (int) $limit;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error in getProductsForDisplay: " . $e->getMessage());
        return [];
    }
}
?>