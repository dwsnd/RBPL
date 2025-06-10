<?php
// FILE: debug_photo.php - Tambahkan ini sebagai file terpisah untuk debug

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/db.php';

echo "<h2>DEBUG FOTO PROFIL</h2>";

// 1. Check session
echo "<h3>1. CECK SESSION:</h3>";
if (isset($_SESSION['id_pelanggan'])) {
    echo "✅ Session ID Pelanggan: " . $_SESSION['id_pelanggan'] . "<br>";
} else {
    echo "❌ Session tidak ada<br>";
    exit();
}

// 2. Check database connection
echo "<h3>2. CHECK DATABASE:</h3>";
if (isset($pdo)) {
    echo "✅ PDO Connection: OK<br>";
} else {
    echo "❌ PDO Connection: GAGAL<br>";
    exit();
}

// 3. Check user data in database
echo "<h3>3. CHECK DATA USER DI DATABASE:</h3>";
try {
    $stmt = $pdo->prepare("SELECT id_pelanggan, nama_lengkap, email, foto_profil FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->execute([$_SESSION['id_pelanggan']]);

    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ User ditemukan:<br>";
        echo "- ID: " . $userData['id_pelanggan'] . "<br>";
        echo "- Nama: " . $userData['nama_lengkap'] . "<br>";
        echo "- Email: " . $userData['email'] . "<br>";
        echo "- Foto Profil: " . ($userData['foto_profil'] ? $userData['foto_profil'] : 'KOSONG') . "<br>";

        if ($userData['foto_profil']) {
            echo "<h3>4. CHECK FILE FOTO:</h3>";

            // Test different possible paths
            $photoFile = $userData['foto_profil'];
            echo "Nama file foto: " . $photoFile . "<br><br>";

            $possiblePaths = [
                'uploads/pelanggan/' . $photoFile,
                '../uploads/pelanggan/' . $photoFile,
                '../../uploads/pelanggan/' . $photoFile,
                '/uploads/pelanggan/' . $photoFile,
                $_SERVER['DOCUMENT_ROOT'] . '/uploads/pelanggan/' . $photoFile,
                $_SERVER['DOCUMENT_ROOT'] . '/RBPL/REBEL1/uploads/pelanggan/' . $photoFile,
                __DIR__ . '/uploads/pelanggan/' . $photoFile,
                __DIR__ . '/../uploads/pelanggan/' . $photoFile,
                __DIR__ . '/../../uploads/pelanggan/' . $photoFile
            ];

            echo "Mencari file di path berikut:<br>";
            $foundPath = null;

            foreach ($possiblePaths as $path) {
                echo "- Testing: " . $path;
                if (file_exists($path)) {
                    echo " ✅ DITEMUKAN!<br>";
                    $foundPath = $path;
                    break;
                } else {
                    echo " ❌ Tidak ada<br>";
                }
            }

            if ($foundPath) {
                echo "<br><h3>5. TAMPILKAN FOTO:</h3>";
                echo "Path yang benar: " . $foundPath . "<br>";

                // Convert to web-accessible path
                $webPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $foundPath);
                $webPath = str_replace('\\', '/', $webPath); // Windows compatibility
                if (substr($webPath, 0, 1) !== '/') {
                    $webPath = '/' . $webPath;
                }

                echo "Web path: " . $webPath . "<br><br>";
                echo "Foto preview:<br>";
                echo '<img src="' . $webPath . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
                echo '<div style="display: none; width: 100px; height: 100px; background: #ccc; border-radius: 50%; text-align: center; line-height: 100px;">No Image</div>';
            } else {
                echo "<br>❌ File foto tidak ditemukan di semua lokasi yang dicoba!<br>";
                echo "<br><h3>INFORMASI SISTEM:</h3>";
                echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
                echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
                echo "Current Directory: " . __DIR__ . "<br>";
                echo "Current Working Directory: " . getcwd() . "<br>";
            }
        } else {
            echo "<h3>4. FOTO PROFIL:</h3>";
            echo "❌ Field foto_profil kosong di database<br>";
        }

    } else {
        echo "❌ User tidak ditemukan di database<br>";
    }

} catch (PDOException $e) {
    echo "❌ Error query database: " . $e->getMessage() . "<br>";
}

echo "<br><h3>6. STRUKTUR DIREKTORI:</h3>";
echo "Silakan cek apakah folder-folder ini ada:<br>";
echo "- uploads/<br>";
echo "- uploads/pelanggan/<br>";
echo "- dan pastikan file foto ada di dalamnya<br>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }

    h2,
    h3 {
        color: #333;
        border-bottom: 2px solid #orange;
        padding-bottom: 5px;
    }
</style>