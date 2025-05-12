<?php
// Dapatkan nama file halaman saat ini
$currentPage = basename($_SERVER['PHP_SELF']);

// Fungsi untuk memeriksa apakah halaman aktif
function isActive($page)
{
    global $currentPage;
    return ($currentPage == $page) ? 'text-orange-500 font-extrabold border-b-2 border-orange-500' : 'text-gray-700 hover:text-orange-300';
}

// Fungsi untuk memeriksa apakah ada halaman dalam array yang aktif (untuk dropdown)
function isDropdownActive($pages)
{
    global $currentPage;
    foreach ($pages as $page) {
        if ($currentPage == $page)
            return true;
    }
    return false;
}

// Halaman-halaman untuk dropdown layanan
$servicePages = ['perawatan.php', 'penitipan.php', 'konsultasi.php'];
?>

<nav class="bg-white sticky top-0 py-4 rounded-b-4xl shadow-xl z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <a class="flex items-center font-bold text-xl" href="index.php">
                <i class="fa-solid fa-paw text-orange-500 mr-2 text-2xl hover:text-3xl transition-all duration-300"></i>
                Ling-Ling Pet Shop
            </a>

            <!-- Mobile menu button -->
            <button class="lg:hidden" id="mobile-menu-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

            <!-- Desktop menu -->
            <div class="hidden lg:flex items-center space-x-8">
                <a class="font-semibold <?php echo isActive('index.php'); ?>" href="index.php">Beranda</a>

                <!-- Dropdown menu -->
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
                    <!-- Add padding to the top of the dropdown to prevent gap -->
                    <div class="absolute pt-2 hidden group-hover:block w-48 z-50">
                        <div class="bg-white rounded-lg shadow-lg py-2">
                            <a href="perawatan.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Perawatan</a>
                            <a href="penitipan.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Penitipan</a>
                            <a href="konsultasi.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Konsultasi</a>
                        </div>
                    </div>
                </div>

                <a class="font-semibold <?php echo isActive('shopawal.php'); ?>" href="shopawal.php">Toko</a>
                <a class="font-semibold <?php echo isActive('aboutawal.php'); ?>" href="aboutawal.php">Tentang
                    Kami</a>
            </div>

            <!-- Auth buttons -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="hidden lg:block relative group">
                    <button class="flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-full hover:bg-gray-100">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'aset/images/default-avatar.png'); ?>"
                            alt="Profil" class="w-8 h-8 rounded-full object-cover">
                        <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden group-hover:block w-48 bg-white rounded-lg shadow-lg py-2 mt-2">
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="admin/index.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Dasbor
                                Admin</a>
                        <?php elseif ($_SESSION['role'] == 'dokter'): ?>
                            <a href="dokter/index.php" class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Dasbor
                                Dokter</a>
                        <?php else: ?>
                            <a href="dashboard/index.php"
                                class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Dasbor</a>
                        <?php endif; ?>

                        <a href="dashboard/profile.php"
                            class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Profil Saya</a>

                        <?php if ($_SESSION['role'] == 'user'): ?>
                            <a href="dashboard/orders.php"
                                class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Pesanan Saya</a>
                            <a href="dashboard/appointments.php"
                                class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Jadwal Konsultasi</a>
                        <?php endif; ?>

                        <div class="border-t border-gray-200 my-2"></div>
                        <a href="auth/logout.php"
                            class="block px-4 py-2 hover:bg-orange-50 hover:text-orange-500">Keluar</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="hidden lg:flex items-center space-x-4">
                    <a href="auth/login.php"
                        class="font-semibold px-4 py-2 border-2 border-orange-500 text-orange-500 rounded hover:bg-orange-500 hover:text-white transition-colors">Masuk</a>
                    <a href="auth/register.php"
                        class="font-semibold px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Daftar</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.querySelector('.lg\\:flex');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>