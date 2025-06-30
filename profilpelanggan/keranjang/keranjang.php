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

// Get cart items
$pelanggan_id = $_SESSION['id_pelanggan'];
$query = "SELECT k.*, p.nama_produk, p.harga, p.foto_utama, p.stok 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['harga'] * $item['quantity'];
}

// Ambil data pelanggan
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
    <?php require '../../includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Keranjang Belanja</h2>

                <?php if (empty($cart_items)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg">Keranjang belanja Anda kosong</p>
                        <a href="../../dashboard/shop/shop_pelanggan.php" class="btn-orange inline-block mt-4">
                            Belanja Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="select-all"
                                        class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                                    <label for="select-all" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                                </div>
                                <button id="remove-selected"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium hidden">
                                    <i class="fas fa-trash mr-1"></i>Hapus Terpilih
                                </button>
                            </div>

                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center space-x-4">
                                        <!-- Checkbox -->
                                        <div class="flex-shrink-0">
                                            <input type="checkbox"
                                                class="product-checkbox w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                                                value="<?php echo $item['id_keranjang']; ?>"
                                                data-price="<?php echo $item['harga'] * $item['quantity']; ?>">
                                        </div>

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
                                                class="w-20 h-20 object-cover rounded-lg"
                                                onerror="this.onerror=null; this.src='../../aset/default-product.png';">
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            <h3 class="text-base font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($item['nama_produk']); ?>
                                            </h3>
                                            <p class="text-base text-orange-500 font-semibold">
                                                Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Stok tersedia: <?php echo $item['stok']; ?>
                                            </p>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-2">
                                            <button class="quantity-btn"
                                                onclick="updateQuantity(<?php echo $item['id_keranjang']; ?>, -1)" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                                -
                                            </button>
                                            <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>"
                                                min="1" max="<?php echo $item['stok']; ?>"
                                                onchange="updateQuantityDirect(<?php echo $item['id_keranjang']; ?>, this.value)">
                                            <button class="quantity-btn"
                                                onclick="updateQuantity(<?php echo $item['id_keranjang']; ?>, 1)" <?php echo $item['quantity'] >= $item['stok'] ? 'disabled' : ''; ?>>
                                                +
                                            </button>
                                        </div>

                                        <!-- Price and Remove -->
                                        <div class="text-right">
                                            <p class="text-lg font-semibold text-gray-900">
                                                Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?>
                                            </p>
                                            <button class="text-red-500 hover:text-red-700 text-sm mt-2"
                                                onclick="removeFromCart(<?php echo $item['id_keranjang']; ?>)">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-6 sticky top-4">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>

                                <div class="space-y-3 mb-6 text-base">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Item Terpilih:</span>
                                        <span class="font-medium" id="selected-count">0 item</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-medium" id="selected-subtotal">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ongkos Kirim:</span>
                                        <span class="font-medium" id="shipping-cost">Rp 10.000</span>
                                    </div>
                                    <hr class="my-3">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span>Total:</span>
                                        <span class="text-orange-500" id="selected-total">Rp 10.000</span>
                                    </div>
                                </div>

                                <button class="w-full btn-orange py-3 text-base font-semibold" id="checkout-btn"
                                    onclick="checkout()" disabled>
                                    Lanjutkan ke Pembayaran
                                </button>

                                <div class="mt-4 text-center">
                                    <a href="../dashboard/shop/shop_pelanggan.php"
                                        class="text-orange-500 hover:text-orange-600 text-sm">
                                        <i class="fas fa-arrow-left mr-1"></i>Lanjutkan Belanja
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

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

        function updateQuantity(cartId, change) {
            const quantityInput = document.querySelector(`input[data-cart-id="${cartId}"]`);
            const currentQuantity = parseInt(quantityInput.value);
            const newQuantity = Math.max(1, currentQuantity + change);

            if (newQuantity !== currentQuantity) {
                quantityInput.value = newQuantity;

                fetch('update_cart_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart_id: cartId,
                        quantity: newQuantity
                    })
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
                        showPopup('Terjadi kesalahan saat memperbarui keranjang', 'error');
                    });
            }
        }

        function removeFromCart(cartId) {
            showCustomConfirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?', (result) => {
                if (result) {
                    fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            cart_id: cartId
                        })
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
                            showPopup('Terjadi kesalahan saat menghapus item', 'error');
                        });
                }
            });
        }

        function updateQuantityDirect(cartId, quantity) {
            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    quantity: parseInt(quantity)
                })
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
                    showPopup('Terjadi kesalahan saat memperbarui keranjang', 'error');
                });
        }

        function checkout() {
            const selectedItems = getSelectedItems();
            if (selectedItems.length === 0) {
                showPopup('Pilih minimal satu produk untuk checkout', 'error');
                return;
            }

            // Redirect to checkout page with selected items
            const selectedIds = selectedItems.map(item => item.id).join(',');
            window.location.href = `checkout_keranjang.php?selected_items=${selectedIds}`;
        }

        function getSelectedItems() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const items = [];

            checkboxes.forEach(checkbox => {
                const cartItem = checkbox.closest('.cart-item');
                const quantityInput = cartItem.querySelector('.quantity-input');

                // Get the total price per item (price × quantity) from the right side price display
                // Using a more specific selector to target the total price element
                const totalPriceElement = cartItem.querySelector('.text-right p.text-lg.font-semibold.text-gray-900');

                // Extract total price from the total price element (remove "Rp " and commas)
                const totalPriceText = totalPriceElement.textContent.replace('Rp ', '').replace(/\./g, '');
                const totalPrice = parseInt(totalPriceText) || 0;
                const quantity = parseInt(quantityInput.value) || 0;

                items.push({
                    id: checkbox.value,
                    quantity: quantity,
                    totalPrice: totalPrice
                });
            });

            return items;
        }

        function updateOrderSummary() {
            const selectedItems = getSelectedItems();
            const selectedCount = selectedItems.reduce((sum, item) => sum + item.quantity, 0);
            const subtotal = selectedItems.reduce((sum, item) => sum + item.totalPrice, 0);
            const shippingCost = selectedCount > 0 ? 10000 : 0;
            const total = subtotal + shippingCost;

            document.getElementById('selected-count').textContent = `${selectedCount} item`;
            document.getElementById('selected-subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            document.getElementById('shipping-cost').textContent = `Rp ${shippingCost.toLocaleString('id-ID')}`;
            document.getElementById('selected-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;

            // Enable/disable checkout button
            const checkoutBtn = document.getElementById('checkout-btn');
            if (selectedCount > 0) {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            // Show/hide remove selected button
            const removeSelectedBtn = document.getElementById('remove-selected');
            if (selectedCount > 0) {
                removeSelectedBtn.classList.remove('hidden');
            } else {
                removeSelectedBtn.classList.add('hidden');
            }
        }

        function removeSelectedItems() {
            const selectedItems = getSelectedItems();
            if (selectedItems.length === 0) {
                showPopup('Pilih item yang ingin dihapus', 'error');
                return;
            }
            showCustomConfirm(`Apakah Anda yakin ingin menghapus ${selectedItems.length} item terpilih?`, (result) => {
                if (result) {
                    const promises = selectedItems.map(item =>
                        fetch('remove_from_cart.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ cart_id: item.id })
                        }).then(response => response.json())
                    );

                    Promise.all(promises)
                        .then(results => {
                            const success = results.every(result => result.success);
                            if (success) {
                                location.reload();
                            } else {
                                showPopup('Beberapa item gagal dihapus', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showPopup('Terjadi kesalahan saat menghapus item', 'error');
                        });
                }
            });
        }

        // Check for URL parameters
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize checkbox functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            const removeSelectedBtn = document.getElementById('remove-selected');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function () {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateOrderSummary();
            });

            // Individual checkbox functionality
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                    const anyChecked = Array.from(productCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = anyChecked && !allChecked;

                    updateOrderSummary();
                });
            });

            // Remove selected button
            removeSelectedBtn.addEventListener('click', removeSelectedItems);

            // Initialize order summary on page load
            updateOrderSummary();

            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const type = urlParams.get('type');

            if (success === 'true') {
                const message = type === 'cart' ?
                    'Keranjang berhasil diperbarui!' :
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