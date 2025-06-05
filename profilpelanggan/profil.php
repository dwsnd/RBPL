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
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../includes/header.php'; ?>

    <?php
    // Ambil data pelanggan (untuk contoh, kita ambil ID 1)
    $pelanggan_id = 1; // Dalam aplikasi nyata, ini akan dari session
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pelanggan_id]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle upload foto profil
    if (isset($_POST['upload_foto'])) {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['foto_profil']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array(strtolower($filetype), $allowed)) {
                $new_filename = 'profil_' . $pelanggan_id . '_' . time() . '.' . $filetype;
                $upload_path = 'uploads/' . $new_filename;

                // Buat folder uploads jika belum ada
                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                    // Update database
                    $update_query = "UPDATE pelanggan SET foto_profil = ? WHERE id = ?";
                    $update_stmt = $pdo->prepare($update_query);
                    $update_stmt->execute([$new_filename, $pelanggan_id]);

                    // Refresh halaman
                    header("Location: profil.php");
                    exit;
                }
            }
        }
    }
    ?>

    <div class="flex min-h-screen bg-gray-50 p-4">
        <!-- Sidebar -->
        <div class="w-64 bg-white border border-grey-100 shadow-sm sidebar">
            <div class="p-4">
                <div class="space-y-2">
                    <div class="bg-orange-500 text-white px-3 py-2 rounded-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-user"></i>
                        <span>Detail Akun</span>
                    </div>
                    <div
                        class="text-gray-600 px-3 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Detail Aktivitas</span>
                    </div>
                    <div
                        class="text-gray-600 px-3 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-heart"></i>
                        <span>Favorite</span>
                    </div>
                    <div
                        class="text-gray-600 px-3 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </div>
                    <div
                        class="text-gray-600 px-3 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </div>
                    <div
                        class="text-gray-600 px-3 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-question-circle"></i>
                        <span>Hubungi Kami</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-10 pb-4">
            <div class="w-full bg-white rounded-lg shadow-md p-8 border border-grey-100 ">
                <!-- Profile Photo Section -->
                <div class="flex justify-center mb-6">
                    <div class="relative group">
                        <?php if ($pelanggan['foto_profil'] && file_exists('uploads/' . $pelanggan['foto_profil'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($pelanggan['foto_profil']); ?>" alt="Foto Profil"
                                class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                        <?php else: ?>
                            <div
                                class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200">
                                <i class="fas fa-user text-2xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                            onclick="document.getElementById('foto_input').click()">
                            <span class="text-white text-xs font-medium">Ubah Profil</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden file input -->
                <form method="POST" enctype="multipart/form-data" style="display: none;">
                    <input type="file" id="foto_input" name="foto_profil" accept="image/*"
                        onchange="this.form.submit()">
                    <input type="hidden" name="upload_foto" value="1">
                </form>

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
                    <a href="profiledit.php"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto submit form when file is selected
        document.getElementById('foto_input').addEventListener('change', function () {
            if (this.files.length > 0) {
                this.form.submit();
            }
        });
    </script>

    <!-- Footer -->
    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>