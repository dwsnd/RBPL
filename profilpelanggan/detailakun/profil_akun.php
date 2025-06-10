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
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

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

    // Fungsi helper untuk menentukan path foto profil
    function getProfilePhotoPath($fotoProfilDb)
    {
        if (empty($fotoProfilDb)) {
            return '';
        }

        $possiblePaths = [
            '../../uploads/pelanggan/' . $fotoProfilDb,
            '../uploads/pelanggan/' . $fotoProfilDb,
            'uploads/pelanggan/' . $fotoProfilDb,
            '../../' . $fotoProfilDb,
            '../' . $fotoProfilDb,
            $fotoProfilDb
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    // Ambil data pelanggan
    $pelanggan_id = $_SESSION['id_pelanggan'];
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pelanggan_id]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Gunakan fungsi helper untuk mendapatkan path yang benar
    $profilePhotoPath = getProfilePhotoPath($pelanggan['foto_profil']);

    // Handle upload foto profil
    if (isset($_POST['upload_foto'])) {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['foto_profil']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array(strtolower($filetype), $allowed)) {
                $new_filename = 'profil_' . $pelanggan_id . '_' . time() . '.' . $filetype;

                $upload_dir = '../../uploads/pelanggan/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                    // Hapus foto lama jika ada
                    if (!empty($pelanggan['foto_profil'])) {
                        $oldPhotoPath = getProfilePhotoPath($pelanggan['foto_profil']);
                        if (!empty($oldPhotoPath) && file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }

                    $update_query = "UPDATE pelanggan SET foto_profil = ? WHERE id_pelanggan = ?";
                    $update_stmt = $pdo->prepare($update_query);
                    $update_stmt->execute([$new_filename, $pelanggan_id]);

                    $_SESSION['foto_profil'] = $new_filename;
                    header("Location: profil_akun.php?success=true&type=photo");
                    exit;
                } else {
                    $error_message = "Gagal mengupload foto profil.";
                }
            } else {
                $error_message = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
            }
        } else {
            $error_message = "Silakan pilih file foto profil.";
        }
    }
    ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Profil Saya</h2>
                </div>

                <!-- Profile Photo Section -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <?php if (!empty($profilePhotoPath)): ?>
                            <img src="<?php echo htmlspecialchars($profilePhotoPath); ?>" alt="Foto Profil"
                                class="w-32 h-32 rounded-full object-cover border-2 border-gray-200"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <!-- Fallback jika gambar tidak bisa dimuat -->
                            <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200"
                                style="display: none;">
                                <i class="fas fa-user text-4xl text-gray-600"></i>
                            </div>
                        <?php else: ?>
                            <div
                                class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200">
                                <i class="fas fa-user text-4xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" value="<?php echo htmlspecialchars($pelanggan['nama_lengkap'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <input type="email" value="<?php echo htmlspecialchars($pelanggan['email'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 pr-10 text-sm"
                                readonly>
                            <i
                                class="fas fa-check-circle text-green-500 absolute right-3 top-1/2 transform -translate-y-1/2"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" value="<?php echo htmlspecialchars($pelanggan['nomor_telepon'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 h-20 text-sm"
                            readonly><?php echo htmlspecialchars($pelanggan['alamat'] ?? '-'); ?></textarea>
                    </div>
                </div>

                <!-- Edit Button -->
                <div class="mt-6">
                    <a href="edit_akun.php"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Edit Profil
                    </a>
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

        // PERBAIKAN: Tambahkan pengecekan session notification
        document.addEventListener('DOMContentLoaded', function () {
            // Check for URL parameters first
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const type = urlParams.get('type');

            if (success === 'true') {
                const message = type === 'photo' ?
                    'Foto profil berhasil diperbarui!' :
                    'Profil berhasil diperbarui!';
                showPopup(message, 'success');

                // Clean URL parameters after showing notification
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // ALTERNATIF: Jika menggunakan session, tambahkan AJAX call
            // untuk mengecek session notification

            // Check session notification via AJAX
            fetch('check_notification.php')
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