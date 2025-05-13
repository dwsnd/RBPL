<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Set pesan logout
    $_SESSION['logout_message'] = "Anda berhasil keluar dari sistem.";
}

// Hapus semua data session
$_SESSION = array();

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: ../public/index.php");
exit();
?>