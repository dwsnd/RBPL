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

// Ambil data pelanggan
$pelanggan_id = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data produk favorit
$favorit_query = "SELECT p.*, f.created_at as favorit_date 
                  FROM produk p 
                  INNER JOIN favorit f ON p.id_produk = f.id_produk 
                  WHERE f.id_pelanggan = ? 
                  ORDER BY f.created_at DESC";
$favorit_stmt = $pdo->prepare($favorit_query);
$favorit_stmt->execute([$pelanggan_id]);
$data_favorit = $favorit_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ling-Ling Pet Shop - Favorit Saya</title>
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
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Favorit Saya</h2>
                    <span class="text-sm text-gray-500"><?= count($data_favorit) ?> produk</span>
                </div>

                <!-- Favorit Products Section -->
                <div class="mt-8">
                    <?php if (empty($data_favorit)): ?>
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="mb-6">
                                <img src="../../assets/images/empty-favorit.png" alt="Empty Favorit"
                                    class="mx-auto w-48 h-48 object-contain opacity-60">
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-3">Belum ada produk favorit</h3>
                            <p class="text-gray-500 mb-6">Yuk, tambahkan produk kesukaan Anda ke favorit untuk memudahkan
                                pencarian!</p>
                            <a href="../produk/"
                                class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Jelajahi Produk
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Favorit Products Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php foreach ($data_favorit as $produk): ?>
                                <div
                                    class="product-card bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <!-- Product Image -->
                                    <div class="relative">
                                        <img src="../../uploads/produk/<?= htmlspecialchars($produk['image'] ?: 'default.jpg') ?>"
                                            alt="<?= htmlspecialchars($produk['name']) ?>" class="w-full h-48 object-cover">

                                        <!-- Favorite Button -->
                                        <button onclick="toggleFavorite(<?= $produk['id'] ?>)"
                                            class="heart-btn favorited absolute top-3 right-3 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center hover:bg-gray-50">
                                            <i class="fas fa-heart text-lg"></i>
                                        </button>

                                        <!-- Category Badge -->
                                        <?php if ($produk['category']): ?>
                                            <span
                                                class="absolute top-3 left-3 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                <?= ucfirst(htmlspecialchars($produk['category'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2"
                                            title="<?= htmlspecialchars($produk['name']) ?>">
                                            <?= htmlspecialchars($produk['name']) ?>
                                        </h3>

                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-lg font-bold text-orange-600">
                                                Rp<?= number_format($produk['price'], 0, ',', '.') ?>
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                Stok: <?= $produk['stock'] ?>
                                            </span>
                                        </div>

                                        <?php if ($produk['description']): ?>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                <?= htmlspecialchars(substr($produk['description'], 0, 100)) ?>...
                                            </p>
                                        <?php endif; ?>

                                        <div class="flex gap-2">
                                            <button
                                                class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 transition-colors font-medium">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Tambah ke Keranjang
                                            </button>
                                            <button
                                                class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-eye text-gray-600"></i>
                                            </button>
                                        </div>

                                        <div class="mt-2 text-xs text-gray-400">
                                            Ditambahkan: <?= date('d M Y', strtotime($produk['favorit_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
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

        // Function to toggle favorite
        function toggleFavorite(productId) {
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