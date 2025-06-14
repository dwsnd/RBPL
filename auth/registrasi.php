<?php
session_start();

require '../includes/db.php';

// Checking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasipassword'];
    $nama_lengkap = $_POST['namalengkap'];
    $nomor_telepon = $_POST['nomortelepon'];

    // validasi input kosong
    if (empty($email) || empty($password) || empty($nama_lengkap) || empty($nomor_telepon)) {
        $error_message = "Semua field harus diisi!";
    }
    // validasi email format
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    }
    // validasi password
    else if ($password !== $konfirmasi_password) {
        $error_message = "Password dan konfirmasi password tidak sama!";
    }
    // validasi panjang password
    else if (strlen($password) < 6) {
        $error_message = "Password minimal 6 karakter!";
    } else {
        try {
            // cek apakah email sudah terdaftar
            $checkEmail = "SELECT * FROM pelanggan WHERE email = :email";
            $stmt = $pdo->prepare($checkEmail);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                $error_message = "Email sudah terdaftar! Silakan gunakan email lain.";
            } else {
                // cek apakah nomor telepon sudah terdaftar
                $checkPhone = "SELECT * FROM pelanggan WHERE nomor_telepon = :nomor_telepon";
                $stmt = $pdo->prepare($checkPhone);
                $stmt->execute(['nomor_telepon' => $nomor_telepon]);

                if ($stmt->rowCount() > 0) {
                    $error_message = "Nomor telepon sudah terdaftar! Silakan gunakan nomor lain.";
                } else {
                    // hashing password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // memasukkan data ke db menggunakan prepared statement
                    $query = "INSERT INTO pelanggan (email, nama_lengkap, nomor_telepon, password, alamat) 
                             VALUES (:email, :nama_lengkap, :nomor_telepon, :password, :alamat)";
                    $stmt = $pdo->prepare($query);

                    if (
                        $stmt->execute([
                            'email' => $email,
                            'nama_lengkap' => $nama_lengkap,
                            'nomor_telepon' => $nomor_telepon,
                            'password' => $hashed_password,
                            'alamat' => ''
                        ])
                    ) {
                        $_SESSION['registered'] = true;

                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
                    }
                }
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

        /* Popup notification styles */
        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
                <h2 class="text-2xl font-bold text-center mb-6">Daftar</h2>

                <?php if (isset($error_message)): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            showPopup('<?= htmlspecialchars($error_message) ?>', 'error');
                        });
                    </script>
                <?php endif; ?>

                <form method="POST" action="" id="registrationForm" class="space-y-4">
                    <div>
                        <label for="email" class="block text-base font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                            placeholder="Masukkan email anda" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                            title="Email harus mengandung @ dan domain yang valid"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>

                    <div>
                        <label for="namalengkap" class="block text-base font-medium text-gray-700 mb-1">Nama
                            Lengkap</label>
                        <input type="text" id="namalengkap" name="namalengkap"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                            placeholder="Masukkan nama lengkap anda"
                            value="<?= isset($_POST['namalengkap']) ? htmlspecialchars($_POST['namalengkap']) : '' ?>"
                            required>
                    </div>

                    <div>
                        <label for="nomortelepon" class="block text-base font-medium text-gray-700 mb-1">Nomor
                            Telepon</label>
                        <input type="tel" id="nomortelepon" name="nomortelepon"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                            placeholder="Masukkan nomor telepon anda" pattern="[0-9]*"
                            title="Nomor telepon hanya boleh berisi angka"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="<?= isset($_POST['nomortelepon']) ? htmlspecialchars($_POST['nomortelepon']) : '' ?>"
                            required>
                    </div>

                    <div>
                        <label for="password" class="block text-base font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
                                placeholder="Masukkan password anda (minimal 6 karakter)" required>
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
                        <div class="text-xs text-gray-500 mt-1">Password minimal 6 karakter</div>
                    </div>

                    <div>
                        <label for="konfirmasipassword"
                            class="block text-base font-medium text-gray-700 mb-1">Konfirmasi
                            Password</label>
                        <div class="relative">
                            <input type="password" id="konfirmasipassword" name="konfirmasipassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 text-sm"
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
                        class="text-base w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Daftar
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-base text-gray-600">
                        Sudah punya akun?
                        <a href="login.php"
                            class="text-base text-orange-500 font-medium hover:text-orange-600">Masuk</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div id="customConfirmModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
            <div class="text-xl font-bold text-gray-900 mb-4 text-center" id="confirmMessage"></div>
            <div class="flex justify-end space-x-3">
                <button id="confirmCancelBtn"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-full hover:bg-gray-100 transition-colors text-sm">
                    Batal
                </button>
                <button id="confirmOKBtn"
                    class="px-6 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors text-sm">
                    OK
                </button>
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

        // Custom confirmation modal functions
        function showCustomConfirm(message, callback) {
            const modal = document.getElementById('customConfirmModal');
            const messageEl = document.getElementById('confirmMessage');
            const cancelBtn = document.getElementById('confirmCancelBtn');
            const okBtn = document.getElementById('confirmOKBtn');

            messageEl.textContent = message;
            modal.classList.remove('hidden');

            const handleConfirm = () => {
                modal.classList.add('hidden');
                callback(true);
                cleanup();
            };

            const handleCancel = () => {
                modal.classList.add('hidden');
                callback(false);
                cleanup();
            };

            const cleanup = () => {
                okBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            okBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);
        }

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

        // Client-side validation
        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('konfirmasipassword').value;
            const email = document.getElementById('email').value;
            const phoneNumber = document.getElementById('nomortelepon').value;

            // Validasi email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showCustomConfirm('Format email tidak valid! Email harus mengandung @ dan domain yang valid', (result) => {
                    if (result) {
                        document.getElementById('email').focus();
                    }
                });
                return;
            }

            // Validasi nomor telepon
            const phoneRegex = /^[0-9]+$/;
            if (!phoneRegex.test(phoneNumber)) {
                e.preventDefault();
                showCustomConfirm('Nomor telepon hanya boleh berisi angka!', (result) => {
                    if (result) {
                        document.getElementById('nomortelepon').focus();
                    }
                });
                return;
            }

            // Validasi password
            if (password.length < 6) {
                e.preventDefault();
                showCustomConfirm('Password minimal 6 karakter!', (result) => {
                    if (result) {
                        document.getElementById('password').focus();
                    }
                });
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                showCustomConfirm('Password dan konfirmasi password tidak sama!', (result) => {
                    if (result) {
                        document.getElementById('konfirmasipassword').focus();
                    }
                });
                return;
            }
        });
    </script>
</body>

</html>