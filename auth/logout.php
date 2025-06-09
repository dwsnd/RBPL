<?php
session_start();

// Hapus semua session variables
$_SESSION = array();

// Hapus cookie jika ada (remember me cookie)
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/"); // Set expired time
}

// Hapus session cookie
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

// Destroy session
session_destroy();

// Set logout message untuk ditampilkan di halaman login
session_start();
$_SESSION['logout_message'] = "Anda berhasil logout!";

// Redirect ke halaman login
header("Location: login.php");
exit();
?>