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

        .popup-confirm {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .popup-box {
            background: #fff;
            border-radius: 12px;
            padding: 32px 24px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 350px;
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
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <!-- Left: Image -->
                    <div class="w-full md:w-1/2 flex justify-center mb-4 md:mb-0">
                        <img src="../../aset/anjinghapusakun.png" alt="" class="max-w-xs w-full h-auto">
                    </div>
                    <!-- Right: Text & Button -->
                    <div class="w-full md:w-1/2 flex flex-col justify-center">
                        <span class="mb-6 block text-justify">Apakah Anda yakin ingin menghapus akun Anda? Dengan
                            melakukannya, Anda
                            tidak hanya kehilangan akses ke semua layanan, tetapi juga kehilangan kesempatan untuk terus
                            mengelola kebutuhan si anabul kesayangan. Pertimbangkan kembali keputusan ini agar si anabul
                            tetap mendapatkan perhatian terbaiknya! Jika Anda benar-benar ingin melanjutkan, klik tombol
                            di bawah ini!</span>
                        <button id="btnHapusAkun" class="btn btn-danger w-fit px-4 py-2 rounded">Hapus Akun</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Konfirmasi -->
    <div id="customConfirmModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
            <div class="text-xl font-bold text-gray-900 mb-4 text-center" id="confirmMessage">Apakah Anda yakin ingin
                menghapus akun Anda secara permanen?</div>
            <div class="flex justify-end space-x-3">
                <button id="konfirmasiTidak"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-full hover:bg-gray-100 transition-colors text-sm">Batal</button>
                <button id="konfirmasiYa"
                    class="px-6 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors text-sm">Ya,
                    Hapus</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('btnHapusAkun').onclick = function () {
                document.getElementById('customConfirmModal').classList.remove('hidden');
            };
            document.getElementById('konfirmasiTidak').onclick = function () {
                document.getElementById('customConfirmModal').classList.add('hidden');
            };
            document.getElementById('konfirmasiYa').onclick = function () {
                fetch('hapus.php?action=delete', { method: 'POST' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '../../public/index.php';
                        } else {
                            alert('Gagal menghapus akun!');
                            document.getElementById('customConfirmModal').classList.add('hidden');
                        }
                    });
            };
            document.getElementById('customConfirmModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
    <?php
    // Proses hapus akun jika ada request POST dengan action=delete (AJAX)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = $_SESSION['id_pelanggan'];
        // Hapus data pelanggan
        $stmt = $pdo->prepare('DELETE FROM pelanggan WHERE id_pelanggan = ?');
        $success = $stmt->execute([$id]);
        // Hapus session dan logout
        session_unset();
        session_destroy();
        echo json_encode(['success' => $success]);
        exit();
    }
    ?>
</body>

</html>