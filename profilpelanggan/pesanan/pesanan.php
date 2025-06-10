<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'produk';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pesanan - Ling-Ling Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tabs {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .tabs a {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            text-decoration: none;
            color: #333;
            background: #fff;
            border-right: 1px solid #ddd;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }

        .tabs a:last-child {
            border-right: none;
        }

        .tabs a.active {
            color: #fd7e14;
            border-bottom: 2px solid #fd7e14;
            background: #fff7f0;
        }

        .tab-content {
            padding: 24px 0 0 0;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="tabs mb-4">
            <a href="?tab=produk" class="<?= $tab == 'produk' ? 'active' : '' ?>">Produk</a>
            <a href="?tab=perawatan" class="<?= $tab == 'perawatan' ? 'active' : '' ?>">Perawatan</a>
            <a href="?tab=penitipan" class="<?= $tab == 'penitipan' ? 'active' : '' ?>">Penitipan</a>
            <a href="?tab=konsultasi" class="<?= $tab == 'konsultasi' ? 'active' : '' ?>">Konsultasi</a>
        </div>
        <div class="tab-content">
            <?php
            if ($tab == 'produk')
                include 'pesanan_produk.php';
            elseif ($tab == 'perawatan')
                include 'pesanan_perawatan.php';
            elseif ($tab == 'penitipan')
                include 'pesanan_penitipan.php';
            elseif ($tab == 'konsultasi')
                include 'pesanan_konsultasi.php';
            ?>
        </div>
    </div>
</body>

</html>