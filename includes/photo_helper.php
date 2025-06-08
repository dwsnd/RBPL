<?php
class PhotoHelper
{

    public static function getPhotoPath($photoFilename, $context = 'header')
    {
        if (empty($photoFilename)) {
            return '';
        }

        // Normalize path
        $photoFilename = str_replace('\\', '/', $photoFilename);

        // Tentukan base paths berdasarkan context
        $basePaths = [];

        switch ($context) {
            case 'header':
                $basePaths = [
                    'uploads/pelanggan/',
                    '../uploads/pelanggan/',
                ];
                break;

            case 'profile_page':
                $basePaths = [
                    '../../uploads/pelanggan/',
                    '../uploads/pelanggan/',
                    'uploads/pelanggan/',
                ];
                break;

            default:
                $basePaths = [
                    'uploads/pelanggan/',
                    '../uploads/pelanggan/',
                    '../../uploads/pelanggan/',
                ];
        }

        // Cek setiap kemungkinan path
        foreach ($basePaths as $basePath) {
            $fullPath = $basePath . $photoFilename;
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $fullPath;

            if (file_exists($serverPath)) {
                return $fullPath;
            }
        }

        return '';
    }

    public static function debugPhotoPath($photoFilename, $context = '')
    {
        error_log("=== PHOTO DEBUG ($context) ===");
        error_log("Input filename: " . $photoFilename);
        error_log("Resolved path: " . self::getPhotoPath($photoFilename, $context));
        error_log("Document root: " . $_SERVER['DOCUMENT_ROOT']);

        // Test berbagai kemungkinan
        $testPaths = [
            'uploads/pelanggan/' . $photoFilename,
            '../uploads/pelanggan/' . $photoFilename,
            '../../uploads/pelanggan/' . $photoFilename,
        ];

        foreach ($testPaths as $path) {
            $exists = file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path);
            error_log("Test path: $path - " . ($exists ? 'EXISTS' : 'NOT FOUND'));
        }
    }

    public static function getAbsolutePhotoUrl($photoFilename)
    {
        if (empty($photoFilename))
            return '';

        // Coba berbagai kombinasi
        $possibleUrls = [
            '/uploads/pelanggan/' . $photoFilename,
            '/petshop/uploads/pelanggan/' . $photoFilename, // jika ada subfolder
            '/' . $photoFilename // jika sudah full path
        ];

        foreach ($possibleUrls as $url) {
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . $url;
            if (file_exists($serverPath)) {
                return 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
        }

        return '';
    }
}

// Helper functions for backward compatibility
function getPhotoPath($photoProfile)
{
    return PhotoHelper::getPhotoPath($photoProfile, 'header');
}

function getProfilePhotoPath($fotoProfilDb)
{
    return PhotoHelper::getPhotoPath($fotoProfilDb, 'profile_page');
}

function ensureConsistentPhotoSession($pelanggan_data)
{
    // Pastikan session foto_profil konsisten dengan database
    if (isset($pelanggan_data['foto_profil'])) {
        $_SESSION['foto_profil'] = $pelanggan_data['foto_profil'];
    }

    // Debug session
    error_log("Session foto_profil: " . ($_SESSION['foto_profil'] ?? 'NOT SET'));
    error_log("Database foto_profil: " . ($pelanggan_data['foto_profil'] ?? 'NOT SET'));
}

function forceReloadUserData($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$user_id]);
        $fresh_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fresh_data) {
            // Update session dengan data terbaru
            $_SESSION['foto_profil'] = $fresh_data['foto_profil'];
            return $fresh_data;
        }
    } catch (PDOException $e) {
        error_log("Error reloading user data: " . $e->getMessage());
    }

    return null;
}