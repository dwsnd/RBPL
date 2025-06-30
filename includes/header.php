<?php
// Only start session if we're in a page that needs authentication
$publicPages = ['index.php', 'shop.php', 'about.php', 'perawatan.php', 'penitipan.php', 'konsultasi.php', 'detail_produk.php'];
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

// Function to get correct path based on login status
function getCorrectPath($page)
{
    if (isset($_SESSION['id_pelanggan'])) {
        $currentPath = $_SERVER['REQUEST_URI'];
        $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
        $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
            strpos($currentPath, '/dashboard/penitipan/') !== false ||
            strpos($currentPath, '/dashboard/konsultasi/') !== false ||
            strpos($currentPath, '/dashboard/shop/') !== false;
        $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;
        $profilPelangganDepth = 0;
        if ($isInProfilPelanggan) {
            $afterProfil = explode('/profilpelanggan/', $currentPath)[1] ?? '';
            $profilPelangganDepth = substr_count($afterProfil, '/');
            // Jika ada file di akhir, tambahkan 1 untuk kedalaman
            if (!empty($afterProfil) && !str_ends_with($afterProfil, '/')) {
                $profilPelangganDepth += 1;
            }
        }
        switch ($page) {
            case 'index.php':
                if ($isInDashboardSubfolder) {
                    return '../index_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'index_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/index_pelanggan.php';
                } else {
                    return '../dashboard/index_pelanggan.php';
                }
                break;
            case 'about.php':
                if ($isInDashboardSubfolder) {
                    return '../about_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'about_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/about_pelanggan.php';
                } else {
                    return '../dashboard/about_pelanggan.php';
                }
                break;
            case 'perawatan.php':
                if ($isInDashboardSubfolder) {
                    return '../perawatan/perawatan_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'perawatan/perawatan_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/perawatan/perawatan_pelanggan.php';
                } else {
                    return '../dashboard/perawatan/perawatan_pelanggan.php';
                }
                break;
            case 'penitipan.php':
                if ($isInDashboardSubfolder) {
                    return '../penitipan/penitipan_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'penitipan/penitipan_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/penitipan/penitipan_pelanggan.php';
                } else {
                    return '../dashboard/penitipan/penitipan_pelanggan.php';
                }
                break;
            case 'konsultasi.php':
                if ($isInDashboardSubfolder) {
                    return '../konsultasi/konsultasi_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'konsultasi/konsultasi_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/konsultasi/konsultasi_pelanggan.php';
                } else {
                    return '../dashboard/konsultasi/konsultasi_pelanggan.php';
                }
                break;
            case 'shop.php':
                if ($isInDashboardSubfolder) {
                    return '../shop/shop_pelanggan.php';
                } elseif ($isInDashboard) {
                    return 'shop/shop_pelanggan.php';
                } elseif ($isInProfilPelanggan) {
                    return str_repeat('../', $profilPelangganDepth) . 'dashboard/shop/shop_pelanggan.php';
                } else {
                    return '../dashboard/shop/shop_pelanggan.php';
                }
                break;
            default:
                return $page;
        }
    }
    return $page;
}

// Function to check if page is active
function isActive($page)
{
    global $currentPage;

    // Get the expected page name based on login status
    if (isset($_SESSION['id_pelanggan'])) {
        // When logged in, check against pelanggan version filenames
        switch ($page) {
            case 'index.php':
                $expectedPage = 'index_pelanggan.php';
                break;
            case 'about.php':
                $expectedPage = 'about_pelanggan.php';
                break;
            case 'perawatan.php':
                $expectedPage = 'perawatan_pelanggan.php';
                break;
            case 'penitipan.php':
                $expectedPage = 'penitipan_pelanggan.php';
                break;
            case 'konsultasi.php':
                $expectedPage = 'konsultasi_pelanggan.php';
                break;
            case 'shop.php':
                $expectedPage = 'shop_pelanggan.php';
                break;
            default:
                $expectedPage = $page;
        }
    } else {
        // When not logged in, use original page names
        $expectedPage = $page;
    }

    return ($currentPage == $expectedPage) ? 'text-orange-500 font-extrabold border-b-2 border-orange-500' : 'text-gray-700 hover:text-orange-300';
}

// Function to check if any page in array is active (for dropdown)
function isDropdownActive($pages)
{
    global $currentPage;

    foreach ($pages as $page) {
        if (isset($_SESSION['id_pelanggan'])) {
            // Check against pelanggan version filenames
            switch ($page) {
                case 'perawatan.php':
                    if ($currentPage == 'perawatan_pelanggan.php')
                        return true;
                    break;
                case 'penitipan.php':
                    if ($currentPage == 'penitipan_pelanggan.php')
                        return true;
                    break;
                case 'konsultasi.php':
                    if ($currentPage == 'konsultasi_pelanggan.php')
                        return true;
                    break;
            }
        } else {
            // Check against original filenames
            if ($currentPage == $page)
                return true;
        }
    }
    return false;
}

// Pages for service dropdown
$servicePages = ['perawatan.php', 'penitipan.php', 'konsultasi.php'];

// Get user data when logged in
$userData = null;
if (isset($_SESSION['id_pelanggan'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$_SESSION['id_pelanggan']]);
        if ($stmt->rowCount() > 0) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}

// Get favorites count if user is logged in
$favorites_count = 0;
if (isset($_SESSION['id_pelanggan'])) {
    $favorites_query = "SELECT COUNT(*) as count FROM favorit WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($favorites_query);
    $stmt->execute([$_SESSION['id_pelanggan']]);
    $favorites_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['id_pelanggan'])) {
    $cart_query = "SELECT SUM(quantity) as total FROM keranjang WHERE id_pelanggan = ?";
    $stmt = $pdo->prepare($cart_query);
    $stmt->execute([$_SESSION['id_pelanggan']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $result['total'] ? $result['total'] : 0;
}

// IMPROVED Function to get correct photo path
function getPhotoPath($photoProfile)
{
    if (empty($photoProfile)) {
        return '';
    }

    // Remove any leading slashes and backslashes
    $photoProfile = ltrim($photoProfile, '/\\');

    // Get current path to determine where we are
    $currentPath = $_SERVER['REQUEST_URI'];
    $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
    $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
        strpos($currentPath, '/dashboard/penitipan/') !== false ||
        strpos($currentPath, '/dashboard/konsultasi/') !== false ||
        strpos($currentPath, '/dashboard/shop/') !== false;
    $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;

    // Define possible paths based on current location
    $possiblePaths = [];

    if ($isInDashboardSubfolder) {
        // In dashboard subfolder (perawatan, penitipan, etc.)
        $possiblePaths = [
            '../../profilpelanggan/uploads/pelanggan/' . $photoProfile,
            '../profilpelanggan/uploads/pelanggan/' . $photoProfile
        ];
    } elseif ($isInDashboard) {
        // In dashboard root
        $possiblePaths = [
            '../profilpelanggan/uploads/pelanggan/' . $photoProfile,
            '../../profilpelanggan/uploads/pelanggan/' . $photoProfile
        ];
    } elseif ($isInProfilPelanggan) {
        // In profilpelanggan folder
        $possiblePaths = [
            'uploads/pelanggan/' . $photoProfile,
            '../uploads/pelanggan/' . $photoProfile,
            '../../uploads/pelanggan/' . $photoProfile
        ];
    } else {
        // In public folder or other locations
        $possiblePaths = [
            'profilpelanggan/uploads/pelanggan/' . $photoProfile,
            '../profilpelanggan/uploads/pelanggan/' . $photoProfile
        ];
    }

    // Check each path
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    // If no file found, return empty string
    return '';
}

// Function to get base URL for login/register links
function getAuthPath($page)
{
    $currentPath = $_SERVER['REQUEST_URI'];
    $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
        strpos($currentPath, '/dashboard/penitipan/') !== false ||
        strpos($currentPath, '/dashboard/konsultasi/') !== false ||
        strpos($currentPath, '/dashboard/shop/') !== false;
    $isInDashboard = strpos($currentPath, '/dashboard/') !== false;

    if ($isInDashboardSubfolder) {
        return '../../auth/' . $page;
    } elseif ($isInDashboard) {
        return '../auth/' . $page;
    } else {
        return '../auth/' . $page;
    }
}

// Function to get correct logout path
function getLogoutPath()
{
    $currentPath = $_SERVER['REQUEST_URI'];
    $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
        strpos($currentPath, '/dashboard/penitipan/') !== false ||
        strpos($currentPath, '/dashboard/konsultasi/') !== false ||
        strpos($currentPath, '/dashboard/shop/') !== false;
    $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
    $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;
    $isInProfilPelangganSubfolder = strpos($currentPath, '/profilpelanggan/keranjang/') !== false ||
        strpos($currentPath, '/profilpelanggan/favorit/') !== false ||
        strpos($currentPath, '/profilpelanggan/detailakun/') !== false ||
        strpos($currentPath, '/profilpelanggan/detailanabul/') !== false ||
        strpos($currentPath, '/profilpelanggan/pesanan/') !== false ||
        strpos($currentPath, '/profilpelanggan/hapus/') !== false;

    if ($isInDashboardSubfolder) {
        return '../../auth/logout.php';
    } elseif ($isInDashboard) {
        return '../auth/logout.php';
    } elseif ($isInProfilPelangganSubfolder) {
        return '../../auth/logout.php';
    } elseif ($isInProfilPelanggan) {
        return '../auth/logout.php';
    } else {
        return '../auth/logout.php';
    }
}

// Function to get profile edit path
function getProfilePath()
{
    $currentPath = $_SERVER['REQUEST_URI'];
    $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
        strpos($currentPath, '/dashboard/penitipan/') !== false ||
        strpos($currentPath, '/dashboard/konsultasi/') !== false ||
        strpos($currentPath, '/dashboard/shop/') !== false;
    $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
    $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;
    $isInProfilPelangganSubfolder = strpos($currentPath, '/profilpelanggan/keranjang/') !== false ||
        strpos($currentPath, '/profilpelanggan/favorit/') !== false ||
        strpos($currentPath, '/profilpelanggan/detailakun/') !== false ||
        strpos($currentPath, '/profilpelanggan/detailanabul/') !== false ||
        strpos($currentPath, '/profilpelanggan/pesanan/') !== false ||
        strpos($currentPath, '/profilpelanggan/hapus/') !== false;

    if ($isInDashboardSubfolder) {
        return '../../profilpelanggan/detailakun/profil_akun.php';
    } elseif ($isInDashboard) {
        return '../profilpelanggan/detailakun/profil_akun.php';
    } elseif ($isInProfilPelangganSubfolder) {
        return '../detailakun/profil_akun.php';
    } elseif ($isInProfilPelanggan) {
        return 'detailakun/profil_akun.php';
    } else {
        return '../profilpelanggan/detailakun/profil_akun.php';
    }
}

// Function to get cart and favorite paths
function getCartPath()
{
    if (isset($_SESSION['id_pelanggan'])) {
        $currentPath = $_SERVER['REQUEST_URI'];
        $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
            strpos($currentPath, '/dashboard/penitipan/') !== false ||
            strpos($currentPath, '/dashboard/konsultasi/') !== false ||
            strpos($currentPath, '/dashboard/shop/') !== false;
        $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
        $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;
        $isInProfilPelangganSubfolder = strpos($currentPath, '/profilpelanggan/keranjang/') !== false ||
            strpos($currentPath, '/profilpelanggan/favorit/') !== false ||
            strpos($currentPath, '/profilpelanggan/detailakun/') !== false ||
            strpos($currentPath, '/profilpelanggan/detailanabul/') !== false ||
            strpos($currentPath, '/profilpelanggan/pesanan/') !== false ||
            strpos($currentPath, '/profilpelanggan/hapus/') !== false;

        if ($isInDashboardSubfolder) {
            return '../../profilpelanggan/keranjang/keranjang.php';
        } elseif ($isInDashboard) {
            return '../profilpelanggan/keranjang/keranjang.php';
        } elseif ($isInProfilPelangganSubfolder) {
            return '../keranjang/keranjang.php';
        } elseif ($isInProfilPelanggan) {
            return 'keranjang/keranjang.php';
        } else {
            return '../profilpelanggan/keranjang/keranjang.php';
        }
    }
    return '../profilpelanggan/keranjang/keranjang.php';
}

function getFavoritePath()
{
    if (isset($_SESSION['id_pelanggan'])) {
        $currentPath = $_SERVER['REQUEST_URI'];
        $isInDashboardSubfolder = strpos($currentPath, '/dashboard/perawatan/') !== false ||
            strpos($currentPath, '/dashboard/penitipan/') !== false ||
            strpos($currentPath, '/dashboard/konsultasi/') !== false ||
            strpos($currentPath, '/dashboard/shop/') !== false;
        $isInDashboard = strpos($currentPath, '/dashboard/') !== false;
        $isInProfilPelanggan = strpos($currentPath, '/profilpelanggan/') !== false;
        $isInProfilPelangganSubfolder = strpos($currentPath, '/profilpelanggan/keranjang/') !== false ||
            strpos($currentPath, '/profilpelanggan/favorit/') !== false ||
            strpos($currentPath, '/profilpelanggan/detailakun/') !== false ||
            strpos($currentPath, '/profilpelanggan/detailanabul/') !== false ||
            strpos($currentPath, '/profilpelanggan/pesanan/') !== false ||
            strpos($currentPath, '/profilpelanggan/hapus/') !== false;

        if ($isInDashboardSubfolder) {
            return '../../profilpelanggan/favorit/favorit.php';
        } elseif ($isInDashboard) {
            return '../profilpelanggan/favorit/favorit.php';
        } elseif ($isInProfilPelangganSubfolder) {
            return '../favorit/favorit.php';
        } elseif ($isInProfilPelanggan) {
            return 'favorit/favorit.php';
        } else {
            return '../profilpelanggan/favorit/favorit.php';
        }
    }
    return '../profilpelanggan/favorit/favorit.php';
}
?>

<nav class="bg-white sticky top-0 py-6 rounded-b-4xl shadow-xl z-50">
    <div class="container max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-12">
            <a class="flex items-center font-bold text-xl" href="<?php echo getCorrectPath('index.php'); ?>">
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
                <a class="font-semibold <?php echo isActive('index.php'); ?>"
                    href="<?php echo getCorrectPath('index.php'); ?>">Beranda</a>

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
                            <a href="<?php echo getCorrectPath('perawatan.php'); ?>"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Perawatan</a>
                            <a href="<?php echo getCorrectPath('penitipan.php'); ?>"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Penitipan</a>
                            <a href="<?php echo getCorrectPath('konsultasi.php'); ?>"
                                class="block px-4 py-2 text-base text-gray-700 hover:bg-orange-50 hover:text-orange-500 transition-colors duration-200">Konsultasi</a>
                        </div>
                    </div>
                </div>

                <a class="font-semibold <?php echo isActive('shop.php'); ?>"
                    href="<?php echo getCorrectPath('shop.php'); ?>">Toko</a>
                <a class="font-semibold <?php echo isActive('about.php'); ?>"
                    href="<?php echo getCorrectPath('about.php'); ?>">Tentang Kami</a>
            </div>

            <!-- Mobile Menu -->
            <div class="lg:hidden hidden" id="mobile-menu">
                <div class="absolute top-full left-0 right-0 bg-white shadow-lg rounded-b-lg py-4 px-4 space-y-4">
                    <a class="block font-semibold <?php echo isActive('index.php'); ?>"
                        href="<?php echo getCorrectPath('index.php'); ?>">Beranda</a>
                    <a class="block font-semibold <?php echo isActive('perawatan.php'); ?>"
                        href="<?php echo getCorrectPath('perawatan.php'); ?>">Perawatan</a>
                    <a class="block font-semibold <?php echo isActive('penitipan.php'); ?>"
                        href="<?php echo getCorrectPath('penitipan.php'); ?>">Penitipan</a>
                    <a class="block font-semibold <?php echo isActive('konsultasi.php'); ?>"
                        href="<?php echo getCorrectPath('konsultasi.php'); ?>">Konsultasi</a>
                    <a class="block font-semibold <?php echo isActive('shop.php'); ?>"
                        href="<?php echo getCorrectPath('shop.php'); ?>">Toko</a>
                    <a class="block font-semibold <?php echo isActive('about.php'); ?>"
                        href="<?php echo getCorrectPath('about.php'); ?>">Tentang
                        Kami</a>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-6">
                <?php if ($userData): ?>
                    <!-- favorite-->
                    <div class="relative">
                        <a href="<?php echo getFavoritePath(); ?>" class="text-gray-700 hover:text-orange-500">
                            <i class="fa-regular fa-heart text-2xl"></i>
                            <span
                                class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                                <?php echo $favorites_count; ?>
                            </span>
                        </a>
                    </div>
                    <!-- keranjang -->
                    <div class="relative">
                        <a href="<?php echo getCartPath(); ?>" class="text-gray-700 hover:text-orange-500">
                            <i class="fa-solid fa-cart-shopping text-2xl"></i>
                            <span
                                class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                                <?php echo $cart_count; ?>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- profil -->
                <?php if ($userData): ?>
                    <div class="relative">
                        <button id="profileMenuBtn"
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-400 hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all overflow-hidden">

                            <?php
                            $photoPath = '';
                            if (!empty($userData['foto_profil'])) {
                                $photoPath = getPhotoPath($userData['foto_profil']);
                            }
                            ?>

                            <?php if (!empty($photoPath)): ?>
                                <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profil"
                                    class="w-full h-full object-cover rounded-full"
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                            <?php endif; ?>

                            <!-- Default/Fallback icon -->
                            <div class="w-full h-full flex items-center justify-center bg-orange-400"
                                style="display: <?php echo empty($photoPath) ? 'flex' : 'none'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="white">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                        </button>

                        <!-- dropdown -->
                        <div id="profileDropdown"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl z-50 py-3 px-4 hidden"
                            style="min-width: 250px;">
                            <div class="text-xs text-gray-500 mb-2">Saat ini menggunakan</div>
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden mr-3 flex-shrink-0 bg-gray-300 flex items-center justify-center">
                                    <?php if (!empty($photoPath)): ?>
                                        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profil"
                                            class="w-full h-full object-cover"
                                            style="width: 48px; height: 48px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                    <?php endif; ?>
                                    <!-- Fallback untuk dropdown -->
                                    <div class="w-full h-full bg-gray-300 flex items-center justify-center"
                                        style="display: <?php echo empty($photoPath) ? 'flex' : 'none'; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="font-bold text-base text-gray-900">
                                        <?php echo htmlspecialchars($userData['nama_lengkap']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($userData['email']); ?>
                                    </div>
                                </div>
                            </div>
                            <button onclick="window.location.href='<?php echo getProfilePath(); ?>'"
                                class="w-full text-left font-semibold py-2 px-1 rounded hover:bg-orange-50 mb-1">Profil
                                Akun</button>
                            <button onclick="window.location.href='<?php echo getLogoutPath(); ?>'"
                                class="w-full text-left font-semibold py-2 px-1 rounded hover:bg-orange-50 text-red-500">Keluar</button>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo getAuthPath('login.php'); ?>"
                        class="font-semibold px-4 py-2 border-2 border-orange-500 text-orange-500 rounded hover:bg-orange-500 hover:text-white transition-colors">Masuk</a>
                    <a href="<?php echo getAuthPath('registrasi.php'); ?>"
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