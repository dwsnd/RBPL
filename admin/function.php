<?php
$conn = mysqli_connect("localhost", "root", "", "petshop");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Generic CRUD Functions
function tambah($data, $type)
{
    global $conn;

    // Separate POST and FILES data
    $post_data = array_filter($data, function ($item) {
        return !is_array($item);
    });

    $files_data = array_filter($data, function ($item) {
        return is_array($item);
    });

    // Sanitize only POST data
    $sanitized_data = array_map(function ($item) {
        return htmlspecialchars($item);
    }, $post_data);

    // Handle file upload if present
    $image = '';
    if (isset($files_data['foto_utama']) && $files_data['foto_utama']['error'] !== 4) {
        $image = upload($files_data['foto_utama'], $type);
        if (!$image) {
            return false;
        }
    }

    // Build query based on type
    $query = '';
    switch ($type) {
        case 'produk':
            $query = "INSERT INTO produk (nama_produk, kategori, sub_kategori, target_hewan, harga, stok, berat_gram, foto_utama, deskripsi) 
                     VALUES ('{$sanitized_data['nama_produk']}', '{$sanitized_data['kategori']}', 
                            '{$sanitized_data['sub_kategori']}', '{$sanitized_data['target_hewan']}', 
                            '{$sanitized_data['harga']}', '{$sanitized_data['stok']}', 
                            '{$sanitized_data['berat_gram']}', '$image',
                            '{$sanitized_data['deskripsi']}')";
            break;

        case 'pesanan':
            $query = "INSERT INTO pesanan (id_pelanggan, id_produk, jumlah, total_harga, tanggal_pesanan, status_pesanan, alamat_pengiriman) 
                     VALUES ('{$sanitized_data['id_pelanggan']}', '{$sanitized_data['id_produk']}', 
                            '{$sanitized_data['jumlah']}', '{$sanitized_data['total_harga']}', 
                            '{$sanitized_data['tanggal_pesanan']}', '{$sanitized_data['status_pesanan']}', 
                            '{$sanitized_data['alamat_pengiriman']}')";
            break;

        case 'dokter':
            $query = "INSERT INTO dokter (nama_dokter, nomor_lisensi, spesialisasi, email, 
                     nomor_telepon, tarif_konsultasi, foto_dokter) 
                     VALUES ('{$sanitized_data['nama_dokter']}', '{$sanitized_data['nomor_lisensi']}', 
                            '{$sanitized_data['spesialisasi']}', '{$sanitized_data['email']}', 
                            '{$sanitized_data['nomor_telepon']}', '{$sanitized_data['tarif_konsultasi']}', 
                            '$image')";
            break;

        case 'pelanggan':
            $query = "INSERT INTO pelanggan (nama, email, nomor_telepon, alamat, status) 
                     VALUES ('{$sanitized_data['nama']}', '{$sanitized_data['email']}', 
                            '{$sanitized_data['nomor_telepon']}', '{$sanitized_data['alamat']}', 
                            '{$sanitized_data['status']}')";
            break;

        case 'anabul':
            $query = "INSERT INTO anabul (nama_hewan, jenis_hewan, ras, umur, berat, 
                     id_pelanggan, foto_hewan) 
                     VALUES ('{$sanitized_data['nama_hewan']}', '{$sanitized_data['jenis_hewan']}', 
                            '{$sanitized_data['ras']}', '{$sanitized_data['umur']}', 
                            '{$sanitized_data['berat']}', '{$sanitized_data['id_pelanggan']}', 
                            '$image')";
            break;

        default:
            return false;
    }

    if (empty($query)) {
        return false;
    }

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function update($data, $type)
{
    global $conn;

    // Separate POST and FILES data
    $post_data = array_filter($data, function ($item) {
        return !is_array($item);
    });

    $files_data = array_filter($data, function ($item) {
        return is_array($item);
    });

    // Sanitize only POST data
    $sanitized_data = array_map(function ($item) {
        return htmlspecialchars($item);
    }, $post_data);

    // Get current data to preserve existing image if no new upload
    $current_data = query("SELECT * FROM $type WHERE id_" . $type . " = " . intval($sanitized_data['id']))[0];

    // Handle file upload if present
    $image = $current_data['foto_utama'] ?? '';
    if (isset($files_data['foto_utama']) && $files_data['foto_utama']['error'] !== 4) {
        $uploaded_image = upload($files_data['foto_utama'], $type);
        if ($uploaded_image) {
            $image = $uploaded_image;

            // Delete old image if it exists and is different from default
            if (!empty($current_data['foto_utama']) && $current_data['foto_utama'] !== $uploaded_image) {
                $old_image_path = '../' . $current_data['foto_utama'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }

    // Determine ID column based on type
    $id_column = '';
    switch ($type) {
        case 'produk':
            $id_column = 'id_produk';
            break;
        case 'dokter':
            $id_column = 'id_dokter';
            break;
        case 'pelanggan':
            $id_column = 'id_pelanggan';
            break;
        default:
            return false;
    }

    // Build query based on type
    switch ($type) {
        case 'produk':
            $query = "UPDATE produk SET 
                     nama_produk = '" . mysqli_real_escape_string($conn, $sanitized_data['nama_produk']) . "',
                     kategori = '" . mysqli_real_escape_string($conn, $sanitized_data['kategori']) . "',
                     sub_kategori = '" . mysqli_real_escape_string($conn, $sanitized_data['sub_kategori']) . "',
                     target_hewan = '" . mysqli_real_escape_string($conn, $sanitized_data['target_hewan']) . "',
                     harga = '" . mysqli_real_escape_string($conn, $sanitized_data['harga']) . "',
                     stok = '" . mysqli_real_escape_string($conn, $sanitized_data['stok']) . "',
                     berat_gram = '" . mysqli_real_escape_string($conn, $sanitized_data['berat_gram']) . "',
                     foto_utama = '" . mysqli_real_escape_string($conn, $image) . "',
                     deskripsi = '" . mysqli_real_escape_string($conn, $sanitized_data['deskripsi']) . "'
                     WHERE $id_column = " . intval($sanitized_data['id']);
            break;

        case 'dokter':
            $query = "UPDATE dokter SET 
                     nama_dokter = '{$sanitized_data['nama_dokter']}',
                     nomor_lisensi = '{$sanitized_data['nomor_lisensi']}',
                     spesialisasi = '{$sanitized_data['spesialisasi']}',
                     email = '{$sanitized_data['email']}',
                     nomor_telepon = '{$sanitized_data['nomor_telepon']}',
                     tarif_konsultasi = '{$sanitized_data['tarif_konsultasi']}'";
            if ($image) {
                $query .= ", foto_dokter = '$image'";
            }
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'pelanggan':
            $query = "UPDATE pelanggan SET 
                     nama_pelanggan = '{$sanitized_data['nama_pelanggan']}',
                     email = '{$sanitized_data['email']}',
                     no_telp = '{$sanitized_data['no_telp']}',
                     alamat = '{$sanitized_data['alamat']}'";
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'anabul':
            $query = "UPDATE anabul SET 
                     nama_hewan = '{$sanitized_data['nama_hewan']}',
                     jenis_hewan = '{$sanitized_data['jenis_hewan']}',
                     ras = '{$sanitized_data['ras']}',
                     umur = '{$sanitized_data['umur']}',
                     berat = '{$sanitized_data['berat']}',
                     id_pelanggan = '{$sanitized_data['id_pelanggan']}'";
            if ($image) {
                $query .= ", foto_hewan = '$image'";
            }
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'pesanan':
            $query = "UPDATE pesanan SET 
                     id_pelanggan = '{$sanitized_data['id_pelanggan']}',
                     id_produk = '{$sanitized_data['id_produk']}',
                     jumlah = '{$sanitized_data['jumlah']}',
                     total_harga = '{$sanitized_data['total_harga']}',
                     tanggal_pesanan = '{$sanitized_data['tanggal_pesanan']}',
                     status_pesanan = '{$sanitized_data['status_pesanan']}',
                     alamat_pengiriman = '{$sanitized_data['alamat_pengiriman']}'";
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'penitipan':
            $query = "UPDATE penitipan SET 
                     id_pelanggan = '{$sanitized_data['id_pelanggan']}',
                     id_anabul = '{$sanitized_data['id_anabul']}',
                     tanggal_masuk = '{$sanitized_data['tanggal_masuk']}',
                     tanggal_keluar = '{$sanitized_data['tanggal_keluar']}',
                     status = '{$sanitized_data['status']}',
                     catatan = '{$sanitized_data['catatan']}'";
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'perawatan':
            $query = "UPDATE perawatan SET 
                     id_pelanggan = '{$sanitized_data['id_pelanggan']}',
                     id_anabul = '{$sanitized_data['id_anabul']}',
                     jenis_perawatan = '{$sanitized_data['jenis_perawatan']}',
                     tanggal = '{$sanitized_data['tanggal']}',
                     status = '{$sanitized_data['status']}',
                     catatan = '{$sanitized_data['catatan']}'";
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;

        case 'konsultasi':
            $query = "UPDATE konsultasi SET 
                     id_pelanggan = '{$sanitized_data['id_pelanggan']}',
                     id_dokter = '{$sanitized_data['id_dokter']}',
                     tanggal = '{$sanitized_data['tanggal']}',
                     waktu = '{$sanitized_data['waktu']}',
                     status = '{$sanitized_data['status']}',
                     keluhan = '{$sanitized_data['keluhan']}'";
            $query .= " WHERE $id_column = {$sanitized_data['id']}";
            break;
    }

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapus($id, $type)
{
    global $conn;

    // Determine the correct primary key column name based on type
    $id_column = match ($type) {
        'produk' => 'id_produk',
        'dokter' => 'id_dokter',
        'pelanggan' => 'id_pelanggan',
        'anabul' => 'id_anabul',
        'pesanan' => 'id_pesanan',
        'penitipan' => 'id_penitipan',
        'perawatan' => 'id_perawatan',
        'konsultasi' => 'id_konsultasi',
        default => 'id_produk'
    };

    // Build query based on type
    switch ($type) {
        case 'produk':
            $query = "DELETE FROM produk WHERE $id_column = $id";
            break;
        case 'dokter':
            $query = "DELETE FROM dokter_hewan WHERE $id_column = $id";
            break;
        case 'pelanggan':
            $query = "DELETE FROM pelanggan WHERE $id_column = $id";
            break;
        case 'anabul':
            $query = "DELETE FROM anabul WHERE $id_column = $id";
            break;
        case 'pesanan':
            $query = "DELETE FROM pesanan WHERE $id_column = $id";
            break;
        case 'penitipan':
            $query = "DELETE FROM penitipan WHERE $id_column = $id";
            break;
        case 'perawatan':
            $query = "DELETE FROM perawatan WHERE $id_column = $id";
            break;
        case 'konsultasi':
            $query = "DELETE FROM konsultasi WHERE $id_column = $id";
            break;
        default:
            return 0;
    }

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function upload($file, $type)
{
    $namaFile = $file['name'];
    $ukuranFile = $file['size'];
    $error = $file['error'];
    $tmpName = $file['tmp_name'];


    // Validate file extension
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiFile = explode('.', $namaFile);
    $ekstensiFile = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file tidak valid! Gunakan JPG, JPEG, PNG, atau WEBP');</script>";
        return false;
    }

    // Validate file size (max 2MB)
    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB');</script>";
        return false;
    }

    // Generate unique filename
    $namaFileBaru = uniqid() . '.' . $ekstensiFile;

    // Determine upload directory based on type
    $uploadDir = '../assets/img/';
    switch ($type) {
        case 'produk':
            $uploadDir .= 'produk/';
            break;
        case 'dokter':
            $uploadDir .= 'dokter/';
            break;
        case 'anabul':
            $uploadDir .= 'anabul/';
            break;
        default:
            $uploadDir .= 'uploads/';
    }

    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file
    if (move_uploaded_file($tmpName, $uploadDir . $namaFileBaru)) {
        // Return the relative path from the admin directory
        return 'assets/img/' . basename($uploadDir) . '/' . $namaFileBaru;
    }

    return false;
}
