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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['nama_hewan']) || empty($_POST['kategori_hewan'])) {
            throw new Exception('Nama hewan dan kategori hewan wajib diisi.');
        }

        // Handle photo upload (optional for edit)
        $foto_name = $anabul['foto']; // Keep existing photo by default

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['foto']['type'], $allowed_types)) {
                throw new Exception('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
            }

            if ($_FILES['foto']['size'] > $max_size) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
            }

            // Create upload directory if not exists
            $upload_dir = '../uploads/anabul/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate unique filename
            $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $new_foto_name = uniqid('anabul_') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_foto_name;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if (!empty($anabul['foto']) && file_exists($upload_dir . $anabul['foto'])) {
                    unlink($upload_dir . $anabul['foto']);
                }
                $foto_name = $new_foto_name;
            } else {
                throw new Exception('Gagal mengunggah foto baru.');
            }
        }

        // Prepare data for update
        $data = [
            'nama_hewan' => trim($_POST['nama_hewan']),
            'kategori_hewan' => $_POST['kategori_hewan'],
            'jenis_ras' => !empty($_POST['jenis_ras']) ? trim($_POST['jenis_ras']) : null,
            'umur_tahun' => !empty($_POST['umur_tahun']) ? (int) $_POST['umur_tahun'] : null,
            'umur_bulan' => !empty($_POST['umur_bulan']) ? (int) $_POST['umur_bulan'] : null,
            'berat' => !empty($_POST['berat']) ? (float) $_POST['berat'] : null,
            'jenis_kelamin' => !empty($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null,
            'riwayat_kesehatan' => !empty($_POST['riwayat_kesehatan']) ? trim($_POST['riwayat_kesehatan']) : null,
            'karakteristik' => !empty($_POST['karakteristik']) ? trim($_POST['karakteristik']) : null,
            'foto' => $foto_name,
            'updated_at' => date('Y-m-d H:i:s')
        ];

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
            foto = ?, 
            updated_at = ? 
            WHERE id_anabul = ? AND id_pelanggan = ?";

        $update_stmt = $pdo->prepare($update_query);
        $update_result = $update_stmt->execute([
            $data['nama_hewan'],
            $data['kategori_hewan'],
            $data['jenis_ras'],
            $data['umur_tahun'],
            $data['umur_bulan'],
            $data['berat'],
            $data['jenis_kelamin'],
            $data['riwayat_kesehatan'],
            $data['karakteristik'],
            $data['foto'],
            $data['updated_at'],
            $anabul_id,
            $pelanggan_id
        ]);

        if ($update_result) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Data anabul berhasil diperbarui!'
            ];
            header("Location: profil_anabul.php");
            exit();
        } else {
            throw new Exception('Gagal memperbarui data anabul.');
        }

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ling-Ling Pet Shop - Tambah Anabul</title>
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
            right: 8px;
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

        #preview-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-item,
        .add-more-item {
            flex: 0 0 calc(25% - 0.75rem);
            max-width: calc(25% - 0.75rem);
        }

        .add-more-item {
            height: 200px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .add-more-item:hover {
            border-color: #fb923c;
            background-color: #fef3f2;
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

        .file-name {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {

            .preview-item,
            .add-more-item {
                flex: 0 0 calc(50% - 0.5rem);
                max-width: calc(50% - 0.5rem);
            }
        }

        @media (max-width: 480px) {

            .preview-item,
            .add-more-item {
                flex: 0 0 100%;
                max-width: 100%;
            }
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Foto Hewan Peliharaan
                            </label>

                            <?php if (!empty($anabul['foto'])): ?>
                                        <div class="mb-4">
                                            <img src="../uploads/anabul/<?php echo htmlspecialchars($anabul['foto']); ?>"
                                                alt="Foto saat ini" class="max-w-xs rounded-lg shadow-md">
                                            <p class="text-sm text-gray-600 mt-2">Foto saat ini</p>
                                        </div>
                            <?php endif; ?>

                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors"
                                onclick="document.getElementById('foto').click()" ondrop="handleDrop(event)"
                                ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                                <div id="upload-area" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-1">Klik untuk mengganti foto</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG (max 5MB)</p>
                                </div>
                                <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                    onchange="previewImage(this)">
                                <div id="image-preview" class="hidden mt-4">
                                    <img id="preview-img" src="" alt="Preview"
                                        class="max-w-xs mx-auto rounded-lg shadow-md">
                                    <button type="button" onclick="removeImage()"
                                        class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
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

            <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            document.getElementById('preview-img').src = e.target.result;
                            document.getElementById('image-preview').classList.remove('hidden');
                            document.getElementById('upload-area').classList.add('hidden');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function removeImage() {
                    document.getElementById('foto').value = '';
                    document.getElementById('image-preview').classList.add('hidden');
                    document.getElementById('upload-area').classList.remove('hidden');
                }

                function handleDrop(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        document.getElementById('foto').files = files;
                        previewImage(document.getElementById('foto'));
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
            </script>
</body>

</html>