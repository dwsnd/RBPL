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

// Get anabul ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profil_anabul.php");
    exit();
}

$anabul_id = (int) $_GET['id'];
$pelanggan_id = $_SESSION['id_pelanggan'];

// Get anabul data with owner verification
$query = "SELECT * FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$anabul_id, $pelanggan_id]);
$anabul = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anabul) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'Data anabul tidak ditemukan atau Anda tidak memiliki akses.'
    ];
    header("Location: profil_anabul.php");
    exit();
}

// Get anabul photos
$foto_query = "SELECT * FROM anabul_foto WHERE id_anabul = ? ORDER BY urutan ASC";
$foto_stmt = $pdo->prepare($foto_query);
$foto_stmt->execute([$anabul_id]);
$anabul_fotos = $foto_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['nama_hewan']) || empty($_POST['kategori_hewan'])) {
            throw new Exception('Nama hewan dan kategori hewan wajib diisi.');
        }

        // Handle photo deletions
        if (isset($_POST['deleted_photos']) && is_array($_POST['deleted_photos'])) {
            foreach ($_POST['deleted_photos'] as $deleted_photo) {
                // Delete from database
                $delete_foto_query = "DELETE FROM anabul_foto WHERE id_anabul = ? AND nama_file = ?";
                $delete_foto_stmt = $pdo->prepare($delete_foto_query);
                $delete_foto_stmt->execute([$anabul_id, $deleted_photo]);

                // Delete file from filesystem
                $file_path = '../../uploads/anabul/' . $deleted_photo;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        // Handle photo uploads
        $uploaded_files = [];
        $upload_dir = '../../uploads/anabul/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (isset($_FILES['foto']) && is_array($_FILES['foto']['name'])) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $max_size = 5 * 1024 * 1024; // 5MB
            $max_files = 4;

            // Validate number of files
            if (count($_FILES['foto']['name']) > $max_files) {
                throw new Exception("Maksimal {$max_files} foto yang dapat diunggah.");
            }

            foreach ($_FILES['foto']['name'] as $key => $name) {
                if ($_FILES['foto']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_type = $_FILES['foto']['type'][$key];
                    $file_size = $_FILES['foto']['size'][$key];
                    $file_tmp = $_FILES['foto']['tmp_name'][$key];

                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
                    }

                    if ($file_size > $max_size) {
                        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
                    }

                    $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                    $new_foto_name = uniqid('anabul_') . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_foto_name;

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $uploaded_files[] = $new_foto_name;
                    } else {
                        throw new Exception('Gagal mengunggah foto.');
                    }
                }
            }
        }

        // Begin transaction
        $pdo->beginTransaction();

        try {
            // Update anabul data
            $update_query = "UPDATE anabul SET 
                nama_hewan = ?, 
                kategori_hewan = ?, 
                jenis_ras = ?, 
                umur_tahun = ?, 
                umur_bulan = ?, 
                berat = ?, 
                jenis_kelamin = ?, 
                riwayat_kesehatan = ?, 
                karakteristik = ?, 
                updated_at = ? 
                WHERE id_anabul = ? AND id_pelanggan = ?";

            $update_stmt = $pdo->prepare($update_query);
            $update_result = $update_stmt->execute([
                trim($_POST['nama_hewan']),
                $_POST['kategori_hewan'],
                !empty($_POST['jenis_ras']) ? trim($_POST['jenis_ras']) : null,
                !empty($_POST['umur_tahun']) ? (int) $_POST['umur_tahun'] : null,
                !empty($_POST['umur_bulan']) ? (int) $_POST['umur_bulan'] : null,
                !empty($_POST['berat']) ? (float) $_POST['berat'] : null,
                !empty($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null,
                !empty($_POST['riwayat_kesehatan']) ? trim($_POST['riwayat_kesehatan']) : null,
                !empty($_POST['karakteristik']) ? trim($_POST['karakteristik']) : null,
                date('Y-m-d H:i:s'),
                $anabul_id,
                $pelanggan_id
            ]);

            if (!$update_result) {
                throw new Exception('Gagal memperbarui data anabul.');
            }

            // Handle photo updates
            if (!empty($uploaded_files)) {
                // Get current max order
                $max_order_query = "SELECT MAX(urutan) as max_order FROM anabul_foto WHERE id_anabul = ?";
                $max_order_stmt = $pdo->prepare($max_order_query);
                $max_order_stmt->execute([$anabul_id]);
                $max_order = $max_order_stmt->fetch(PDO::FETCH_ASSOC)['max_order'] ?? 0;

                // Insert new photos
                $insert_foto_query = "INSERT INTO anabul_foto (id_anabul, nama_file, urutan) VALUES (?, ?, ?)";
                $insert_foto_stmt = $pdo->prepare($insert_foto_query);

                foreach ($uploaded_files as $index => $filename) {
                    $insert_foto_stmt->execute([
                        $anabul_id,
                        $filename,
                        $max_order + $index + 1
                    ]);
                }

                // Update main photo in anabul table if no main photo exists
                if (empty($anabul['foto_utama'])) {
                    $update_main_foto_query = "UPDATE anabul SET foto_utama = ? WHERE id_anabul = ?";
                    $update_main_foto_stmt = $pdo->prepare($update_main_foto_query);
                    $update_main_foto_stmt->execute([$uploaded_files[0], $anabul_id]);
                }
            }

            $pdo->commit();

            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Data anabul berhasil diperbarui!'
            ];
            header("Location: profil_anabul.php");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
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

        .upload-zone {
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .upload-zone:hover {
            border-color: #fb923c;
            background-color: #fef3f2;
        }

        .drag-over {
            border-color: #ea580c !important;
            background-color: #fed7cc !important;
        }

        .preview-item {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background-color: #f8f9fa;
        }

        .remove-btn {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 28px;
            height: 28px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .remove-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* Custom Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 24px;
        }

        .modal-button {
            padding: 8px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-button.cancel {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .modal-button.cancel:hover {
            background-color: #e5e7eb;
        }

        .modal-button.confirm {
            background-color: #ef4444;
            color: white;
            border: none;
        }

        .modal-button.confirm:hover {
            background-color: #dc2626;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <div class="flex min-h-screen p-4">
        <?php require_once '../sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 px-6 pb-4 max-w-6xl mx-auto">
            <div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
                <!-- Header -->
                <div class="mb-4">
                    <a href="profil_anabul.php"
                        class="inline-flex items-center text-orange-600 text-sm hover:text-orange-700 mb-2">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Profil
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Edit Data Anabul</h1>
                </div>

                <!-- Error Message -->
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <strong>Error:</strong>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php foreach ($anabul_fotos as $foto): ?>
                                    <div class="relative bg-gray-100 rounded-lg">
                                        <img src="../../uploads/anabul/<?php echo htmlspecialchars($foto['nama_file']); ?>"
                                            alt="Foto anabul" class="w-full h-48 object-contain rounded-lg">
                                        <div class="absolute top-2 right-2">
                                            <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                                Foto <?php echo $foto['urutan']; ?>
                                            </span>
                                        </div>
                                        <button type="button"
                                            onclick="removeExistingPhoto('<?php echo htmlspecialchars($foto['nama_file']); ?>', this)"
                                            class="remove-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Upload Zone -->
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors h-48 flex items-center justify-center"
                                    onclick="document.getElementById('foto').click()" ondrop="handleDrop(event)"
                                    ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                                    <div id="upload-area" class="cursor-pointer">
                                        <i class="fas fa-plus text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">Tambah foto</p>
                                        <p class="text-xs text-gray-500" id="remaining-slots">
                                            <?php echo 4 - count($anabul_fotos); ?> slot tersisa
                                        </p>
                                    </div>
                                    <input type="file" id="foto" name="foto[]" accept="image/*" class="hidden" multiple
                                        onchange="handleFileSelect(this)">
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Foto saat ini</p>
                        </div>

                        <!-- Preview Area -->
                        <div id="preview-area" class="hidden mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <span id="photo-counter" class="text-sm text-gray-600">0/4 foto dipilih</span>
                                <button type="button" onclick="removeAllImages()"
                                    class="text-red-500 hover:text-red-700 text-sm px-3 py-1 rounded border border-red-200 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash mr-1"></i> Hapus Semua
                                </button>
                            </div>
                            <div id="preview-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Preview images will be inserted here -->
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Hewan -->
                            <div>
                                <label for="nama_hewan" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Hewan Peliharaan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nama_hewan" name="nama_hewan"
                                    value="<?php echo htmlspecialchars($anabul['nama_hewan']); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Masukkan nama peliharaan" required>
                            </div>

                            <!-- Kategori Hewan -->
                            <div>
                                <label for="kategori_hewan" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kategori Hewan <span class="text-red-500">*</span>
                                </label>
                                <select id="kategori_hewan" name="kategori_hewan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    required>
                                    <option value="">Pilih jenis hewan</option>
                                    <option value="Kucing" <?php echo $anabul['kategori_hewan'] === 'Kucing' ? 'selected' : ''; ?>>Kucing</option>
                                    <option value="Anjing" <?php echo $anabul['kategori_hewan'] === 'Anjing' ? 'selected' : ''; ?>>Anjing</option>
                                    <option value="Burung" <?php echo $anabul['kategori_hewan'] === 'Burung' ? 'selected' : ''; ?>>Burung</option>
                                    <option value="Hamster" <?php echo $anabul['kategori_hewan'] === 'Hamster' ? 'selected' : ''; ?>>Hamster</option>
                                    <option value="Kelinci" <?php echo $anabul['kategori_hewan'] === 'Kelinci' ? 'selected' : ''; ?>>Kelinci</option>
                                    <option value="Ikan" <?php echo $anabul['kategori_hewan'] === 'Ikan' ? 'selected' : ''; ?>>Ikan</option>
                                    <option value="Reptil" <?php echo $anabul['kategori_hewan'] === 'Reptil' ? 'selected' : ''; ?>>Reptil</option>
                                    <option value="Lainnya" <?php echo $anabul['kategori_hewan'] === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>

                            <!-- Jenis Ras/Spesies -->
                            <div>
                                <label for="jenis_ras" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jenis Ras/Spesies Spesifik
                                </label>
                                <input type="text" id="jenis_ras" name="jenis_ras"
                                    value="<?php echo htmlspecialchars($anabul['jenis_ras'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Masukkan ras/spesies">
                            </div>

                            <!-- Umur -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="umur_tahun"
                                        class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                                    <div class="flex">
                                        <input type="number" id="umur_tahun" name="umur_tahun" min="0" max="50"
                                            value="<?php echo $anabul['umur_tahun'] ?? ''; ?>"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="0">
                                        <span
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-xs text-gray-600 flex items-center">Tahun</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                                    <div class="flex">
                                        <input type="number" id="umur_bulan" name="umur_bulan" min="0" max="11"
                                            value="<?php echo $anabul['umur_bulan'] ?? ''; ?>"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="0">
                                        <span
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-xs text-gray-600 flex items-center">Bulan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Berat -->
                            <div>
                                <label for="berat" class="block text-sm font-medium text-gray-700 mb-1">Berat</label>
                                <div class="flex">
                                    <input type="number" id="berat" name="berat" step="0.1" min="0"
                                        value="<?php echo $anabul['berat'] ?? ''; ?>"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                        placeholder="0">
                                    <span
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-xs text-gray-600 flex items-center">Kg</span>
                                </div>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                                    Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    <option value="">Pilih kelamin</option>
                                    <option value="Jantan" <?php echo $anabul['jenis_kelamin'] === 'Jantan' ? 'selected' : ''; ?>>Jantan</option>
                                    <option value="Betina" <?php echo $anabul['jenis_kelamin'] === 'Betina' ? 'selected' : ''; ?>>Betina</option>
                                </select>
                            </div>

                            <!-- Riwayat Kesehatan -->
                            <div class="md:col-span-2">
                                <label for="riwayat_kesehatan"
                                    class="block text-sm font-medium text-gray-700 mb-1">Riwayat Kesehatan</label>
                                <textarea id="riwayat_kesehatan" name="riwayat_kesehatan" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Contoh: vaksinasi, alergi, atau penyakit"><?php echo htmlspecialchars($anabul['riwayat_kesehatan'] ?? ''); ?></textarea>
                            </div>

                            <!-- Tanda atau Karakteristik -->
                            <div class="md:col-span-2">
                                <label for="karakteristik" class="block text-sm font-medium text-gray-700 mb-1">Tanda
                                    atau Karakteristik Unik Hewan Peliharaan</label>
                                <textarea id="karakteristik" name="karakteristik" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Contoh: bulu berwarna, tanda lahir, atau disabilitas"><?php echo htmlspecialchars($anabul['karakteristik'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 mt-6">
                            <a href="profil_anabul.php"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
        // Global variables
        let selectedFiles = [];
        const maxFiles = 4;
        const maxFileSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Helper functions
        function getExistingPhotoCount() {
            return document.querySelectorAll('.grid > div:not(:last-child) img').length;
        }

        function getPreviewPhotoCount() {
            return document.querySelectorAll('#preview-grid .relative').length;
        }

        function getTotalPhotoCount() {
            return getExistingPhotoCount() + getPreviewPhotoCount();
        }

        function updatePhotoCounters() {
            const existingPhotos = getExistingPhotoCount();
            const previewPhotos = getPreviewPhotoCount();
            const totalPhotos = existingPhotos + previewPhotos;

            document.getElementById('photo-counter').textContent = `${previewPhotos} foto dipilih`;
            document.getElementById('remaining-slots').textContent = `${maxFiles - totalPhotos} slot tersisa`;
        }

        function validateFiles(files) {
            const errors = [];

            // Check total count
            if (getTotalPhotoCount() + files.length > maxFiles) {
                errors.push(`Maksimal ${maxFiles} foto yang dapat diunggah. Anda sudah memiliki ${getExistingPhotoCount()} foto dan ${getPreviewPhotoCount()} foto yang akan diupload, serta mencoba menambahkan ${files.length} foto lagi.`);
            }

            // Validate each file
            Array.from(files).forEach((file, index) => {
                if (!allowedTypes.includes(file.type)) {
                    errors.push(`File "${file.name}" tidak didukung. Gunakan JPG, JPEG, atau PNG.`);
                }
                if (file.size > maxFileSize) {
                    errors.push(`File "${file.name}" terlalu besar. Maksimal 5MB per file.`);
                }
            });

            return errors;
        }

        function createPreviewItem(file, index) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'relative';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-48 object-cover rounded-lg shadow-md';
                    img.alt = `Foto ${index + 1}`;

                    const badge = document.createElement('div');
                    badge.className = 'absolute top-2 right-2';
                    badge.innerHTML = `
                        <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                            Foto ${getExistingPhotoCount() + getPreviewPhotoCount() + index + 1}
                        </span>
                    `;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function () {
                        const fileIndex = selectedFiles.indexOf(file);
                        if (fileIndex > -1) {
                            selectedFiles.splice(fileIndex, 1);
                        }
                        previewItem.remove();
                        updatePhotoCounters();
                    };

                    previewItem.appendChild(img);
                    previewItem.appendChild(badge);
                    previewItem.appendChild(removeBtn);
                    resolve(previewItem);
                };
                reader.readAsDataURL(file);
            });
        }

        // Custom confirmation modal functions
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

        async function handleFileSelect(input) {
            if (input.files && input.files.length > 0) {
                const files = Array.from(input.files);
                const errors = validateFiles(files);

                if (errors.length > 0) {
                    showCustomConfirm(errors.join('\n'), (result) => {
                        if (result) {
                            input.value = '';
                        }
                    });
                    return;
                }

                // Add files to selectedFiles array
                selectedFiles.push(...files);

                // Show preview area
                document.getElementById('preview-area').classList.remove('hidden');

                // Update preview grid
                const previewGrid = document.getElementById('preview-grid');

                // Create previews
                for (let i = 0; i < files.length; i++) {
                    const previewItem = await createPreviewItem(files[i], i);
                    previewGrid.appendChild(previewItem);
                }

                updatePhotoCounters();

                // Clear the input to allow selecting the same file again
                input.value = '';
            }
        }

        function removeAllImages() {
            showCustomConfirm('Yakin ingin menghapus semua foto yang akan diupload?', (result) => {
                if (result) {
                    selectedFiles = [];
                    document.getElementById('preview-area').classList.add('hidden');
                    document.getElementById('preview-grid').innerHTML = '';
                    updatePhotoCounters();
                }
            });
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();

            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                const dataTransfer = new DataTransfer();
                files.forEach(file => dataTransfer.items.add(file));

                const input = document.getElementById('foto');
                input.files = dataTransfer.files;
                handleFileSelect(input);
            }

            e.target.classList.remove('border-orange-400');
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            e.target.classList.add('border-orange-400');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            e.target.classList.remove('border-orange-400');
        }

        function removeExistingPhoto(filename, button) {
            showCustomConfirm('Yakin ingin menghapus foto ini?', (result) => {
                if (result) {
                    const container = button.closest('.relative');
                    container.remove();

                    // Add hidden input to track deleted photos
                    const form = document.querySelector('form');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_photos[]';
                    input.value = filename;
                    form.appendChild(input);

                    updatePhotoCounters();
                }
            });
        }

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function (e) {
            if (selectedFiles.length > 0) {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));

                const input = document.getElementById('foto');
                input.files = dataTransfer.files;
            }
        });
    </script>
</body>

</html>