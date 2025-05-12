<?php
session_start();

require '../includes/db.php';

// Inisialisasi variabel agar tidak error notice
$show_success = false;

// cek form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if ($email) {
        // cek email di tabel pelanggan
        $query = "SELECT * FROM pelanggan WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $show_success = true;
        } else {
            $error_message = "Email tidak ditemukan!";
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
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- bagian kiri -->
        <div class="hidden md:flex md:w-1/2 bg-orange-400 flex-col items-center justify-center">
            <h1 class="text-white text-3xl font-bold mt-12 z-10">Selamat Datang di Ling-Ling Pet Shop</h1>
            <div class="relative z-10 mt-5">
                <img src="../aset/iconloginregis.png" alt="Person holding a cat" class="max-w-md">
            </div>
        </div>

        <!-- bagian kanan -->
        <div class="w-full md:w-1/2 p-6 flex flex-col justify-start mt-6 items-center">
            <div class="w-full max-w-md">
                <?php if ($show_success): ?>
                    <h2 class="text-2xl font-bold text-center mb-4 mt-6">Permintaan Atur Ulang Kata Sandi Berhasil!</h2>
                    <p class="text-center mb-6">Silakan cek email Anda untuk instruksi lebih lanjut tentang cara mengatur
                        ulang kata sandi Anda. Jika tidak menemukan email tersebut, periksa folder spam atau junk.</p>
                    <form action="login.php" method="get">
                        <button type="submit"
                            class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                            Kembali ke Halaman Login
                        </button>
                    </form>
                <?php else: ?>
                    <h2 class="text-2xl font-bold text-center mb-8">Lupa Password</h2>
                    <?php if (isset($error_message)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>
                    <p class="mb-4">Kehilangan password? Silakan masukkan alamat email Anda. Anda akan menerima tautan untuk
                        membuat kata
                        sandi baru melalui email.</p>
                    <form method="POST" action="" id="loginForm">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="text" id="email" name="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Masukkan alamat email anda" required>
                        </div>
                        <button type="submit" id="submitBtn"
                            class="w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                            Atur Ulang Kata Sandi
                        </button>
                    </form>

                    <div class="text-center mt-6 flex items-center justify-center gap-2">
                        <svg class="w-6 h-6 text-gray-800 dark:text-orange-600" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12l4-4m-4 4 4 4" />
                        </svg>
                        <a href="login.php" class="text-orange-500 font-medium hover:text-orange-600">Kembali ke Halaman
                            Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>