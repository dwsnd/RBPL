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

    // Ambil data pelanggan
    $pelanggan_id = 1; // Dalam aplikasi nyata, ini akan dari session
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pelanggan_id]);
    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    $message = '';
    $error = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_lengkap = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);
        $nomor_telepon = trim($_POST['nomor_telepon']);
        $alamat = trim($_POST['alamat']);
        $rt = trim($_POST['rt']);
        $rw = trim($_POST['rw']);
        $desa = trim($_POST['desa']);
        $kode_pos = trim($_POST['kode_pos']);
        $kelurahan = trim($_POST['kelurahan']);
        $kecamatan = trim($_POST['kecamatan']);
        $kabupaten = trim($_POST['kabupaten']);
        $provinsi = trim($_POST['provinsi']);

        // Validasi input
        if (empty($nama_lengkap)) {
            $error = 'Nama lengkap harus diisi!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid!';
        } elseif (empty($nomor_telepon)) {
            $error = 'Nomor telepon harus diisi!';
        } else {
            // Cek apakah email sudah digunakan oleh user lain
            $check_email = "SELECT id_pelanggan FROM pelanggan WHERE email = ? AND id_pelanggan != ?";
            $check_stmt = $pdo->prepare($check_email);
            $check_stmt->execute([$email, $pelanggan_id]);

            if ($check_stmt->fetch()) {
                $error = 'Email sudah digunakan oleh pengguna lain!';
            } else {
                // Handle file upload
                $foto_profil = $pelanggan['foto_profil']; // Keep existing photo by default
                if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png'];
                    $filename = $_FILES['foto_profil']['name'];
                    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

                    if (in_array(strtolower($filetype), $allowed)) {
                        $new_filename = uniqid() . '.' . $filetype;
                        $upload_path = 'uploads/' . $new_filename;

                        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                            // Delete old photo if exists
                            if ($pelanggan['foto_profil'] && file_exists('uploads/' . $pelanggan['foto_profil'])) {
                                unlink('uploads/' . $pelanggan['foto_profil']);
                            }
                            $foto_profil = $new_filename;
                        } else {
                            $error = 'Gagal mengupload foto profil!';
                        }
                    } else {
                        $error = 'Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.';
                    }
                }

                if (empty($error)) {
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

                    try {
                        // Update data
                        $update_query = "UPDATE pelanggan SET 
                            nama_lengkap = ?, 
                            email = ?, 
                            nomor_telepon = ?, 
                            alamat = ?,
                            foto_profil = ?
                            WHERE id_pelanggan = ?";

                        $update_stmt = $pdo->prepare($update_query);

                        if (
                            $update_stmt->execute([
                                $nama_lengkap,
                                $email,
                                $nomor_telepon,
                                $alamat_lengkap,
                                $foto_profil,
                                $pelanggan_id
                            ])
                        ) {
                            $message = 'Profil berhasil diperbarui!';
                            // Refresh data pelanggan
                            $stmt->execute([$pelanggan_id]);
                            $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
                        } else {
                            $error = 'Gagal memperbarui profil!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan: ' . $e->getMessage();
                    }
                }
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Profil Pelanggan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>

    <body class="bg-gray-50">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <div class="w-64 bg-white shadow-lg">
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="bg-orange-500 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                            <i class="fas fa-user"></i>
                            <span>Detail Akun</span>
                        </div>
                        <div
                            class="text-gray-600 px-4 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Detail Aktivitas</span>
                        </div>
                        <div
                            class="text-gray-600 px-4 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer">
                            <i class="fas fa-heart"></i>
                            <span>Favorite</span>
                        </div>
                        <div
                            class="text-gray-600 px-4 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer">
                            <i class="fas fa-bell"></i>
                            <span>Notifikasi</span>
                        </div>
                        <div
                            class="text-gray-600 px-4 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </div>
                        <div
                            class="text-gray-600 px-4 py-2 flex items-center space-x-2 hover:bg-gray-100 rounded-lg cursor-pointer">
                            <i class="fas fa-question-circle"></i>
                            <span>Hubungi Kami</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-8">
                <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Edit Profil</h2>
                        <a href="profil.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-times text-xl"></i>
                        </a>
                    </div>

                    <!-- Messages -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Photo Section -->
                    <div class="flex justify-center mb-8">
                        <?php if ($pelanggan['foto_profil'] && file_exists('uploads/' . $pelanggan['foto_profil'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($pelanggan['foto_profil']); ?>" alt="Foto Profil"
                                class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                        <?php else: ?>
                            <div
                                class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                                <i class="fas fa-user text-3xl text-gray-600"></i>
                            </div>
                        <?php endif; ?>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Foto Profil</label>
                            <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/jpg"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, JPEG, PNG</p>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form method="POST" class="space-y-6" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap"
                                    value="<?php echo htmlspecialchars($pelanggan['nama_lengkap'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    required>
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="relative">
                                    <input type="email" name="email"
                                        value="<?php echo htmlspecialchars($pelanggan['email'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pr-10"
                                        required>
                                    <i
                                        class="fas fa-check-circle text-green-500 absolute right-3 top-1/2 transform -translate-y-1/2"></i>
                                </div>
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" name="nomor_telepon"
                                    value="<?php echo htmlspecialchars($pelanggan['nomor_telepon'] ?? ''); ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Alamat Lengkap</h3>

                            <!-- Alamat Utama -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                                    <input type="text" name="rt"
                                        value="<?php echo htmlspecialchars($pelanggan['rt'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="001">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                                    <input type="text" name="rw"
                                        value="<?php echo htmlspecialchars($pelanggan['rw'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="002">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                                    <input type="text" name="desa"
                                        value="<?php echo htmlspecialchars($pelanggan['desa'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama Desa">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                                    <input type="text" name="kode_pos"
                                        value="<?php echo htmlspecialchars($pelanggan['kode_pos'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="12345">
                                </div>
                            </div>

                            <!-- Kelurahan, Kecamatan -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelurahan</label>
                                    <input type="text" name="kelurahan"
                                        value="<?php echo htmlspecialchars($pelanggan['kelurahan'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama Kelurahan">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                                    <input type="text" name="kecamatan"
                                        value="<?php echo htmlspecialchars($pelanggan['kecamatan'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama Kecamatan">
                                </div>
                            </div>

                            <!-- Kabupaten, Provinsi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten</label>
                                    <input type="text" name="kabupaten"
                                        value="<?php echo htmlspecialchars($pelanggan['kabupaten'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama Kabupaten">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                                    <input type="text" name="provinsi"
                                        value="<?php echo htmlspecialchars($pelanggan['provinsi'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama Provinsi">
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
                            <a href="profil.php"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require '../includes/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>