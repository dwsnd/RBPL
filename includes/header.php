<?php
// Only start session if we're in a page that needs authentication
$publicPages = ['index.php', 'shopawal.php', 'about.php', 'perawatan.php', 'penitipan.php', 'konsultasi.php'];
$currentPage = basename($_SERVER['PHP_SELF']);

// Include database connection
require_once __DIR__ . '/db.php';

// Check if database connection is successful
if (!isset($pdo) || !isset($conn)) {
    die("Database connection failed. Please check your configuration.");
}

// Start session if not in public page
if (!in_array($currentPage, $publicPages) && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// fungsi untuk memeriksa apakah halaman aktif
function isActive($page)
{
    global $currentPage;
    return ($currentPage == $page) ? 'text-orange-500 font-extrabold border-b-2 border-orange-500' : 'text-gray-700 hover:text-orange-300';
}

// fungsi untuk memeriksa apakah ada halaman dalam array yang aktif (untuk dropdown)
function isDropdownActive($pages)
{
    global $currentPage;
    foreach ($pages as $page) {
        if ($currentPage == $page)
            return true;
    }
    return false;
}

// halaman untuk dropdown layanan
$servicePages = ['perawatan.php', 'penitipan.php', 'konsultasi.php'];

// mendapatkan user data saat login
$userData = null;
if (isset($_SESSION['id_pelanggan'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$_SESSION['id_pelanggan']]);
        if ($stmt->rowCount() > 0) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Handle error silently or log it
        error_log("Error fetching user data: " . $e->getMessage());
    }
}

// Function to get correct photo path
function getPhotoPath($photoProfile)
{
    if (empty($photoProfile)) {
        return '';
    }

    // Remove any leading slashes
    $photoProfile = ltrim($photoProfile, '/');

    // Try different possible paths
    $possiblePaths = [
        'uploads/pelanggan/' . $photoProfile,
        '../uploads/pelanggan/' . $photoProfile,
        '../../uploads/pelanggan/' . $photoProfile,
        '/uploads/pelanggan/' . $photoProfile
    ];

    // Check each possible path
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    // If no path works, return empty string to trigger fallback
    return '';
}
?>

<nav class="bg-white sticky top-0 py-4 rounded-b-4xl shadow-xl z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <a class="flex items-center font-bold text-xl" href="index.php">
                <i class="fa-solid fa-paw text-orange-500 mr-2 text-2xl hover:text-3xl transition-all duration-300"></i>
                Ling-Ling Pet Shop
            </a>

            <button class="lg:hidden" id="mobile-menu-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-8" id="desktop-menu">
                <a class="font-semibold <?php echo isActive('index.php'); ?>" href="index.php">Beranda</a>

                <!-- dropdown menu -->
                <div class="relative group">
                    <div class="flex items-center">
                        <button
                            class="font-semibold flex items-center <?php echo isDropdownActive($servicePages) ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-700 hover:text-orange-300'; ?>">
                            Layanan
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <div class="absolute pt-2 hidden group-hover:block w-48 z-50">
                        <div class="bg-white rounded-lg shadow-lg py-2">
                            <a href="perawatan.php"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Perawatan</a>
                            <a href="penitipan.php"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Penitipan</a>
                            <a href="konsultasi.php"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Konsultasi</a>
                        </div>
                    </div>
                </div>

                <a class="font-semibold <?php echo isActive('shopawal.php'); ?>" href="shopawal.php">Toko</a>
                <a class="font-semibold <?php echo isActive('about.php'); ?>" href="about.php">Tentang Kami</a>
            </div>

            <!-- Mobile Menu -->
            <div class="lg:hidden hidden" id="mobile-menu">
                <div class="absolute top-full left-0 right-0 bg-white shadow-lg rounded-b-lg py-4 px-4 space-y-4">
                    <a class="block font-semibold <?php echo isActive('index.php'); ?>" href="index.php">Beranda</a>
                    <a class="block font-semibold <?php echo isActive('perawatan.php'); ?>"
                        href="perawatan.php">Perawatan</a>
                    <a class="block font-semibold <?php echo isActive('penitipan.php'); ?>"
                        href="penitipan.php">Penitipan</a>
                    <a class="block font-semibold <?php echo isActive('konsultasi.php'); ?>"
                        href="konsultasi.php">Konsultasi</a>
                    <a class="block font-semibold <?php echo isActive('shopawal.php'); ?>" href="shopawal.php">Toko</a>
                    <a class="block font-semibold <?php echo isActive('about.php'); ?>" href="about.php">Tentang
                        Kami</a>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-6">
                <?php if ($userData): ?>
                    <!-- favorite-->
                    <div class="relative">
                        <a href="favorit.php" class="text-gray-700 hover:text-orange-500">
                            <i class="fa-regular fa-heart text-2xl"></i>
                            <span
                                class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                                <?php echo isset($_SESSION['fav_count']) ? $_SESSION['fav_count'] : 0; ?>
                            </span>
                        </a>
                    </div>
                    <!-- keranjang -->
                    <div class="relative">
                        <a href="keranjang.php" class="text-gray-700 hover:text-orange-500">
                            <i class="fa-solid fa-cart-shopping text-2xl"></i>
                            <span
                                class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                                <?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- profil -->
                <?php if ($userData): ?>
                    <div class="relative">
                        <button id="profileMenuBtn"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-400 hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all overflow-hidden">
                            <?php if (empty($userData['foto_profil'])): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            <?php else: ?>
                                <?php $photoPath = getPhotoPath($userData['foto_profil']); ?>
                                <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profil"
                                    class="w-full h-full object-cover rounded-full"
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                <!-- Fallback icon jika gambar tidak dapat dimuat -->
                                <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="blue">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </button>

                        <!-- dropdown -->
                        <div id="profileDropdown"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl z-50 py-3 px-4 hidden"
                            style="min-width: 250px;">
                            <div class="text-xs text-gray-500 mb-2">Saat ini menggunakan</div>
                            <div class="flex items-center mb-3">
                                <?php if (empty($userData['foto_profil'])): ?>
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <?php $photoPath = getPhotoPath($userData['foto_profil']); ?>
                                    <div class="w-12 h-12 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profil"
                                            class="w-full h-full object-cover"
                                            style="width: 48px; height: 48px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                        <!-- Fallback untuk dropdown -->
                                        <div class="w-full h-full bg-gray-300 flex items-center justify-center"
                                            style="display: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow">
                                    <div class="font-bold text-base text-gray-900">
                                        <?php echo htmlspecialchars($userData['nama_lengkap']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($userData['email']); ?>
                                    </div>
                                </div>
                            </div>
                            <button onclick="window.location.href='../profilpelanggan/dakun.php'"
                                class="w-full text-left font-semibold py-2 px-1 rounded hover:bg-orange-50 mb-1">Profil
                                Akun</button>
                            <button onclick="window.location.href='../auth/logout.php'"
                                class="w-full text-left font-semibold py-2 px-1 rounded hover:bg-orange-50 text-red-500">Keluar</button>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../auth/login.php"
                        class="font-semibold px-4 py-2 border-2 border-orange-500 text-orange-500 rounded hover:bg-orange-500 hover:text-white transition-colors">Masuk</a>
                    <a href="../auth/registrasi.php"
                        class="font-semibold px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    // mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // profile dropdown toggle (klik, bukan hover)
    const profileMenuBtn = document.getElementById('profileMenuBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileMenuBtn && profileDropdown) {
        profileMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        // close dropdown on click outside
        document.addEventListener('click', function (e) {
            if (!profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
            }
        });

        profileDropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
</script>