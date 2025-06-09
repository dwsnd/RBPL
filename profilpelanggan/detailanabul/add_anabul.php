<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location:../auth/login.php");
    exit();
}

// Database connection
require_once '../../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

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
                    <h1 class="text-xl font-bold text-gray-900">Tambah Data Anabul</h1>
                </div>

                <!-- Form -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <form action="process_anabul.php" method="POST" enctype="multipart/form-data" id="anabulForm">
                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih file atau klik untuk unggah (Maksimal 4 foto) <span class="text-red-500">*</span>
                            </label>

                            <!-- Upload Zone -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center upload-zone"
                                id="upload-zone" onclick="document.getElementById('foto').click()"
                                ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                                ondragleave="handleDragLeave(event)">
                                <div id="upload-area">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-1">Klik untuk memilih file atau drag & drop</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG (max 5MB per file)</p>
                                    <p class="text-xs text-gray-500">Maksimal 4 foto</p>
                                </div>
                                <input type="file" id="foto" name="foto[]" accept="image/jpeg,image/jpg,image/png"
                                    class="hidden" onchange="handleFileSelect(this)" multiple required>
                            </div>

                            <!-- Preview Area -->
                            <div id="image-preview" class="hidden mt-4">
                                <div class="flex justify-between items-center mb-3">
                                    <span id="photo-counter" class="text-sm text-gray-600 font-medium">0/4 foto
                                        dipilih</span>
                                    <button type="button" onclick="removeAllImages()"
                                        class="text-red-500 hover:text-red-700 text-sm px-3 py-1 rounded border border-red-200 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash mr-1"></i> Hapus Semua
                                    </button>
                                </div>
                                <div id="preview-grid">
                                    <!-- Preview images will be inserted here -->
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
                                    <option value="Kucing">Kucing</option>
                                    <option value="Anjing">Anjing</option>
                                    <option value="Hamster">Hamster</option>
                                    <option value="Kelinci">Kelinci</option>
                                </select>
                            </div>

                            <!-- Jenis Ras/Spesies -->
                            <div>
                                <label for="jenis_ras" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jenis Ras/Spesies Spesifik
                                </label>
                                <input type="text" id="jenis_ras" name="jenis_ras"
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
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="0" pattern="[0-9]*" inputmode="numeric"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                        <span
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-xs text-gray-600 flex items-center">Tahun</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                                    <div class="flex">
                                        <input type="number" id="umur_bulan" name="umur_bulan" min="0" max="11"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                            placeholder="0" pattern="[0-9]*" inputmode="numeric"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
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
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                        placeholder="0" pattern="[0-9]*\.?[0-9]*" inputmode="decimal"
                                        onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46">
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
                                    <option value="Jantan">Jantan</option>
                                    <option value="Betina">Betina</option>
                                </select>
                            </div>

                            <!-- Riwayat Kesehatan -->
                            <div class="md:col-span-2">
                                <label for="riwayat_kesehatan"
                                    class="block text-sm font-medium text-gray-700 mb-1">Riwayat Kesehatan</label>
                                <textarea id="riwayat_kesehatan" name="riwayat_kesehatan" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Contoh: vaksinasi, alergi, atau penyakit yang pernah dialami"></textarea>
                            </div>

                            <!-- Tanda atau Karakteristik -->
                            <div class="md:col-span-2">
                                <label for="karakteristik" class="block text-sm font-medium text-gray-700 mb-1">Tanda
                                    atau Karakteristik Unik Hewan Peliharaan</label>
                                <textarea id="karakteristik" name="karakteristik" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    placeholder="Contoh: bulu berwarna belang, tanda lahir, atau keunikan lainnya"></textarea>
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

    <!-- Footer -->
    <?php require '../../includes/footer.php'; ?>

    <script>
        // Global variables
        let selectedFiles = [];
        const maxFiles = 4;
        const maxFileSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Handle file selection from input
        function handleFileSelect(input) {
            const newFiles = Array.from(input.files);
            addFilesToSelection(newFiles);
        }

        // Add files to selection (maintaining existing files)
        function addFilesToSelection(newFiles) {
            if (!newFiles || newFiles.length === 0) return;

            // Check if adding new files would exceed limit
            if (selectedFiles.length + newFiles.length > maxFiles) {
                showAlert(`Maksimal ${maxFiles} foto. Saat ini sudah ada ${selectedFiles.length} foto.`, 'error');
                return;
            }

            const validFiles = [];

            for (let file of newFiles) {
                // Validate file type
                if (!allowedTypes.includes(file.type)) {
                    showAlert(`File ${file.name} bukan format yang didukung. Gunakan JPG, JPEG, atau PNG.`, 'error');
                    continue;
                }

                // Validate file size
                if (file.size > maxFileSize) {
                    showAlert(`File ${file.name} terlalu besar. Maksimal 5MB per file.`, 'error');
                    continue;
                }

                // Check for duplicate files (by name and size)
                const isDuplicate = selectedFiles.some(existingFile =>
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (isDuplicate) {
                    showAlert(`File ${file.name} sudah dipilih sebelumnya.`, 'warning');
                    continue;
                }

                validFiles.push(file);
            }

            // Add valid files to selection
            selectedFiles = [...selectedFiles, ...validFiles];
            updateFileInput();
            displayPreviews();

            if (validFiles.length > 0) {
                showAlert(`Berhasil menambahkan ${validFiles.length} foto`, 'success');
            }
        }

        // Display image previews
        function displayPreviews() {
            const previewGrid = document.getElementById('preview-grid');
            const uploadArea = document.getElementById('upload-area');
            const imagePreview = document.getElementById('image-preview');
            const photoCounter = document.getElementById('photo-counter');
            const uploadZone = document.getElementById('upload-zone'); // Get the upload zone element

            // Clear existing previews
            previewGrid.innerHTML = '';

            if (selectedFiles.length > 0) {
                // Update counter
                photoCounter.textContent = `${selectedFiles.length}/${maxFiles} foto dipilih`;

                // Hide the main upload zone and show preview area
                uploadZone.classList.add('hidden'); // Hide the upload zone
                imagePreview.classList.remove('hidden');

                // Create preview for each file
                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'preview-item';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <button type="button" class="remove-btn" onclick="removeImage(${index})" title="Hapus foto">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="file-name">${file.name}</div>
                        `;
                        previewGrid.appendChild(previewDiv);
                    }
                    reader.readAsDataURL(file);
                });

                // Add "Add More" button if there's space
                if (selectedFiles.length < maxFiles) {
                    const addMoreDiv = document.createElement('div');
                    addMoreDiv.className = 'add-more-item';
                    addMoreDiv.onclick = () => document.getElementById('foto').click();
                    addMoreDiv.innerHTML = `
                        <i class="fas fa-plus text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Tambah foto</p>
                        <p class="text-xs text-gray-500">${maxFiles - selectedFiles.length} slot tersisa</p>
                    `;
                    previewGrid.appendChild(addMoreDiv);
                }
            } else {
                // Reset to initial state
                imagePreview.classList.add('hidden');
                uploadZone.classList.remove('hidden');
                uploadArea.innerHTML = `
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 mb-1">Klik untuk memilih file atau drag & drop</p>
                    <p class="text-xs text-gray-500">Format: JPG, PNG (max 5MB per file)</p>
                    <p class="text-xs text-gray-500">Maksimal ${maxFiles} foto</p>
                `;
                photoCounter.textContent = `0/${maxFiles} foto dipilih`;
            }
        }

        // Remove single image
        function removeImage(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            displayPreviews();
            showAlert('Berhasil menghapus foto', 'info');
        }

        // Remove all images
        function removeAllImages() {
            if (selectedFiles.length === 0) return;

            showCustomConfirm('Yakin ingin menghapus semua foto?', function (result) {
                if (result) {
                    selectedFiles = [];
                    updateFileInput();
                    displayPreviews();
                    showAlert('Berhasil menghapus semua foto', 'info');
                }
            });
        }

        // Update file input with current selection
        function updateFileInput() {
            const input = document.getElementById('foto');
            const dt = new DataTransfer();

            selectedFiles.forEach(file => {
                dt.items.add(file);
            });

            input.files = dt.files;
        }

        // Drag and drop handlers
        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();

            const uploadZone = document.getElementById('upload-zone');
            uploadZone.classList.remove('drag-over');

            const files = Array.from(e.dataTransfer.files).filter(file =>
                allowedTypes.includes(file.type)
            );

            if (files.length > 0) {
                addFilesToSelection(files);
            }
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('upload-zone').classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('upload-zone').classList.remove('drag-over');
        }

        // Show alert messages
        function showAlert(message, type = 'info') {
            // Create alert element
            const alertDiv = document.createElement('div');
            const alertClass = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            };

            alertDiv.className = `popup-notification ${alertClass[type]}`;
            alertDiv.innerHTML = `
                <div class="flex justify-between items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold">×</button>
                </div>
            `;

            document.body.appendChild(alertDiv);

            // Trigger animation
            setTimeout(() => {
                alertDiv.classList.add('show');
            }, 10);

            // Auto remove after 3 seconds
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => {
                    alertDiv.remove();
                }, 300);
            }, 3000);
        }

        // Custom confirmation dialog
        function showCustomConfirm(message, callback) {
            const modal = document.getElementById('customConfirmModal');
            const confirmMessage = document.getElementById('confirmMessage');
            const confirmOKBtn = document.getElementById('confirmOKBtn');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');

            confirmMessage.textContent = message;
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

            // Optional: Close modal if clicking outside (consider user experience)
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    handleCancel(); // Treat outside click as a cancel
                }
            });
        }

        // Form validation before submit
        document.getElementById('anabulForm').addEventListener('submit', function (e) {
            if (selectedFiles.length === 0) {
                e.preventDefault();
                showAlert('Silakan pilih minimal 1 foto untuk diunggah', 'error');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;

            // Re-enable button after a delay in case of validation errors
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            displayPreviews();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
</body>

</html>