<?php
// Only start session if we're in a page that needs authentication
$publicPages = ['index.php', 'shopawal.php', 'about.php', 'perawatan.php', 'penitipan.php', 'konsultasi.php'];
$currentPage = basename($_SERVER['PHP_SELF']);

if (!in_array($currentPage, $publicPages) && session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

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
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = '" . $_SESSION['id_pelanggan'] . "'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
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

            <div class="hidden lg:flex items-center space-x-8">
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
                <a class="font-semibold <?php echo isActive('about.php'); ?>" href="about.php">Tentang
                    Kami</a>
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
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-400 hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all">
                            <?php if (empty($userData['foto_profil'])): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                    width="24" height="24" viewBox="0 0 256 256" xml:space="preserve">
                                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;"
                                        transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                        <path
                                            d="M 85.091 90 h -8.372 c 0 -17.49 -14.229 -31.719 -31.719 -31.719 S 13.281 72.51 13.281 90 H 4.909 c 0 -22.107 17.985 -40.091 40.091 -40.091 S 85.091 67.893 85.091 90 z"
                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255, 255, 255); fill-rule: nonzero; opacity: 1;"
                                            transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                        <path
                                            d="M 45 46.484 c -12.816 0 -23.242 -10.426 -23.242 -23.242 S 32.184 0 45 0 s 23.242 10.426 23.242 23.242 S 57.816 46.484 45 46.484 z M 45 8.372 c -8.199 0 -14.87 6.67 -14.87 14.87 s 6.67 14.87 14.87 14.87 s 14.87 -6.67 14.87 -14.87 S 53.199 8.372 45 8.372 z"
                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255, 255, 255); fill-rule: nonzero; opacity: 1;"
                                            transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                    </g>
                                </svg>
                            <?php else: ?>
                                <img src="uploads/pelanggan/<?php echo htmlspecialchars($userData['foto_profil']); ?>"
                                    alt="Profil" class="w-8 h-8 rounded-full object-cover" />
                            <?php endif; ?>
                        </button>
                        <!-- dropdown -->
                        <div id="profileDropdown"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl z-50 py-3 px-4 hidden"
                            style="min-width: 250px;">
                            <div class="text-xs text-gray-500 mb-2">Saat ini menggunakan</div>
                            <div class="flex items-center mb-3">
                                <?php if (empty($userData['foto_profil'])): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        version="1.1" width="40" height="40" viewBox="0 0 256 256" xml:space="preserve">
                                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;"
                                            transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                            <path
                                                d="M 53.026 45.823 c 3.572 -2.527 5.916 -6.682 5.916 -11.381 C 58.941 26.754 52.688 20.5 45 20.5 s -13.942 6.254 -13.942 13.942 c 0 4.699 2.344 8.854 5.916 11.381 C 28.172 49.092 21.883 57.575 21.883 67.5 c 0 1.104 0.896 2 2 2 s 2 -0.896 2 -2 c 0 -10.541 8.576 -19.116 19.117 -19.116 S 64.116 56.959 64.116 67.5 c 0 1.104 0.896 2 2 2 s 2 -0.896 2 -2 C 68.116 57.575 61.827 49.092 53.026 45.823 z M 35.058 34.442 c 0 -5.482 4.46 -9.942 9.942 -9.942 c 5.481 0 9.941 4.46 9.941 9.942 s -4.46 9.942 -9.941 9.942 C 39.518 44.384 35.058 39.924 35.058 34.442 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 45 0 C 20.187 0 0 20.187 0 45 c 0 24.813 20.187 45 45 45 c 24.813 0 45 -20.187 45 -45 C 90 20.187 69.813 0 45 0 z M 45 86 C 22.393 86 4 67.607 4 45 S 22.393 4 45 4 s 41 18.393 41 41 S 67.607 86 45 86 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                        </g>
                                    </svg>
                                <?php else: ?>
                                    <img src="uploads/pelanggan/<?php echo htmlspecialchars($userData['foto_profil']); ?>"
                                        alt="Profil" class="w-12 h-12 rounded-full object-cover mr-3" />
                                <?php endif; ?>
                                <div class="ml-3">
                                    <div class="font-bold text-base text-gray-900">
                                        <?php echo htmlspecialchars($userData['nama_lengkap']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($userData['email']); ?>
                                    </div>
                                </div>
                            </div>
                            <button onclick="window.location.href='../dashboard/profile.php'"
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
    const mobileMenu = document.querySelector('.lg\\:flex');
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

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