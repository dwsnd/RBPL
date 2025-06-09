<?php
/**
 * Helper functions untuk sistem favorit
 */

/**
 * Check if product is in user's favorites
 */
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

/**
 * Get user's favorite products
 */
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

/**
 * Get favorite count for user
 */
function getFavoriteCount($pdo, $pelanggan_id)
{
    try {
        $query = "SELECT COUNT(*) as count FROM favorit WHERE id_pelanggan = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pelanggan_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error getting favorite count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Add product to favorites
 */
function addToFavorites($pdo, $pelanggan_id, $product_id)
{
    try {
        // Check if already exists
        if (isFavorited($pdo, $pelanggan_id, $product_id)) {
            return ['success' => false, 'message' => 'Produk sudah ada di favorit'];
        }

        // Check if product exists
        $check_product = "SELECT id_produk FROM produk WHERE id_produk = ?";
        $stmt = $pdo->prepare($check_product);
        $stmt->execute([$product_id]);

        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }

        // Add to favorites
        $query = "INSERT INTO favorit (id_pelanggan, id_produk) VALUES (?, ?)";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$pelanggan_id, $product_id]);

        if ($result) {
            return ['success' => true, 'message' => 'Produk ditambahkan ke favorit'];
        } else {
            return ['success' => false, 'message' => 'Gagal menambahkan ke favorit'];
        }
    } catch (PDOException $e) {
        error_log("Error adding to favorites: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan database'];
    }
}

/**
 * Remove product from favorites
 */
function removeFromFavorites($pdo, $pelanggan_id, $product_id)
{
    try {
        $query = "DELETE FROM favorit WHERE id_pelanggan = ? AND id_produk = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$pelanggan_id, $product_id]);

        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Produk dihapus dari favorit'];
        } else {
            return ['success' => false, 'message' => 'Produk tidak ditemukan di favorit'];
        }
    } catch (PDOException $e) {
        error_log("Error removing from favorites: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan database'];
    }
}

/**
 * Generate heart icon HTML for product
 */
function getFavoriteHeartIcon($pdo, $pelanggan_id, $product_id, $additional_classes = '')
{
    $is_favorited = isFavorited($pdo, $pelanggan_id, $product_id);
    $heart_class = $is_favorited ? 'favorited text-red-500' : 'not-favorited text-gray-300';

    return sprintf(
        '<button onclick="toggleFavorite(%d)" class="heart-btn %s %s p-2 rounded-full hover:bg-gray-100 transition-colors">
            <i class="fas fa-heart text-lg"></i>
        </button>',
        $product_id,
        $heart_class,
        $additional_classes
    );
}

/**
 * Get products with favorite status for user
 */
function getProductsWithFavoriteStatus($pdo, $pelanggan_id, $category = null, $limit = null)
{
    try {
        $query = "SELECT p.*, 
                         (SELECT COUNT(*) FROM favorit f WHERE f.id_pelanggan = ? AND f.id_produk = p.id_produk) as is_favorited
                  FROM produk p 
                  WHERE 1=1";
        $params = [$pelanggan_id];

        if ($category) {
            $query .= " AND p.category = ?";
            $params[] = $category;
        }

        $query .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $query .= " LIMIT " . (int) $limit;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting products with favorite status: " . $e->getMessage());
        return [];
    }
}
?>