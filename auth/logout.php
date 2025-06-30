<?php
session_start();

require '../includes/db.php';

// Hapus token remember me dari database jika user sedang login
if (isset($_SESSION['id_pelanggan'])) {
    $update_query = "UPDATE pelanggan SET remember_token = NULL WHERE id_pelanggan = ?";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([$_SESSION['id_pelanggan']]);
}

// Hapus semua session variables
$_SESSION = array();

// Hapus cookie remember me yang baru
if (isset($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Hapus cookie lama (untuk backward compatibility)
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
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

// Set pesan logout
session_start();
$_SESSION['logout_message'] = 'Anda berhasil logout!';

// Redirect ke halaman login
header("Location: login.php");
exit();
?>