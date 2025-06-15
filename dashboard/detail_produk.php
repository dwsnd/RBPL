<?php
require_once '../src/lib/database.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$product_id) {
    header('Location: shop.php');
    exit();
}

// Fetch product details
$query = "SELECT * FROM produk WHERE id_produk = $product_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: shop.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Fetch related products (same category)
$related_query = "SELECT * FROM produk WHERE category = '{$product['category']}' AND id_produk != $product_id LIMIT 4";
$related_result = mysqli_query($conn, $related_query);
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

        .product-image {
            transition: transform 0.3s ease;
            max-height: 300px;
            width: auto;
            margin: 0 auto;
        }

        .product-image:hover {
            transform: scale(1.02);
        }

        .btn-orange {
            background-color: #f97316;
            border: none;
            color: white;
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-orange:hover {
            background-color: #ea580c;
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-orange {
            border: 1px solid #f97316;
            color: #f97316;
            background: white;
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-outline-orange:hover {
            background-color: #f97316;
            color: white;
        }

        .quantity-btn {
            border: 1px solid #d1d5db;
            background: white;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background-color: #f3f4f6;
        }

        .quantity-input {
            border: 1px solid #d1d5db;
            width: 40px;
            height: 28px;
            text-align: center;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            font-size: 0.9rem;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: #6b7280;
        }

        .product-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            max-width: 220px;
            margin: 0 auto;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #f97316;
        }

        .heart-btn {
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .heart-btn:hover {
            transform: scale(1.05);
        }

        .tabs-container {
            border-bottom: 1px solid #e5e7eb;
        }

        .tab-button {
            padding: 6px 12px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-weight: 500;
            font-size: 0.9rem;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: #f97316;
            border-bottom-color: #f97316;
        }

        .tab-button:hover {
            color: #f97316;
        }

        .product-info-section {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .product-title {
            font-size: 1.35rem;
            line-height: 1.4;
            font-weight: 600;
        }

        .product-price {
            font-size: 1.35rem;
            font-weight: 600;
        }

        .related-products-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .related-product-name {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .related-product-price {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .product-info-section h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        .product-info-section .text-sm {
            font-size: 0.9rem;
        }

        .product-info-section .text-gray-600 {
            font-size: 0.9rem;
        }

        .product-info-section .font-medium {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .tab-content {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .tab-content h4 {
            font-size: 1rem;
            font-weight: 600;
        }

        .tab-content ul li {
            font-size: 0.9rem;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 mb-4">
                <img src="<?php echo $product['gambar']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-image">
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <h1 class="product-title mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="product-price text-orange-500 mb-4">Rp
                    <?php echo number_format($product['price'], 0, ',', '.'); ?>
                </p>

                <div class="product-info-section mb-4">
                    <h3 class="mb-2">Deskripsi</h3>
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="product-info-section mb-4">
                    <h3 class="mb-2">Stok</h3>
                    <p class="text-gray-600"><?php echo $product['stok']; ?> unit tersedia</p>
                </div>

                <div class="product-info-section mb-4">
                    <h3 class="mb-2">Jumlah</h3>
                    <div class="d-flex align-items-center">
                        <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" class="quantity-input mx-2" value="1" min="1"
                            max="<?php echo $product['stok']; ?>">
                        <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-orange flex-grow-1"
                        onclick="addToCart(<?php echo $product['id_produk']; ?>)">
                        <i class="fas fa-shopping-cart me-2"></i>Tambahkan ke Keranjang
                    </button>
                    <button class="btn btn-outline-orange heart-btn"
                        onclick="addToFavorites(<?php echo $product['id_produk']; ?>)">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-5">
            <h2 class="related-products-title mb-4">Produk Terkait</h2>
            <div class="row">
                <?php while ($related = mysqli_fetch_assoc($related_result)) { ?>
                    <div class="col-md-3 mb-4">
                        <a href="detail_produk.php?id=<?php echo $related['id_produk']; ?>" class="text-decoration-none">
                            <div class="product-card">
                                <img src="<?php echo $related['gambar']; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($related['name']); ?>"
                                    style="height: 150px; object-fit: contain; padding: 1rem;">
                                <div class="p-3">
                                    <h6 class="related-product-name text-gray-800 mb-2">
                                        <?php echo htmlspecialchars($related['name']); ?>
                                    </h6>
                                    <p class="related-product-price text-orange-500 mb-0">Rp
                                        <?php echo number_format($related['price'], 0, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            // ... existing add to cart function ...
        }

        function addToFavorites(productId) {
            // ... existing add to favorites function ...
        }
    </script>
</body>

</html>