<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: ../../auth/login.php");
    exit();
}

// Database connection
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pelanggan_id = $_SESSION['id_pelanggan'];

        // Validate required fields
        if (empty($_POST['nama_hewan']) || empty($_POST['kategori_hewan'])) {
            throw new Exception('Nama hewan dan kategori hewan wajib diisi.');
        }

        // Validate and process multiple file uploads
        $uploaded_files = [];

        if (isset($_FILES['foto']) && is_array($_FILES['foto']['name']) && !empty($_FILES['foto']['name'][0])) {
            $file_count = count($_FILES['foto']['name']);

            // Validate file count
            if ($file_count > 4) {
                throw new Exception('Maksimal 4 foto yang dapat diunggah.');
            }

            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $allowed_mime_types = ['image/jpeg', 'image/png'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Create upload directory if not exists
            $upload_dir = '../../uploads/anabul/';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    throw new Exception('Gagal membuat direktori upload.');
                }
            }

            // Process each uploaded file
            for ($i = 0; $i < $file_count; $i++) {
                // Skip if no file or error occurred
                if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($_FILES['foto']['error'][$i] !== UPLOAD_ERR_OK) {
                    $error_messages = [
                        UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi batas server)',
                        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar',
                        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                        UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary tidak ditemukan',
                        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file',
                        UPLOAD_ERR_EXTENSION => 'Upload dibatalkan oleh ekstensi'
                    ];

                    $error_msg = isset($error_messages[$_FILES['foto']['error'][$i]])
                        ? $error_messages[$_FILES['foto']['error'][$i]]
                        : 'Error tidak dikenal';

                    throw new Exception("Error upload file " . $_FILES['foto']['name'][$i] . ": " . $error_msg);
                }

                $file_name = $_FILES['foto']['name'][$i];
                $file_tmp = $_FILES['foto']['tmp_name'][$i];
                $file_size = $_FILES['foto']['size'][$i];

                // Validate file size
                if ($file_size > $max_size) {
                    throw new Exception("File {$file_name} terlalu besar. Maksimal 5MB per file.");
                }

                // Validate file type using multiple methods
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                    throw new Exception("File {$file_name} bukan format yang didukung. Gunakan JPG, JPEG, atau PNG.");
                }

                // Validate MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file_tmp);
                finfo_close($finfo);

                if (!in_array($mime_type, $allowed_mime_types)) {
                    throw new Exception("File {$file_name} bukan format gambar yang valid.");
                }

                // Additional validation: check if it's actually an image
                $image_info = getimagesize($file_tmp);
                if ($image_info === false) {
                    throw new Exception("File {$file_name} bukan file gambar yang valid.");
                }

                // Generate unique filename to prevent conflicts
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $unique_name = 'anabul_' . $pelanggan_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $unique_name;

                // Move uploaded file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $uploaded_files[] = $unique_name;

                    // Optional: Create thumbnail or compress image here if needed
                    // compressImage($upload_path, $upload_path, 80);

                } else {
                    throw new Exception("Gagal mengunggah file: {$file_name}");
                }
            }

            // Check if at least one file was uploaded successfully
            if (empty($uploaded_files)) {
                throw new Exception('Tidak ada foto yang berhasil diunggah. Silakan coba lagi.');
            }

        } else {
            throw new Exception('Minimal 1 foto hewan wajib diunggah.');
        }

        // Prepare data for insertion
        $data = [
            'id_pelanggan' => $pelanggan_id,
            'nama_hewan' => trim($_POST['nama_hewan']),
            'kategori_hewan' => $_POST['kategori_hewan'],
            'jenis_ras' => !empty($_POST['jenis_ras']) ? trim($_POST['jenis_ras']) : null,
            'umur_tahun' => !empty($_POST['umur_tahun']) ? (int) $_POST['umur_tahun'] : null,
            'umur_bulan' => !empty($_POST['umur_bulan']) ? (int) $_POST['umur_bulan'] : null,
            'berat' => !empty($_POST['berat']) ? (float) $_POST['berat'] : null,
            'jenis_kelamin' => !empty($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null,
            'riwayat_kesehatan' => !empty($_POST['riwayat_kesehatan']) ? trim($_POST['riwayat_kesehatan']) : null,
            'karakteristik' => !empty($_POST['karakteristik']) ? trim($_POST['karakteristik']) : null,
            'foto_utama' => $uploaded_files[0] // Set first image as main photo
        ];

        // Validate age input
        if (!empty($data['umur_tahun']) && ($data['umur_tahun'] < 0 || $data['umur_tahun'] > 50)) {
            throw new Exception('Umur tahun harus antara 0-50 tahun.');
        }

        if (!empty($data['umur_bulan']) && ($data['umur_bulan'] < 0 || $data['umur_bulan'] > 11)) {
            throw new Exception('Umur bulan harus antara 0-11 bulan.');
        }

        // Validate weight
        if (!empty($data['berat']) && ($data['berat'] < 0 || $data['berat'] > 200)) {
            throw new Exception('Berat hewan tidak valid.');
        }

        // Begin transaction for data consistency
        $pdo->beginTransaction();

        try {
            // Insert main anabul record
            $sql = "INSERT INTO anabul (
                id_pelanggan, 
                nama_hewan, 
                kategori_hewan, 
                jenis_ras, 
                umur_tahun, 
                umur_bulan, 
                berat, 
                jenis_kelamin, 
                riwayat_kesehatan, 
                karakteristik, 
                foto_utama
            ) VALUES (
                :id_pelanggan, 
                :nama_hewan, 
                :kategori_hewan, 
                :jenis_ras, 
                :umur_tahun, 
                :umur_bulan, 
                :berat, 
                :jenis_kelamin, 
                :riwayat_kesehatan, 
                :karakteristik, 
                :foto_utama
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_pelanggan' => $data['id_pelanggan'],
                ':nama_hewan' => $data['nama_hewan'],
                ':kategori_hewan' => $data['kategori_hewan'],
                ':jenis_ras' => $data['jenis_ras'],
                ':umur_tahun' => $data['umur_tahun'],
                ':umur_bulan' => $data['umur_bulan'],
                ':berat' => $data['berat'],
                ':jenis_kelamin' => $data['jenis_kelamin'],
                ':riwayat_kesehatan' => $data['riwayat_kesehatan'],
                ':karakteristik' => $data['karakteristik'],
                ':foto_utama' => $data['foto_utama']
            ]);

            // Get the inserted anabul ID
            $anabul_id = $pdo->lastInsertId();

            // Insert multiple photos into anabul_foto table
            $foto_sql = "INSERT INTO anabul_foto (id_anabul, nama_file, urutan) VALUES (?, ?, ?)";
            $foto_stmt = $pdo->prepare($foto_sql);

            foreach ($uploaded_files as $index => $filename) {
                $foto_stmt->execute([
                    $anabul_id,
                    $filename,
                    $index + 1 // Order starts from 1
                ]);
            }

            // Commit transaction
            $pdo->commit();

            // Set success message
            $_SESSION['success_message'] = "Data hewan peliharaan '{$data['nama_hewan']}' berhasil ditambahkan dengan " . count($uploaded_files) . " foto.";

            // Log success activity (optional)
            error_log("Anabul added successfully: ID {$anabul_id}, User: {$pelanggan_id}, Files: " . implode(', ', $uploaded_files));

            // Redirect to profile page
            header("Location: profil_anabul.php");
            exit();

        } catch (Exception $e) {
            // Rollback transaction
            $pdo->rollBack();

            // Clean up uploaded files if database insertion failed
            foreach ($uploaded_files as $filename) {
                $file_path = $upload_dir . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            throw new Exception("Gagal menyimpan data ke database: " . $e->getMessage());
        }

    } catch (Exception $e) {
        // Log error
        error_log("Anabul creation error: " . $e->getMessage() . " - User: " . ($_SESSION['id_pelanggan'] ?? 'unknown'));

        // Set error message
        $_SESSION['error_message'] = $e->getMessage();

        // Redirect back to form
        header("Location: tambah_anabul.php");
        exit();
    }

} else {
    // If not POST request, redirect to form
    header("Location: tambah_anabul.php");
    exit();
}

/**
 * Optional function to compress images
 * Uncomment and modify as needed
 */
/*
function compressImage($source, $destination, $quality) {
    $info = getimagesize($source);
    
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false;
    }
    
    // Save compressed image
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        // Convert PNG to JPEG for compression
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagejpeg($bg, $destination, $quality);
        imagedestroy($bg);
    }
    
    imagedestroy($image);
    return true;
}
*/
?>