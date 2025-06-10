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
    <title>Ling-Ling Pet Shop - Riwayat Pesanan</title>
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
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-history text-orange-500 mr-2"></i>
                        Riwayat Pesanan
                    </h2>
                    <p class="text-gray-600">Lihat semua riwayat pesanan dan layanan Anda</p>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// ================================================================
// File: profilpelanggan/pesanan/pesanan_produk.php
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

// Query untuk mengambil pesanan produk
$query = "SELECT p.*, pr.nama_produk, pr.harga, pr.gambar 
          FROM pemesanan p 
          LEFT JOIN produk pr ON p.id_produk = pr.id_produk 
          WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'produk' 
          ORDER BY p.tanggal_pesan DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pesanan_produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pesanan-content">
    <?php if (empty($pesanan_produk)): ?>
        <div class="no-data">
            <i class="fas fa-shopping-bag"></i>
            <div class="mb-3">
                <strong>Belum ada pesanan produk</strong>
            </div>
            <p class="mb-4">Pesanan produk Anda akan tampil di sini setelah melakukan pembelian</p>
            <a href="../../produk/" class="btn btn-primary-custom">
                <i class="fas fa-shopping-cart mr-2"></i>
                Belanja Sekarang
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($pesanan_produk as $pesanan): ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <span class="pesanan-id">
                        <i class="fas fa-receipt mr-2"></i>
                        #<?= htmlspecialchars($pesanan['id_pemesanan'] ?? 'PRD' . str_pad($pesanan['id_pemesanan'], 3, '0', STR_PAD_LEFT)) ?>
                    </span>
                    <span class="pesanan-status status-<?= strtolower($pesanan['status'] ?? 'pending') ?>">
                        <?= ucfirst($pesanan['status'] ?? 'Pending') ?>
                    </span>
                </div>
                <div class="pesanan-detail">
                    <div class="row">
                        <div class="col-md-2">
                            <?php if (!empty($pesanan['gambar'])): ?>
                                <img src="../../assets/images/products/<?= htmlspecialchars($pesanan['gambar']) ?>"
                                    alt="<?= htmlspecialchars($pesanan['nama_produk']) ?>" class="img-fluid rounded"
                                    style="max-height: 60px;">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="height: 60px; width: 60px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-10">
                            <strong><?= htmlspecialchars($pesanan['nama_produk'] ?? 'Produk') ?></strong><br>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>
                                <?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesan'])) ?>
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-sort-numeric-up mr-1"></i>
                                Jumlah: <?= number_format($pesanan['jumlah'] ?? 1) ?> item
                            </small><br>
                            <div class="mt-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    Rp <?= number_format($pesanan['total_harga'] ?? $pesanan['harga'] ?? 0, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// ================================================================
// File: profilpelanggan/pesanan/pesanan_perawatan.php
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

// Query untuk mengambil pesanan perawatan/grooming
$query = "SELECT p.*, l.nama_layanan, l.harga_layanan, a.nama_anabul 
          FROM pemesanan p 
          LEFT JOIN layanan l ON p.id_layanan = l.id_layanan 
          LEFT JOIN anabul a ON p.id_anabul = a.id_anabul
          WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'perawatan' 
          ORDER BY p.tanggal_pesan DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pesanan_perawatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pesanan-content">
    <?php if (empty($pesanan_perawatan)): ?>
        <div class="no-data">
            <i class="fas fa-cut"></i>
            <div class="mb-3">
                <strong>Belum ada jadwal grooming di riwayat</strong>
            </div>
            <p class="mb-4">Yuk, berikan perawatan terbaik agar si imut tampil kece dan sehat—pesan layanan grooming
                sekarang!</p>
            <a href="../../perawatan/" class="btn btn-primary-custom">
                <i class="fas fa-scissors mr-2"></i>
                Saatnya Perawatan
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($pesanan_perawatan as $pesanan): ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <span class="pesanan-id">
                        <i class="fas fa-spa mr-2"></i>
                        #<?= htmlspecialchars($pesanan['id_pemesanan'] ?? 'PRW' . str_pad($pesanan['id_pemesanan'], 3, '0', STR_PAD_LEFT)) ?>
                    </span>
                    <span class="pesanan-status status-<?= strtolower($pesanan['status'] ?? 'pending') ?>">
                        <?= ucfirst($pesanan['status'] ?? 'Pending') ?>
                    </span>
                </div>
                <div class="pesanan-detail">
                    <strong><?= htmlspecialchars($pesanan['nama_layanan'] ?? 'Layanan Perawatan') ?></strong><br>
                    <small class="text-muted">
                        <i class="fas fa-paw mr-1"></i>
                        Hewan: <?= htmlspecialchars($pesanan['nama_anabul'] ?? 'Tidak diketahui') ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar mr-1"></i>
                        Tanggal: <?= date('d/m/Y', strtotime($pesanan['tanggal_layanan'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        Waktu: <?= date('H:i', strtotime($pesanan['waktu_layanan'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Rp <?= number_format($pesanan['total_harga'] ?? $pesanan['harga_layanan'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// ================================================================
// File: profilpelanggan/pesanan/pesanan_penitipan.php
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

// Query untuk mengambil pesanan penitipan
$query = "SELECT p.*, a.nama_anabul 
          FROM pemesanan p 
          LEFT JOIN anabul a ON p.id_anabul = a.id_anabul
          WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'penitipan' 
          ORDER BY p.tanggal_pesan DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pesanan_penitipan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pesanan-content">
    <?php if (empty($pesanan_penitipan)): ?>
        <div class="no-data">
            <i class="fas fa-home"></i>
            <div class="mb-3">
                <strong>Belum ada riwayat penitipan</strong>
            </div>
            <p class="mb-4">Riwayat penitipan hewan Anda akan tampil di sini</p>
            <a href="../../penitipan/" class="btn btn-primary-custom">
                <i class="fas fa-home mr-2"></i>
                Pesan Penitipan
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($pesanan_penitipan as $pesanan): ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <span class="pesanan-id">
                        <i class="fas fa-bed mr-2"></i>
                        #<?= htmlspecialchars($pesanan['id_pemesanan'] ?? 'PNT' . str_pad($pesanan['id_pemesanan'], 3, '0', STR_PAD_LEFT)) ?>
                    </span>
                    <span class="pesanan-status status-<?= strtolower($pesanan['status'] ?? 'pending') ?>">
                        <?= ucfirst($pesanan['status'] ?? 'Pending') ?>
                    </span>
                </div>
                <div class="pesanan-detail">
                    <strong>Penitipan Hewan</strong><br>
                    <small class="text-muted">
                        <i class="fas fa-paw mr-1"></i>
                        Hewan: <?= htmlspecialchars($pesanan['nama_anabul'] ?? 'Tidak diketahui') ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Check-in: <?= date('d/m/Y', strtotime($pesanan['tanggal_mulai'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar-times mr-1"></i>
                        Check-out: <?= date('d/m/Y', strtotime($pesanan['tanggal_selesai'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        Durasi: <?= $pesanan['durasi'] ?? '1' ?> hari
                    </small><br>
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Rp <?= number_format($pesanan['total_harga'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// ================================================================
// File: profilpelanggan/pesanan/pesanan_konsultasi.php

$pelanggan_id = $_SESSION['id_pelanggan'];

// Query untuk mengambil pesanan konsultasi
$query = "SELECT p.*, d.nama_dokter, a.nama_anabul 
          FROM pemesanan p 
          LEFT JOIN dokter d ON p.id_dokter = d.id_dokter 
          LEFT JOIN anabul a ON p.id_anabul = a.id_anabul
          WHERE p.id_pelanggan = ? AND p.jenis_pesanan = 'konsultasi' 
          ORDER BY p.tanggal_pesan DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$pelanggan_id]);
$pesanan_konsultasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pesanan-content">
    <?php if (empty($pesanan_konsultasi)): ?>
        <div class="no-data">
            <i class="fas fa-stethoscope"></i>
            <div class="mb-3">
                <strong>Belum ada riwayat konsultasi</strong>
            </div>
            <p class="mb-4">Riwayat konsultasi dengan dokter hewan akan tampil di sini</p>
        </div>
    <?php else: ?>
        <?php foreach ($pesanan_konsultasi as $pesanan): ?>
            <div class="pesanan-item">
                <div class="pesanan-header">
                    <span class="pesanan-id">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        #<?= htmlspecialchars($pesanan['id_pemesanan'] ?? 'KNS' . str_pad($pesanan['id_pemesanan'], 3, '0', STR_PAD_LEFT)) ?>
                    </span>
                    <span class="pesanan-status status-<?= strtolower($pesanan['status'] ?? 'pending') ?>">
                        <?= ucfirst($pesanan['status'] ?? 'Pending') ?>
                    </span>
                </div>
                <div class="pesanan-detail">
                    <strong>Konsultasi Dokter Hewan</strong><br>
                    <small class="text-muted">
                        <i class="fas fa-user-md mr-1"></i>
                        Dokter: <?= htmlspecialchars($pesanan['nama_dokter'] ?? 'Tidak diketahui') ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-paw mr-1"></i>
                        Hewan: <?= htmlspecialchars($pesanan['nama_anabul'] ?? 'Tidak diketahui') ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-calendar mr-1"></i>
                        Tanggal: <?= date('d/m/Y', strtotime($pesanan['tanggal_konsultasi'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        Waktu: <?= date('H:i', strtotime($pesanan['waktu_konsultasi'] ?? $pesanan['tanggal_pesan'])) ?>
                    </small><br>
                    <?php if (!empty($pesanan['keluhan'])): ?>
                        <small class="text-muted">
                            <i class="fas fa-comment-medical mr-1"></i>
                            Keluhan:
                            <?= htmlspecialchars(substr($pesanan['keluhan'], 0, 50)) ?>            <?= strlen($pesanan['keluhan']) > 50 ? '...' : '' ?>
                        </small><br>
                    <?php endif; ?>
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Rp <?= number_format($pesanan['total_harga'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>