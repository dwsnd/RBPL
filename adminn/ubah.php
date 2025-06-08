<?php
require 'function.php';
$id_menu = $_GET["id_menu"];
$pdk = query("SELECT * FROM produk WHERE id_menu=$id_menu")[0];
if (isset($_POST["submit"])) {
    if (ubah($_POST) > 0) {
        echo "
            <script>
            alert('data berhasil dubah!');
            document.location.href='produk.php';
            </script>
        ";
    } else {
        echo "
        <script>
        alert('data gagal dubah!');
        document.location.href='produk.php';
        </script>
    ";
    }
}


?>