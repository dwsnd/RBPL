<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: ../src/pages/auth/login.php");
    exit();
}

// Database connection
require_once '../src/lib/database.php';

// Handle direct product addition from Buy Now
if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = (int) $_GET['product_id'];
    $quantity = (int) $_GET['quantity'];
    $pelanggan_id = $_SESSION['id_pelanggan'];

    try {
        // Check if product exists and has enough stock
        $check_product = "SELECT stock FROM produk WHERE id_produk = ?";
        $stmt = $pdo->prepare($check_product);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['stock'] >= $quantity) {
            // Check if product already in cart
            $check_cart = "SELECT quantity FROM keranjang WHERE id_pelanggan = ? AND id_produk = ?";
            $stmt = $pdo->prepare($check_cart);
            $stmt->execute([$pelanggan_id, $product_id]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                // Update quantity if product already in cart
                $update_query = "UPDATE keranjang SET quantity = ? WHERE id_pelanggan = ? AND id_produk = ?";
                $stmt = $pdo->prepare($update_query);
                $stmt->execute([$quantity, $pelanggan_id, $product_id]);
            } else {
                // Add new item to cart
                $insert_query = "INSERT INTO keranjang (id_pelanggan, id_produk, quantity) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($insert_query);
                $stmt->execute([$pelanggan_id, $product_id, $quantity]);
            }

            // Update cart count in session
            $count_query = "SELECT COUNT(*) as count FROM keranjang WHERE id_pelanggan = ?";
            $stmt = $pdo->prepare($count_query);
            $stmt->execute([$pelanggan_id]);
            $_SESSION['cart_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        }
    } catch (PDOException $e) {
        error_log("Database error in keranjang.php: " . $e->getMessage());
    }
}

// Get cart items
$pelanggan_id = $_SESSION['id_pelanggan'];
$query = "SELECT k.*, p.name, p.price, p.image, p.stock 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Ling-Ling Pet Shop</title>
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

        .cart-item {
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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

        .btn-orange {
            background-color: #f97316;
            border: none;
            color: white;
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn-orange:hover {
            background-color: #ea580c;
            color: white;
            transform: translateY(-1px);
        }

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
    <?php require_once '../src/includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Keranjang Belanja</h1>

                <?php if (empty($cart_items)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">Keranjang belanja Anda kosong</p>
                            <a href="../dashboard/shop_pelanggan.php" class="btn-orange inline-block mt-4">
                                Belanja Sekarang
                            </a>
                        </div>
                <?php else: ?>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Cart Items -->
                            <div class="lg:col-span-2">
                                <?php foreach ($cart_items as $item): ?>
                                        <div class="cart-item bg-white rounded-lg p-4 mb-4 border border-gray-200">
                                            <div class="flex items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                    class="w-24 h-24 object-cover rounded-lg">
                                                <div class="ml-4 flex-grow">
                                                    <h3 class="font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($item['name']); ?>
                                                    </h3>
                                                    <p class="text-orange-600 font-medium mt-1">
                                                        Rp<?php echo number_format($item['price'], 0, ',', '.'); ?>
                                                    </p>
                                                    <div class="flex items-center mt-2">
                                                        <button type="button" class="quantity-btn rounded-l-md"
                                                            onclick="updateQuantity(<?php echo $item['id_produk']; ?>, 'decrease')">
                                                            <i class="fas fa-minus text-sm"></i>
                                                        </button>
                                                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1"
                                                            max="<?php echo $item['stock']; ?>" class="quantity-input"
                                                            onchange="updateQuantity(<?php echo $item['id_produk']; ?>, 'set', this.value)">
                                                        <button type="button" class="quantity-btn rounded-r-md"
                                                            onclick="updateQuantity(<?php echo $item['id_produk']; ?>, 'increase')">
                                                            <i class="fas fa-plus text-sm"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button type="button" class="text-gray-400 hover:text-red-500 ml-4"
                                                    onclick="removeFromCart(<?php echo $item['id_produk']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Order Summary -->
                            <div class="lg:col-span-1">
                                <div class="bg-white rounded-lg p-6 border border-gray-200">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                                    <div class="space-y-3">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Subtotal</span>
                                            <span>Rp<?php echo number_format($total, 0, ',', '.'); ?></span>
                                        </div>
                                        <div class="flex justify-between text-gray-600">
                                            <span>Pengiriman</span>
                                            <span>Gratis</span>
                                        </div>
                                        <div class="border-t pt-3 mt-3">
                                            <div class="flex justify-between font-semibold text-gray-900">
                                                <span>Total</span>
                                                <span>Rp<?php echo number_format($total, 0, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-orange w-full mt-6" onclick="checkout()">
                                        Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once '../src/includes/footer.php'; ?>

    <!-- Add popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

    <script>
        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popupNotification');
            popup.textContent = message;
            popup.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            popup.classList.add('show');

            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        function updateQuantity(productId, action, value = null) {
            const input = event.target.closest('.cart-item').querySelector('.quantity-input');
            let newQuantity = parseInt(input.value);

            if (action === 'increase') {
                newQuantity = Math.min(newQuantity + 1, parseInt(input.max));
            } else if (action === 'decrease') {
                newQuantity = Math.max(newQuantity - 1, 1);
            } else if (action === 'set' && value !== null) {
                newQuantity = Math.min(Math.max(parseInt(value), 1), parseInt(input.max));
            }

            if (newQuantity !== parseInt(input.value)) {
                input.value = newQuantity;
                updateCartItem(productId, newQuantity);
            }
        }

        function updateCartItem(productId, quantity) {
            fetch('../includes/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showPopup(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Terjadi kesalahan sistem', 'error');
                });
        }

        function removeFromCart(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                fetch('../includes/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            showPopup(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showPopup('Terjadi kesalahan sistem', 'error');
                    });
            }
        }

        function checkout() {
            window.location.href = '../checkout/checkout.php';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>