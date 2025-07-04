<?php
session_start();

require '../includes/db.php';

// Fungsi untuk generate token yang aman
function generateRememberToken()
{
    return bin2hex(random_bytes(32));
}

// Fungsi untuk set cookie remember me
function setRememberMeCookie($email, $token)
{
    $expires = time() + (12 * 60 * 60); // 12 jam
    setcookie('remember_email', $email, $expires, '/', '', true, true); // secure dan httponly
    setcookie('remember_token', $token, $expires, '/', '', true, true);
}

// Fungsi untuk clear cookie remember me
function clearRememberMeCookie()
{
    setcookie('remember_email', '', time() - 3600, '/');
    setcookie('remember_token', '', time() - 3600, '/');
}

// Auto-login berdasarkan cookie (jika user belum login)
if (!isset($_SESSION['id_pelanggan']) && isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_token'])) {
    $remember_email = $_COOKIE['remember_email'];
    $remember_token = $_COOKIE['remember_token'];

    // Cek apakah token valid di database
    $query = "SELECT * FROM pelanggan WHERE email = ? AND remember_token = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$remember_email, $remember_token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Auto-login berhasil
        $_SESSION['logged_in'] = true;
        $_SESSION['id_pelanggan'] = $result['id_pelanggan'];
        $_SESSION['nama'] = $result['nama_lengkap'];
        $_SESSION['email'] = $result['email'];
        $_SESSION['foto_profil'] = $result['foto_profil'];

        // Redirect ke dashboard
        header("Location: ../dashboard/index_pelanggan.php");
        exit();
    } else {
        // Token tidak valid, hapus cookie
        clearRememberMeCookie();
    }
}

// Checking login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_phone = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    if ($email_or_phone && $password) {
        // Mencari pelanggan berdasarkan email atau nomor telepon
        $query = "SELECT * FROM pelanggan WHERE email = :email_or_phone OR nomor_telepon = :email_or_phone";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email_or_phone' => $email_or_phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Verifikasi password dengan password_verify untuk hashed password
            if (password_verify($password, $result['password'])) {
                // Set session dengan nama kolom yang benar
                $_SESSION['logged_in'] = true;
                $_SESSION['id_pelanggan'] = $result['id_pelanggan'];
                $_SESSION['nama'] = $result['nama_lengkap'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['foto_profil'] = $result['foto_profil'];

                // Handle remember me
                if ($remember) {
                    $token = generateRememberToken();

                    // Update token di database
                    $update_query = "UPDATE pelanggan SET remember_token = ? WHERE id_pelanggan = ?";
                    $update_stmt = $pdo->prepare($update_query);
                    $update_stmt->execute([$token, $result['id_pelanggan']]);

                    // Set cookie
                    setRememberMeCookie($result['email'], $token);
                } else {
                    // Hapus token lama jika ada
                    $update_query = "UPDATE pelanggan SET remember_token = NULL WHERE id_pelanggan = ?";
                    $update_stmt = $pdo->prepare($update_query);
                    $update_stmt->execute([$result['id_pelanggan']]);

                    // Clear cookie
                    clearRememberMeCookie();
                }

                header("Location: ../dashboard/index_pelanggan.php");
                exit();
            } else {
                $error_message = "Password yang Anda masukkan salah!";
            }
        } else {
            $error_message = "Email atau nomor telepon tidak ditemukan!";
        }
    } else {
        $error_message = "Email/nomor telepon dan password harus diisi!";
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
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease-in-out;
        }

        .popup-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <?php
    if (isset($_SESSION['registered']) && $_SESSION['registered'] === true) {
        echo '
        <div id="popupNotification" class="popup-notification">Akun berhasil dibuat! Silakan login.</div>

        <script>
            window.addEventListener("DOMContentLoaded", function() {
                const popup = document.getElementById("popupNotification");
                popup.classList.add("show");
                
                setTimeout(() => {
                    popup.classList.remove("show");
                }, 3000);
            });
        </script>';

        unset($_SESSION['registered']);
    }

    // Popup untuk logout berhasil
    if (isset($_SESSION['logout_message'])) {
        echo '
        <div id="popupNotification" class="popup-notification">' . $_SESSION['logout_message'] . '</div>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                const popup = document.getElementById("popupNotification");
                popup.classList.add("show");
                
                setTimeout(() => {
                    popup.classList.remove("show");
                }, 3000);
            });
        </script>';

        unset($_SESSION['logout_message']);
    }
    ?>

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
                <h2 class="text-2xl font-bold text-center mb-6">Masuk</h2>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm" class="space-y-4">
                    <div>
                        <label for="email" class="block text-base font-medium text-gray-700 mb-1">Alamat Email atau
                            Nomor
                            Telepon</label>
                        <input type="text" id="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                            placeholder="Masukkan email atau nomor telepon anda"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>

                    <div>
                        <label for="password" class="block text-base font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                                placeholder="Masukkan password anda" required>
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="eyeIcon" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Tetap ingat saya</label>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Masuk
                    </button>
                </form>

                <div class="text-right mt-2">
                    <a href="lupapassword.php" class="text-sm text-gray-600 hover:text-orange-500">Lupa password?</a>
                </div>

                <div class="text-center mt-6">
                    <p class="text-base text-gray-600">
                        Belum punya akun?
                        <a href="registrasi.php"
                            class="text-base text-orange-500 font-medium hover:text-orange-600">Daftar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Auto-fill email dan centang checkbox jika ada cookie remember me
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const rememberCheckbox = document.getElementById('remember');

            // Cek apakah ada cookie remember me
            const hasRememberCookie = document.cookie.includes('remember_email=');

            if (hasRememberCookie) {
                // Ambil email dari cookie
                const cookies = document.cookie.split(';');
                for (let cookie of cookies) {
                    const [name, value] = cookie.trim().split('=');
                    if (name === 'remember_email') {
                        emailInput.value = decodeURIComponent(value);
                        rememberCheckbox.checked = true;
                        break;
                    }
                }
            }
        });
    </script>
</body>

</html>