<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location:../../auth/login.php");
    exit();
}

// Database connection
require_once '../../includes/db.php';

$pelanggan_id = $_SESSION['id_pelanggan'];
$checkout_items = [];
$total = 0;
$shipping_cost = 10000;

// Handle direct product checkout (Buy Now)
if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $product_id = (int) $_GET['product_id'];
    $quantity = (int) $_GET['quantity'];

    try {
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['stok'] >= $quantity) {
            $checkout_items[] = [
                'id_produk' => $product['id_produk'],
                'nama_produk' => $product['nama_produk'],
                'harga' => $product['harga'],
                'quantity' => $quantity,
                'foto_utama' => $product['foto_utama'],
                'stok' => $product['stok'],
                'is_direct_checkout' => true
            ];
            $total = $product['harga'] * $quantity;
        }
    } catch (PDOException $e) {
        error_log("Database error in checkout_keranjang.php: " . $e->getMessage());
    }
}

// Handle selected cart items checkout
if (isset($_GET['selected_items'])) {
    $selected_ids = explode(',', $_GET['selected_items']);
    $selected_ids = array_filter($selected_ids, 'is_numeric');

    if (!empty($selected_ids)) {
        try {
            $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
            $query = "SELECT k.*, p.nama_produk, p.harga, p.foto_utama, p.stok 
                      FROM keranjang k 
                      JOIN produk p ON k.id_produk = p.id_produk 
                      WHERE k.id_keranjang IN ($placeholders) AND k.id_pelanggan = ?";

            $params = array_merge($selected_ids, [$pelanggan_id]);
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cart_items as $item) {
                $checkout_items[] = [
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'harga' => $item['harga'],
                    'quantity' => $item['quantity'],
                    'foto_utama' => $item['foto_utama'],
                    'stok' => $item['stok'],
                    'id_keranjang' => $item['id_keranjang'],
                    'is_direct_checkout' => false
                ];
                $total += $item['harga'] * $item['quantity'];
            }
        } catch (PDOException $e) {
            error_log("Database error in checkout_keranjang.php: " . $e->getMessage());
        }
    }
}

// If no items to checkout, redirect to cart
if (empty($checkout_items)) {
    header("Location: keranjang.php");
    exit();
}

// Get customer data
$query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

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
    <?php require '../../includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-xl font-bold text-gray-800">Checkout</h1>
                    <a href="keranjang.php" class="text-orange-500 hover:text-orange-600 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Keranjang
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Order Items -->
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 mb-4">Produk yang Dibeli</h2>
                        <div class="space-y-4">
                            <?php foreach ($checkout_items as $item): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <?php
                                        $image_path = $item['foto_utama'];
                                        if (!empty($image_path)) {
                                            // Try different possible paths for images
                                            $possible_paths = [
                                                '../../uploads/produk/' . $image_path,
                                                '../../assets/img/produk/' . $image_path,
                                                '../../aset/produk/kucing/' . $image_path,
                                                $image_path
                                            ];

                                            $final_path = '';
                                            foreach ($possible_paths as $path) {
                                                if (file_exists($path)) {
                                                    $final_path = $path;
                                                    break;
                                                }
                                            }

                                            if (empty($final_path)) {
                                                // If file doesn't exist, try to use the path as is
                                                if (strpos($image_path, 'uploads/') === 0) {
                                                    $final_path = '../../' . $image_path;
                                                } elseif (strpos($image_path, 'assets/') === 0) {
                                                    $final_path = '../../' . $image_path;
                                                } else {
                                                    $final_path = '../../aset/default-product.png';
                                                }
                                            }
                                        } else {
                                            $final_path = '../../aset/default-product.png';
                                        }
                                        ?>
                                        <img src="<?php echo $final_path; ?>"
                                            alt="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                                            class="w-16 h-16 object-cover rounded-lg"
                                            onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">
                                            <?php echo htmlspecialchars($item['nama_produk']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Jumlah: <?php echo $item['quantity']; ?> x Rp
                                            <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                        </p>
                                        <p class="text-orange-500 font-semibold">
                                            Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Order Summary & Payment -->
                    <div>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-base font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>

                            <div class="space-y-3 mb-6 text-base">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Item:</span>
                                    <span class="font-medium">
                                        <?php echo array_sum(array_column($checkout_items, 'quantity')); ?> item
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium">Rp
                                        <?php echo number_format($total, 0, ',', '.'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ongkos Kirim:</span>
                                    <span class="font-medium">Rp
                                        <?php echo number_format($shipping_cost, 0, ',', '.'); ?></span>
                                </div>
                                <hr class="my-3">
                                <div class="flex justify-between font-semibold">
                                    <span>Total:</span>
                                    <span class="text-orange-500">Rp
                                        <?php echo number_format($total + $shipping_cost, 0, ',', '.'); ?></span>
                                </div>
                            </div>

                            <!-- Customer Information -->
                            <div class="mb-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Pelanggan</h3>
                                <div class="space-y-2 text-sm">
                                    <p><strong>Nama:</strong>
                                        <?php echo htmlspecialchars($pelanggan['nama_lengkap']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($pelanggan['email']); ?></p>
                                    <p><strong>Telepon:</strong>
                                        <?php echo htmlspecialchars($pelanggan['nomor_telepon']); ?></p>
                                    <p><strong>Alamat:</strong>
                                        <?php echo htmlspecialchars($pelanggan['alamat'] ?? 'Alamat belum diisi'); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-3">Metode Pembayaran</h3>
                                <div class="space-y-2 text-base">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="transfer" class="mr-2" checked>
                                        <span>Transfer</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_method" value="cod" class="mr-2">
                                        <span>Cash on Delivery (COD)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <button class="w-full btn-orange py-3 text-lg font-semibold" onclick="placeOrder()">
                                Buat Pesanan
                            </button>
                        </div>
                    </div>
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

        function placeOrder() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Get selected items data
            const checkoutData = {
                items: <?php echo json_encode($checkout_items); ?>,
                payment_method: paymentMethod,
                total: <?php echo $total + $shipping_cost; ?>,
                shipping_cost: <?php echo $shipping_cost; ?>
            };

            // Send order to server
            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(checkoutData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPopup('Pesanan berhasil dibuat!', 'success');
                        setTimeout(() => {
                            window.location.href = '../pesanan/index_pesanan.php?success=true&type=pesanan';
                        }, 2000);
                    } else {
                        showPopup(data.message || 'Gagal membuat pesanan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Terjadi kesalahan sistem', 'error');
                });
        }

        // Check for URL parameters
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const type = urlParams.get('type');

            if (success === 'true') {
                const message = type === 'checkout' ?
                    'Checkout berhasil!' :
                    'Operasi berhasil dilakukan!';
                showPopup(message, 'success');

                // Clean URL parameters after showing notification
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // Check session notification via AJAX
            fetch('../check_notification.php')
                .then(response => response.json())
                .then(data => {
                    if (data.notification) {
                        showPopup(data.notification.message, data.notification.type);
                    }
                })
                .catch(error => {
                    console.log('No notification to show');
                });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>