<?php
require_once '../includes/db.php';

// Cek data yang ada di database
$query = "SELECT * FROM admin WHERE email = 'admin@admin.com'";
$result = $conn->query($query);
$admin = $result->fetch_assoc();

echo "Data sebelum update:<br>";
echo "Email: " . $admin['email'] . "<br>";
echo "Password (dari DB): " . $admin['password'] . "<br><br>";

// Password yang ingin di-hash
$password = "admin123";
$email = "admin@admin.com";

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update password di database
$query = "UPDATE admin SET password = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "Password berhasil diupdate!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password (plain): " . $password . "<br>";
    echo "Password (hashed): " . $hashed_password . "<br><br>";

    // Verifikasi password
    echo "Verifikasi password:<br>";
    if (password_verify($password, $hashed_password)) {
        echo "Password verification: SUCCESS<br>";
    } else {
        echo "Password verification: FAILED<br>";
    }
} else {
    echo "Error updating password: " . $conn->error;
}
?>