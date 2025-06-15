<?php
session_start();
require 'function.php';
$id = $_GET["id"];
$type = $_GET["type"];

// Determine the correct redirect page based on type
$redirect_page = match ($type) {
    'produk' => 'dataproduk.php',
    'dokter' => 'datadokter.php',
    'pelanggan' => 'datapelanggan.php',
    'anabul' => 'dataanabul.php',
    'pesanan' => 'datapesanan.php',
    'penitipan' => 'datapenitipan.php',
    'perawatan' => 'dataperawatan.php',
    'konsultasi' => 'datakonsultasi.php',
    default => 'dataproduk.php'
};

if (hapus($id, $type) > 0) {
    echo "
            <script>
            alert('data berhasil dihapus!');
            document.location.href='$redirect_page';
            </script>
        ";
} else {
    echo "
            <script>
            alert('data gagal dihapus!');
            document.location.href='$redirect_page';
            </script>
        ";
}

?>