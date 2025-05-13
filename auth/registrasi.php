<?php
session_start();

require '../includes/db.php';

// checking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasipassword'];
    $nama_lengkap = $_POST['namalengkap'];
    $nomor_telepon = $_POST['nomortelepon'];

    // validasi password
    if ($password !== $konfirmasi_password) {
        $error_message = "Password dan konfirmasi password tidak sama!";
    } else if ($email && $password && $nama_lengkap && $nomor_telepon) {
        // hashing password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // memasukkan data ke db
        $query = "INSERT INTO pelanggan (email, password, nama_lengkap, nomor_telepon) 
                 VALUES ('$email', '$hashed_password', '$nama_lengkap', '$nomor_telepon')";

        if ($conn->query($query) === TRUE) {
            // set session
            $_SESSION['registered'] = true;
            $_SESSION['email'] = $email;

            header("Location: login.php");
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    } else {
        $error_message = "Semua field harus diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- bagian kiri -->
        <div class="hidden md:flex md:w-1/2 bg-orange-400 flex-col items-center justify-center">
            <h1 class="text-white text-3xl font-bold mt-12 z-10">Selamat Datang di Ling-Ling Pet Shop</h1>
            <a href="../public/index.php">
                <div class="relative z-10 mt-5">
                    <img src="../aset/iconloginregis.png" alt="Person holding a cat" class="max-w-md">
                </div>
            </a>
        </div>

        <!-- bagian kanan -->
        <div class="w-full md:w-1/2 p-6 flex flex-col justify-start mt-6 items-center">
            <div class="w-full max-w-md">
                <h2 class="text-2xl font-bold text-center mb-8">Daftar</h2>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="text" id="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Masukkan email anda" required>
                    </div>

                    <div class="mb-4">
                        <label for="namalengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama
                            Lengkap</label>
                        <input type="text" id="namalengkap" name="namalengkap"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Masukkan nama lengkap anda" required>
                    </div>

                    <div class="mb-4">
                        <label for="nomortelepon" class="block text-sm font-medium text-gray-700 mb-1">Nomor
                            Telepon</label>
                        <input type="text" id="nomortelepon" name="nomortelepon"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Masukkan nomor telepon anda" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Masukkan password anda" required>
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="passwordIcon" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="konfirmasipassword" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi
                            Password</label>
                        <div class="relative">
                            <input type="password" id="konfirmasipassword" name="konfirmasipassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Masukkan konfirmasi password anda" required>
                            <button type="button" onclick="togglePassword('konfirmasipassword')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="konfirmasipasswordIcon"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Daftar
                    </button>
                </form>

                <div class="text-center mt-8">
                    <p class="text-base text-gray-600">
                        Sudah punya akun?
                        <a href="login.php" class="text-orange-500 font-medium hover:text-orange-600">Masuk</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + 'Icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>

</html>