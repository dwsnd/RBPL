<!-- Sidebar -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if current page matches the base name
function isActivePage($baseName)
{
    global $current_page;
    return strpos($current_page, $baseName) !== false;
}

// Function to get menu item classes
function getMenuItemClasses($baseName)
{
    return isActivePage($baseName)
        ? 'bg-orange-500 text-white'
        : 'text-gray-600 hover:bg-gray-200';
}
?>

<div class="w-64 bg-white border border-grey-100 shadow-sm sidebar">
    <div class="p-4">
        <div class="space-y-2">
            <div class="<?php echo getMenuItemClasses('akun'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../detailakun/profil_akun.php" class="flex items-center gap-2">
                    <i class="fas fa-user w-5"></i>
                    <span>Detail Akun</span>
                </a>
            </div>
            <div class="<?php echo getMenuItemClasses('anabul'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../detailanabul/profil_anabul.php" class="flex items-center gap-2">
                    <i class="fas fa-paw w-5"></i>
                    <span>Detail Anabul</span>
                </a>
            </div>
            <div class="<?php echo getMenuItemClasses('favorit'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../favorit/favorit.php" class="flex items-center gap-2">
                    <i class="fas fa-heart w-5"></i>
                    <span>Favorit</span>
                </a>
            </div>
            <div class="<?php echo getMenuItemClasses('keranjang'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../keranjang.php" class="flex items-center gap-2">
                    <i class="fas fa-cart-shopping w-5"></i>
                    <span>Keranjang</span>
                </a>
            </div>
            <div class="<?php echo getMenuItemClasses('pesanan'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../pesanan.php" class="flex items-center gap-2">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span>Pesanan</span>
                </a>
            </div>
            <div class="<?php echo getMenuItemClasses('hapusakun'); ?> px-3 py-2 rounded-lg cursor-pointer text-sm">
                <a href="../hapusakun.php" class="flex items-center gap-2">
                    <i class="fas fa-user-xmark w-5"></i>
                    <span>Hapus Akun</span>
                </a>
            </div>
        </div>
    </div>
</div>