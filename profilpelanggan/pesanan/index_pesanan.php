<?php
// File: profilpelanggan/pesanan/index.php (File Utama)
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

// Ambil data pelanggan
$pelanggan_id = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
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

        /* Tab Navigation Styles */
        .tab-nav {
            display: flex;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .tab-btn {
            flex: 1;
            padding: 14px 16px;
            background: #f9fafb;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            position: relative;
        }

        .tab-btn.active {
            background: #fff;
            color: #f97316;
            border-bottom-color: #f97316;
            font-weight: 600;
        }

        .tab-btn:hover:not(.active) {
            background: #f3f4f6;
            color: #374151;
        }

        .tab-content {
            min-height: 400px;
        }

        .loading {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .loading i {
            font-size: 2rem;
            color: #f97316;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .pesanan-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .pesanan-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .pesanan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .pesanan-id {
            font-weight: 600;
            color: #374151;
            font-size: 15px;
        }

        .pesanan-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-selesai {
            background: #dcfce7;
            color: #166534;
        }

        .status-proses {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-dibatalkan {
            background: #f3f4f6;
            color: #4b5563;
        }

        .pesanan-detail {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
        }

        .pesanan-detail strong {
            color: #1f2937;
            font-weight: 600;
            font-size: 15px;
            display: block;
            margin-bottom: 4px;
        }

        .pesanan-detail small {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .pesanan-detail .badge {
            font-size: 13px;
            padding: 6px 12px;
            font-weight: 500;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-data i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .no-data strong {
            font-size: 18px;
            color: #374151;
            display: block;
            margin-bottom: 12px;
        }

        .no-data p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .btn-primary-custom {
            background: #f97316;
            border-color: #f97316;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: #ea580c;
            border-color: #ea580c;
            transform: translateY(-1px);
        }

        .tab-btn i {
            font-size: 14px;
            margin-right: 8px;
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
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">
                        Riwayat Pesanan
                    </h2>
                    <p class="text-gray-600 text-base">Lihat semua riwayat pesanan dan layanan Anda</p>
                </div>

                <!-- Tab Navigation -->
                <div class="tab-nav">
                    <button class="tab-btn active" onclick="loadTab('produk', this)">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Produk
                    </button>
                    <button class="tab-btn" onclick="loadTab('perawatan', this)">
                        <i class="fas fa-cut mr-2"></i>
                        Perawatan
                    </button>
                    <button class="tab-btn" onclick="loadTab('penitipan', this)">
                        <i class="fas fa-home mr-2"></i>
                        Penitipan
                    </button>
                    <button class="tab-btn" onclick="loadTab('konsultasi', this)">
                        <i class="fas fa-stethoscope mr-2"></i>
                        Konsultasi
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="tab-content">
                    <!-- Konten akan dimuat di sini -->
                </div>
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
                    class="px-6 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors text-sm">
                    Ya, Sudah Bayar
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

        // Function to load tab content
        function loadTab(tabName, buttonElement) {
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            buttonElement.classList.add('active');

            // Show loading
            document.getElementById('tab-content').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <div class="mt-3">Memuat data...</div>
                </div>
            `;

            // Load content via AJAX
            fetch(`pesanan_${tabName}.php`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('tab-content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('tab-content').innerHTML = `
                        <div class="no-data">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>Gagal memuat data</div>
                            <small class="text-muted">Silakan coba lagi nanti</small>
                        </div>
                    `;
                });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            // Load default tab
            const defaultTab = document.querySelector('.tab-btn.active');
            loadTab('produk', defaultTab);

            // Check for URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const type = urlParams.get('type');

            if (success === 'true') {
                const message = type === 'pesanan' ?
                    'Pesanan berhasil dibuat!' :
                    'Operasi berhasil dilakukan!';
                showPopup(message, 'success');

                // Clean URL parameters after showing notification
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // Check session notification
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

        // Fungsi untuk salin hanya nomor rekening/ewallet
        function copyInputValue(inputId) {
            var input = document.getElementById(inputId);
            var value = input.value;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(value).then(function () {
                    if (typeof showPopup === 'function') showPopup('Nomor berhasil disalin!', 'success');
                }).catch(function (err) {
                    // Fallback jika gagal
                    fallbackCopy(value);
                    if (typeof showPopup === 'function') showPopup('Gagal menyalin dengan clipboard API, mencoba fallback.', 'error');
                    console.log('Clipboard API error:', err);
                });
            } else {
                fallbackCopy(value);
            }
        }
        function fallbackCopy(value) {
            try {
                var tempInput = document.createElement('input');
                tempInput.value = value;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                var success = document.execCommand('copy');
                document.body.removeChild(tempInput);
                if (success) {
                    if (typeof showPopup === 'function') showPopup('Nomor berhasil disalin!', 'success');
                } else {
                    alert('Gagal menyalin nomor. Silakan salin manual.');
                }
            } catch (e) {
                alert('Gagal menyalin nomor. Silakan salin manual.');
                console.log('Fallback copy error:', e);
            }
        }
        function sudahBayarNotif() {
            if (typeof showPopup === 'function') {
                showPopup('Terima kasih, pembayaran Anda akan segera diverifikasi.', 'success');
            } else {
                alert('Terima kasih, pembayaran Anda akan segera diverifikasi.');
            }
        }
        function showCustomConfirm(message, callback) {
            const modal = document.getElementById('customConfirmModal');
            const messageEl = document.getElementById('confirmMessage');
            const confirmOKBtn = document.getElementById('confirmOKBtn');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');

            messageEl.textContent = message;
            modal.classList.remove('hidden');

            const handleConfirm = () => {
                callback(true);
                modal.classList.add('hidden');
                confirmOKBtn.removeEventListener('click', handleConfirm);
                confirmCancelBtn.removeEventListener('click', handleCancel);
            };

            const handleCancel = () => {
                callback(false);
                modal.classList.add('hidden');
                confirmOKBtn.removeEventListener('click', handleConfirm);
                confirmCancelBtn.removeEventListener('click', handleCancel);
            };

            confirmOKBtn.addEventListener('click', handleConfirm);
            confirmCancelBtn.addEventListener('click', handleCancel);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    handleCancel();
                }
            });
        }
        function sudahBayarAjax(id_pesanan, btn) {
            showCustomConfirm('Apakah Anda yakin sudah melakukan pembayaran?', function (result) {
                if (!result) return;
                btn.disabled = true;
                fetch('update_status_pembayaran.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_pesanan=${id_pesanan}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof showPopup === 'function') showPopup('Status pembayaran berhasil diupdate. Menunggu konfirmasi admin.', 'success');
                            setTimeout(function () { window.location.reload(); }, 1200);
                        } else {
                            btn.disabled = false;
                            if (typeof showPopup === 'function') showPopup('Gagal update status pembayaran!', 'error');
                        }
                    })
                    .catch((err) => {
                        btn.disabled = false;
                        if (typeof showPopup === 'function') showPopup('Gagal update status pembayaran!', 'error');
                    });
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>