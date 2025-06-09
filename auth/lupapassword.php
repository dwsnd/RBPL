<?php
session_start();
require '../includes/db.php';

// Inisialisasi variabel agar tidak error notice
$show_success = false;

// cek form
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
            // cek email di tabel pelanggan menggunakan PDO
            $query = "SELECT * FROM pelanggan WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                $show_success = true;
                // Di sini Anda bisa menambahkan kode untuk mengirim email reset password
                // misalnya menggunakan PHPMailer atau fungsi mail() PHP

                // Generate token reset password (opsional)
                $reset_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Simpan token ke database (Anda perlu membuat tabel password_resets)
                /*
                $insert_token = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
                $stmt_token = $pdo->prepare($insert_token);
                $stmt_token->execute([
                    'email' => $email,
                    'token' => $reset_token,
                    'expires_at' => $expires_at
                ]);
                */

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
    <title>Lupa Password - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- bagian kiri -->
        <div class="hidden md:flex md:w-1/2 bg-orange-400 flex-col items-center justify-center">
            <h1 class="text-white text-3xl font-bold text-center pt-10">Selamat Datang di Ling-Ling Pet Shop</h1>
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
                            <p class="text-gray-600 text-sm">
                                Kami telah mengirimkan instruksi reset password ke email Anda.
                                Silakan periksa kotak masuk dan folder spam Anda.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <a href="login.php"
                                class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 block text-center">
                                Kembali ke Login
                            </a>

                            <button onclick="resendEmail()"
                                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                                Kirim Ulang Email
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Form lupa password -->
                    <div class="text-center mb-6">
                        <i class="fas fa-key text-orange-400 text-4xl mb-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Lupa Password</h2>
                        <p class="text-gray-600 text-sm">Masukkan email Anda untuk mendapatkan link reset password</p>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <?= htmlspecialchars($error_message) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="forgotPasswordForm" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-1"></i> Alamat Email
                            </label>
                            <input type="email" id="email" name="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-300 text-sm"
                                placeholder="Masukkan email yang terdaftar"
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Link Reset Password
                        </button>
                    </form>

                    <div class="text-center space-y-3 mt-6">
                        <div class="text-sm text-gray-600">
                            <a href="login.php"
                                class="text-orange-500 font-medium hover:text-orange-600 transition duration-300">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                            </a>
                        </div>

                        <div class="text-xs text-gray-500">
                            Belum punya akun?
                            <a href="registrasi.php"
                                class="text-orange-500 font-medium hover:text-orange-600 transition duration-300">
                                Daftar di sini
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
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

        // Function untuk resend email
        function resendEmail() {
            const button = event.target;
            const originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

            // Simulasi pengiriman ulang email
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
                alert('Email telah dikirim ulang!');
            }, 2000);
        }

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