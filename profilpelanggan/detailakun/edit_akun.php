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
$message = '';
$error = '';

// Ambil data pelanggan
try {
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pelanggan_id]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pelanggan) {
        header("Location:.../../auth/login.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Sanitize and validate input
        $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $nomor_telepon = filter_input(INPUT_POST, 'nomor_telepon', FILTER_SANITIZE_STRING);
        $rt = filter_input(INPUT_POST, 'rt', FILTER_SANITIZE_STRING);
        $rw = filter_input(INPUT_POST, 'rw', FILTER_SANITIZE_STRING);
        $desa = filter_input(INPUT_POST, 'desa', FILTER_SANITIZE_STRING);
        $kode_pos = filter_input(INPUT_POST, 'kode_pos', FILTER_SANITIZE_STRING);
        $kelurahan = filter_input(INPUT_POST, 'kelurahan', FILTER_SANITIZE_STRING);
        $kecamatan = filter_input(INPUT_POST, 'kecamatan', FILTER_SANITIZE_STRING);
        $kabupaten = filter_input(INPUT_POST, 'kabupaten', FILTER_SANITIZE_STRING);
        $provinsi = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);

        // Validasi input
        if (empty($nama_lengkap)) {
            throw new Exception('Nama lengkap harus diisi!');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid!');
        }
        if (empty($nomor_telepon)) {
            throw new Exception('Nomor telepon harus diisi!');
        }

        // Cek apakah email sudah digunakan oleh user lain
        $check_email = "SELECT id_pelanggan FROM pelanggan WHERE email = ? AND id_pelanggan != ?";
        $check_stmt = $pdo->prepare($check_email);
        $check_stmt->execute([$email, $pelanggan_id]);

        if ($check_stmt->fetch()) {
            throw new Exception('Email sudah digunakan oleh pengguna lain!');
        }

        // Handle file upload dengan logika yang diperbaiki
        $foto_profil = $pelanggan['foto_profil']; // Keep existing photo by default
        $photo_changed = false;

        // Handle photo removal
        if (isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
            if ($pelanggan['foto_profil'] && $pelanggan['foto_profil'] !== 'default.jpg') {
                // Delete the old photo file
                $old_photo_path = __DIR__ . '/../uploads/pelanggan/' . $pelanggan['foto_profil'];
                if (file_exists($old_photo_path)) {
                    unlink($old_photo_path);
                }
            }
            $foto_profil = null; // Set to null instead of default.jpg
            $photo_changed = true;
        }
        // Handle new photo upload
        else if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['foto_profil']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filesize = $_FILES['foto_profil']['size'];
            $maxsize = 2 * 1024 * 1024; // 2MB

            // Validate file type
            if (!in_array($filetype, $allowed)) {
                throw new Exception('Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.');
            }

            // Validate file size
            if ($filesize > $maxsize) {
                throw new Exception('Ukuran file terlalu besar! Maksimal 2MB.');
            }

            // Set upload directory
            $upload_dir = __DIR__ . '/../uploads/pelanggan/';

            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    throw new Exception('Gagal membuat direktori uploads!');
                }
            }

            // Generate unique filename
            $new_filename = 'profil_' . $pelanggan_id . '_' . time() . '.' . $filetype;
            $upload_path = $upload_dir . $new_filename;

            // Delete old photo if exists
            if ($pelanggan['foto_profil'] && file_exists($upload_dir . $pelanggan['foto_profil'])) {
                unlink($upload_dir . $pelanggan['foto_profil']);
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                // Verify the uploaded file is actually an image
                $image_info = getimagesize($upload_path);
                if ($image_info === false) {
                    unlink($upload_path);
                    throw new Exception('File yang diupload bukan gambar yang valid!');
                }

                // Additional security validation
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $upload_path);
                    finfo_close($finfo);

                    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($mime_type, $allowed_mime)) {
                        unlink($upload_path);
                        throw new Exception('Tipe file tidak valid!');
                    }
                }

                // Set proper permissions
                chmod($upload_path, 0644);

                // Store only filename in database (not full path)
                $foto_profil = $new_filename;
                $photo_changed = true;

                // Update session for immediate header update
                $_SESSION['foto_profil'] = $new_filename;

            } else {
                $upload_error = $_FILES['foto_profil']['error'] ?? 0;
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                    UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                    UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                    UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary tidak ditemukan',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                    UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                ];

                $error_message = $error_messages[$upload_error] ?? 'Error upload tidak diketahui';
                throw new Exception('Gagal mengupload foto profil: ' . $error_message);
            }
        }

        // Gabungkan alamat lengkap
        $alamat_parts = array_filter([
            $desa ? "Desa $desa" : null,
            $rt ? "RT $rt" : null,
            $rw ? "RW $rw" : null,
            $kelurahan ? "Kel. $kelurahan" : null,
            $kecamatan ? "Kec. $kecamatan" : null,
            $kabupaten ? "Kab. $kabupaten" : null,
            $provinsi,
            $kode_pos
        ]);

        $alamat_lengkap = implode(', ', $alamat_parts);

        // Update data pelanggan
        $update_query = "UPDATE pelanggan SET 
            nama_lengkap = ?, 
            email = ?, 
            nomor_telepon = ?, 
            alamat = ?,
            foto_profil = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id_pelanggan = ?";

        $update_stmt = $pdo->prepare($update_query);
        $result = $update_stmt->execute([
            $nama_lengkap,
            $email,
            $nomor_telepon,
            $alamat_lengkap,
            $foto_profil,
            $pelanggan_id
        ]);

        if ($result) {
            // Update session data
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['email'] = $email;
            if ($photo_changed) {
                $_SESSION['foto_profil'] = $foto_profil;
            }

            $message_text = $photo_changed ? 'Foto profil berhasil diperbarui!' : 'Profil berhasil diperbarui!';

            // Set session untuk notifikasi
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => $message_text
            ];

            header("Location: profil_akun.php?success=true&type=" . ($photo_changed ? 'photo' : 'profile'));
            exit();
        } else {
            throw new Exception('Gagal menyimpan data ke database.');
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan database: ' . $e->getMessage();
        error_log('Database error in profil_edit.php: ' . $e->getMessage());
    }
}

// Function to get profile photo URL for display
function getProfilePhotoUrl($foto_profil)
{
    if (empty($foto_profil)) {
        return null;
    }

    // Check if file exists in uploads/pelanggan/
    $photo_path = __DIR__ . '/../uploads/pelanggan/' . $foto_profil;
    if (file_exists($photo_path)) {
        return '../uploads/pelanggan/' . $foto_profil;
    }

    return null;
}

$current_photo_url = getProfilePhotoUrl($pelanggan['foto_profil']);
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
            background-color: #f8f9fa;
            position: relative;
        }

        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease-in-out;
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
        <?php require '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Edit Profil</h2>
                </div>

                <!-- Messages -->
                <?php if ($error): ?>
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Photo Section -->
                <div class="flex flex-col items-center mb-8">
                    <div class="relative group mb-4">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-orange-100 shadow-lg cursor-pointer transition-transform duration-300 hover:scale-105"
                            onclick="document.getElementById('foto_input').click()">
                            <?php if ($current_photo_url): ?>
                                <img id="preview-image" src="<?php echo htmlspecialchars($current_photo_url); ?>"
                                    alt="Foto Profil" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div id="preview-image" class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div
                            class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-camera text-white text-2xl"></i>
                        </div>
                    </div>

                    <div class="flex flex-col items-center space-y-3">
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="document.getElementById('foto_input').click()"
                                class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition-colors flex items-center shadow-sm">
                                <i class="fas fa-camera mr-2"></i> Upload Foto
                            </button>
                            <?php if ($current_photo_url): ?>
                                <button type="button" onclick="removeProfilePhoto()"
                                    class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors flex items-center shadow-sm">
                                    <i class="fas fa-trash mr-2"></i> Hapus Foto
                                </button>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-gray-500 bg-gray-50 px-3 py-1 rounded-full">Format: JPG, PNG, GIF
                            (max. 2MB)</span>
                    </div>
                </div>

                <!-- Edit Form -->
                <form method="POST" class="space-y-4" enctype="multipart/form-data">
                    <input type="file" id="foto_input" name="foto_profil"
                        accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden" onchange="previewImage(this)">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap"
                                value="<?php echo htmlspecialchars($pelanggan['nama_lengkap'] ?? ''); ?>"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                required>
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <input type="email" name="email"
                                    value="<?php echo htmlspecialchars($pelanggan['email'] ?? ''); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pr-10"
                                    required>
                                <i
                                    class="fas fa-check-circle text-green-500 absolute right-3 top-1/2 transform -translate-y-1/2"></i>
                            </div>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="tel" name="nomor_telepon"
                                value="<?php echo htmlspecialchars($pelanggan['nomor_telepon'] ?? ''); ?>"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="mt-6">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Alamat Lengkap</h3>

                        <?php
                        // Parse existing address
                        $alamat_array = explode(', ', $pelanggan['alamat'] ?? '');
                        $parsed_address = [
                            'rt' => '',
                            'rw' => '',
                            'desa' => '',
                            'kelurahan' => '',
                            'kecamatan' => '',
                            'kabupaten' => '',
                            'provinsi' => '',
                            'kode_pos' => ''
                        ];

                        foreach ($alamat_array as $part) {
                            if (strpos($part, 'RT ') === 0) {
                                $parsed_address['rt'] = substr($part, 3);
                            } elseif (strpos($part, 'RW ') === 0) {
                                $parsed_address['rw'] = substr($part, 3);
                            } elseif (strpos($part, 'Desa ') === 0) {
                                $parsed_address['desa'] = substr($part, 5);
                            } elseif (strpos($part, 'Kel. ') === 0) {
                                $parsed_address['kelurahan'] = substr($part, 5);
                            } elseif (strpos($part, 'Kec. ') === 0) {
                                $parsed_address['kecamatan'] = substr($part, 5);
                            } elseif (strpos($part, 'Kab. ') === 0) {
                                $parsed_address['kabupaten'] = substr($part, 5);
                            } elseif (is_numeric($part)) {
                                $parsed_address['kode_pos'] = $part;
                            } else {
                                if (
                                    empty($parsed_address['provinsi']) &&
                                    !in_array($part, ['RT', 'RW', 'Desa', 'Kel.', 'Kec.', 'Kab.'])
                                ) {
                                    $parsed_address['provinsi'] = $part;
                                }
                            }
                        }
                        ?>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                                <input type="text" name="rt"
                                    value="<?php echo htmlspecialchars($parsed_address['rt']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="001">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                                <input type="text" name="rw"
                                    value="<?php echo htmlspecialchars($parsed_address['rw']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="002">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                                <input type="text" name="desa"
                                    value="<?php echo htmlspecialchars($parsed_address['desa']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Nama Desa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos"
                                    value="<?php echo htmlspecialchars($parsed_address['kode_pos']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="12345">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                                <input type="text" name="kelurahan"
                                    value="<?php echo htmlspecialchars($parsed_address['kelurahan']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Nama Kelurahan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                <input type="text" name="kecamatan"
                                    value="<?php echo htmlspecialchars($parsed_address['kecamatan']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Nama Kecamatan">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                                <input type="text" name="kabupaten"
                                    value="<?php echo htmlspecialchars($parsed_address['kabupaten']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Nama Kabupaten">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                                <input type="text" name="provinsi"
                                    value="<?php echo htmlspecialchars($parsed_address['provinsi']); ?>"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Nama Provinsi">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <a href="profil_akun.php"
                            class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <!-- Popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

    <!-- Custom Confirmation Modal -->
    <div id="customConfirmModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
            <div class="text-xl font-bold text-gray-900 mb-4" id="confirmMessage"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.');
                    input.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewContainer = document.querySelector('.group .w-32.h-32');
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview Foto Profil';
                    img.className = 'w-full h-full object-cover';
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
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

        function removeProfilePhoto() {
            showCustomConfirm('Yakin ingin menghapus foto profil?', function (result) {
                if (result) {
                    const previewContainer = document.querySelector('.group .w-32.h-32');
                    previewContainer.innerHTML = '<div class="w-full h-full bg-gray-200 flex items-center justify-center"><i class="fas fa-user text-gray-500 text-4xl"></i></div>';

                    const removePhotoInput = document.createElement('input');
                    removePhotoInput.type = 'hidden';
                    removePhotoInput.name = 'remove_photo';
                    removePhotoInput.value = '1';
                    document.querySelector('form').appendChild(removePhotoInput);

                    document.querySelector('form').submit();
                }
            });
        }

        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popupNotification');
            popup.textContent = message;
            popup.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            popup.classList.add('show');

            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>