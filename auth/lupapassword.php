<?php
session_start();
require '../includes/db.php';

$show_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Validasi input kosong
    if (empty($email)) {
        $error_message = "Email harus diisi!";
    }
    // Validasi email format
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        try {
            $query = "SELECT * FROM pelanggan WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                // Generate token reset password
                $reset_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Hapus token lama untuk email ini jika ada
                $delete_old_token = "DELETE FROM password_resets WHERE email = :email";
                $stmt_delete = $pdo->prepare($delete_old_token);
                $stmt_delete->execute(['email' => $email]);

                // Simpan token baru ke database
                $insert_token = "INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (:email, :token, :expires_at, NOW())";
                $stmt_token = $pdo->prepare($insert_token);
                $token_saved = $stmt_token->execute([
                    'email' => $email,
                    'token' => $reset_token,
                    'expires_at' => $expires_at
                ]);

                // Debug: cek apakah token berhasil disimpan
                if (!$token_saved) {
                    $error_message = "Gagal menyimpan token reset password.";
                } else {
                    // Kirim email reset password
                    $reset_link = "http://localhost/your-project/auth/reset_password.php?token=" . $reset_token;

                    // Konfigurasi email
                    $to = $email;
                    $subject = "Reset Password - Ling-Ling Pet Shop";
                    $message = "
                <html>
                <head>
                    <title>Reset Password</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #fb923c; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { 
                            display: inline-block; 
                            background-color: #fb923c; 
                            color: white; 
                            padding: 12px 24px; 
                            text-decoration: none; 
                            border-radius: 5px; 
                            margin: 20px 0;
                        }
                        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Ling-Ling Pet Shop</h1>
                        </div>
                        <div class='content'>
                            <h2>Reset Password</h2>
                            <p>Halo,</p>
                            <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah ini untuk membuat password baru:</p>
                            <p style='text-align: center;'>
                                <a href='$reset_link' class='button'>Reset Password</a>
                            </p>
                            <p>Atau copy dan paste link berikut ke browser Anda:</p>
                            <p style='word-break: break-all; background-color: #e5e5e5; padding: 10px; border-radius: 3px;'>$reset_link</p>
                            <p><strong>Link ini akan kedaluwarsa dalam 1 jam.</strong></p>
                            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                        </div>
                        <div class='footer'>
                            <p>© 2024 Ling-Ling Pet Shop. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                    // Headers untuk HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: noreply@linglingpetshop.com" . "\r\n";
                    $headers .= "Reply-To: support@linglingpetshop.com" . "\r\n";

                    // Kirim email
                    if (mail($to, $subject, $message, $headers)) {
                        $show_success = true;
                    } else {
                        $error_message = "Gagal mengirim email. Silakan coba lagi.";
                    }
                }

            } else {
                $error_message = "Email tidak ditemukan!";
            }
        } catch (PDOException $e) {
            $error_message = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

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

        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .popup-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <!-- Add popup notification div -->
    <div id="popupNotification" class="popup-notification"></div>

    <div class="flex min-h-screen">
        <!-- bagian kiri -->
        <div class="hidden md:flex md:w-1/2 bg-orange-400 flex-col items-center justify-center">
            <h1 class="text-white text-2xl font-bold text-center pt-10">Selamat Datang di Ling-Ling Pet Shop</h1>
            <a href="../public/index.php">
                <div class="relative mt-16">
                    <img src="../aset/iconloginregis.png" alt="Person holding a cat" class="max-w-sm">
                </div>
            </a>
        </div>

        <!-- bagian kanan -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <?php if ($show_success): ?>
                    <!-- Tampilan sukses -->
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Email Terkirim!</h2>
                            <p class="text-gray-600 text-base">
                                Kami telah mengirimkan instruksi reset password ke email Anda.
                                Silakan periksa kotak masuk dan folder spam Anda.
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Link reset akan kedaluwarsa dalam 1 jam.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <a href="login.php"
                                class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 block text-center">
                                Kembali ke Login
                            </a>

                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                <button type="submit"
                                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                                    Kirim Ulang Email
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Form lupa password -->
                    <div class="text-center mb-6">
                        <i class="fas fa-key text-orange-400 text-4xl mb-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Lupa Password</h2>
                        <p class="text-gray-600 text-base">Masukkan email Anda untuk mendapatkan link reset password</p>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                showPopup('<?= htmlspecialchars($error_message) ?>', 'error');
                            });
                        </script>
                    <?php endif; ?>

                    <form method="POST" action="" id="forgotPasswordForm" class="space-y-4">
                        <div>
                            <label for="email" class="block text-base font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-1"></i> Alamat Email
                            </label>
                            <input type="email" id="email" name="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-300 text-sm"
                                placeholder="Masukkan email yang terdaftar"
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-orange-400 hover:bg-orange-500 text-white text-base font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Link Reset Password
                        </button>
                    </form>

                    <div class="text-center space-y-3 mt-6">
                        <div class="text-sm text-gray-600">
                            <a href="login.php"
                                class="text-base text-orange-500 font-medium hover:text-orange-600 transition duration-300">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Function to show popup notification
        function showPopup(message, type = 'success') {
            const popup = document.getElementById('popupNotification');
            popup.textContent = message;
            popup.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            popup.classList.add('show');

            // Hide popup after 3 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        // Client-side validation
        document.getElementById('forgotPasswordForm')?.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');

            // Validasi email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid!');
                return;
            }

            // Disable button dan tampilkan loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

            // Re-enable button setelah 3 detik jika ada error
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password';
            }, 3000);
        });

        // Auto focus pada input email
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        });
    </script>
</body>

</html>