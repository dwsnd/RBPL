<?php
$conn = mysqli_connect("localhost", "root", "", "petshop");

function query($query)
{
    global $conn;
    $query = str_replace('FROM dokter ', 'FROM dokter_hewan ', $query);
    $query = str_replace('from dokter ', 'from dokter_hewan ', $query);
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
        // Untuk produk, kirim target_hewan sebagai parameter ke fungsi upload
        if ($type === 'produk' && isset($sanitized_data['target_hewan'])) {
            $image = upload($files_data['foto_utama'], $type, $sanitized_data['target_hewan']);
        } else {
            $image = upload($files_data['foto_utama'], $type);
        }

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
            // Handle different order types
            $jenis_pesanan = $sanitized_data['jenis_pesanan'] ?? '';
            $id_pelanggan = $sanitized_data['id_pelanggan'] ?? '';
            $total_harga = $sanitized_data['total_harga'] ?? 0;
            $status_pesanan = 'completed';
            $status_pembayaran = $sanitized_data['status_pembayaran'] ?? 'pending';
            $metode_pembayaran = $sanitized_data['metode_pembayaran'] ?? '';
            $catatan_pelanggan = $sanitized_data['catatan_pelanggan'] ?? '';

            // Generate unique order number
            $date = date('Ymd');
            $random = rand(1000, 9999);
            $nomor_pesanan = "ORD-" . $date . "-" . $random;

            // Insert into pesanan table
            $query = "INSERT INTO pesanan (nomor_pesanan, id_pelanggan, jenis_pesanan, total_harga, status_pesanan, status_pembayaran, metode_pembayaran, catatan_pelanggan, tanggal_pesanan) 
                     VALUES ('$nomor_pesanan', '$id_pelanggan', '$jenis_pesanan', '$total_harga', '$status_pesanan', '$status_pembayaran', '$metode_pembayaran', '$catatan_pelanggan', NOW())";

            if (mysqli_query($conn, $query)) {
                $id_pesanan = mysqli_insert_id($conn);

                // Insert into specific order tables based on jenis_pesanan
                switch ($jenis_pesanan) {
                    case 'produk':
                        $id_produk = $sanitized_data['id_produk'] ?? '';
                        $quantity = $sanitized_data['quantity'] ?? 1;

                        // Get product price
                        $product_query = "SELECT harga FROM produk WHERE id_produk = '$id_produk'";
                        $product_result = mysqli_query($conn, $product_query);
                        $product_data = mysqli_fetch_assoc($product_result);
                        $harga_satuan = $product_data['harga'] ?? 0;
                        $subtotal = $harga_satuan * $quantity;

                        $query_detail = "INSERT INTO pesanan_produk (id_pesanan, id_produk, quantity, harga_satuan, subtotal) 
                                       VALUES ('$id_pesanan', '$id_produk', '$quantity', '$harga_satuan', '$subtotal')";
                        break;

                    case 'penitipan':
                        $id_anabul = $sanitized_data['id_anabul'] ?? '';
                        $tanggal_checkin = $sanitized_data['tanggal_checkin'] ?? '';
                        $tanggal_checkout = $sanitized_data['tanggal_checkout'] ?? '';
                        $catatan_khusus = $sanitized_data['catatan_khusus'] ?? '';

                        // Calculate number of days
                        $checkin_date = new DateTime($tanggal_checkin);
                        $checkout_date = new DateTime($tanggal_checkout);
                        $jumlah_hari = $checkin_date->diff($checkout_date)->days;

                        $query_detail = "INSERT INTO penitipan (id_pesanan, id_anabul, tanggal_checkin, tanggal_checkout, jumlah_hari, catatan_checkin) 
                                       VALUES ('$id_pesanan', '$id_anabul', '$tanggal_checkin', '$tanggal_checkout', '$jumlah_hari', '$catatan_khusus')";
                        break;

                    case 'perawatan':
                        $id_anabul = $sanitized_data['id_anabul'] ?? '';
                        $tanggal_perawatan = $sanitized_data['tanggal_perawatan'] ?? '';
                        $waktu_perawatan = $sanitized_data['waktu_perawatan'] ?? '';
                        $catatan_khusus = $sanitized_data['catatan_khusus'] ?? '';

                        // For perawatan, we need to create a pesanan_layanan record first
                        $id_layanan = $sanitized_data['id_layanan'] ?? '';
                        $harga_layanan = $sanitized_data['harga_layanan'] ?? 0;

                        $query_layanan = "INSERT INTO pesanan_layanan (id_pesanan, id_layanan, id_anabul, harga_layanan, catatan_khusus) 
                                         VALUES ('$id_pesanan', '$id_layanan', '$id_anabul', '$harga_layanan', '$catatan_khusus')";

                        if (mysqli_query($conn, $query_layanan)) {
                            $id_pesanan_layanan = mysqli_insert_id($conn);

                            // Now insert into perawatan table
                            $paket_perawatan = $sanitized_data['paket_perawatan'] ?? 'basic';
                            $query_detail = "INSERT INTO perawatan (id_pesanan_layanan, id_anabul, paket_perawatan, tanggal_perawatan, catatan_perawatan) 
                                           VALUES ('$id_pesanan_layanan', '$id_anabul', '$paket_perawatan', '$tanggal_perawatan', '$catatan_khusus')";
                        }
                        break;

                    case 'konsultasi':
                        $id_dokter = $sanitized_data['id_dokter'] ?? '';
                        $id_anabul = $sanitized_data['id_anabul'] ?? '';
                        $tanggal_konsultasi = $sanitized_data['tanggal_konsultasi'] ?? '';
                        $waktu_konsultasi = $sanitized_data['waktu_konsultasi'] ?? '';
                        $catatan_khusus = $sanitized_data['catatan_khusus'] ?? '';
                        // Set status_konsultasi default completed untuk admin
                        $status_konsultasi = 'completed';
                        $query_detail = "INSERT INTO konsultasi (id_pesanan, id_dokter, id_anabul, keluhan_utama, gejala, tanggal_kontrol, status_konsultasi) 
                                       VALUES ('$id_pesanan', '$id_dokter', '$id_anabul', '$catatan_khusus', '$catatan_khusus', '$tanggal_konsultasi', '$status_konsultasi')";
                        break;

                    default:
                        return false;
                }

                if (isset($query_detail)) {
                    mysqli_query($conn, $query_detail);
                }

                return $id_pesanan;
            }
            return false;

        case 'dokter_hewan':
            $query = "INSERT INTO dokter_hewan (nama_dokter, nomor_lisensi, spesialisasi, email, 
                     nomor_telepon, tarif_konsultasi, foto_dokter) 
                     VALUES ('{$sanitized_data['nama_dokter']}', '{$sanitized_data['nomor_lisensi']}', 
                            '{$sanitized_data['spesialisasi']}', '{$sanitized_data['email']}', 
                            '{$sanitized_data['nomor_telepon']}', '{$sanitized_data['tarif_konsultasi']}', 
                            '$image')";
            break;

        case 'pelanggan':
            $query = "INSERT INTO pelanggan (nama_lengkap, email, nomor_telepon, alamat, status) 
                     VALUES ('{$sanitized_data['nama_lengkap']}', '{$sanitized_data['email']}', 
                            '{$sanitized_data['nomor_telepon']}', '{$sanitized_data['alamat']}', 
                            '{$sanitized_data['status']}')";
            break;

        case 'anabul':
            $query = "INSERT INTO anabul (nama_hewan, spesies, ras, umur_tahun, umur_bulan, berat_kg, jenis_kelamin, warna, ciri_khusus, riwayat_penyakit, alergi, id_pelanggan, foto_utama) 
                     VALUES ('{$sanitized_data['nama_hewan']}', '{$sanitized_data['spesies']}', 
                            '{$sanitized_data['ras']}', '{$sanitized_data['umur_tahun']}', '{$sanitized_data['umur_bulan']}', 
                            '{$sanitized_data['berat_kg']}', '{$sanitized_data['jenis_kelamin']}', '{$sanitized_data['warna']}', 
                            '{$sanitized_data['ciri_khusus']}', '{$sanitized_data['riwayat_penyakit']}', '{$sanitized_data['alergi']}', 
                            '{$sanitized_data['id_pelanggan']}', '$image')";
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

    // Sanitize only POST data with proper handling of undefined keys
    $sanitized_data = array_map(function ($item) {
        return htmlspecialchars($item ?? '');
    }, $post_data);

    // Get current data to preserve existing image if no new upload
    $current_data = query("SELECT * FROM $type WHERE id_" . $type . " = " . intval($sanitized_data['id'] ?? 0))[0] ?? [];

    // Handle file upload if present
    $image = $current_data['foto_utama'] ?? '';
    if (isset($files_data['foto_utama']) && $files_data['foto_utama']['error'] !== 4) {
        // Untuk produk, kirim target_hewan sebagai parameter ke fungsi upload
        if ($type === 'produk' && isset($sanitized_data['target_hewan'])) {
            $uploaded_image = upload($files_data['foto_utama'], $type, $sanitized_data['target_hewan']);
        } else {
            $uploaded_image = upload($files_data['foto_utama'], $type);
        }

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
        case 'dokter_hewan':
            $id_column = 'id_dokter';
            break;
        case 'pelanggan':
            $id_column = 'id_pelanggan';
            break;
        case 'anabul':
            $id_column = 'id_anabul';
            break;
        case 'pesanan':
            $id_column = 'id_pesanan';
            break;
        case 'penitipan':
            $id_column = 'id_penitipan';
            break;
        case 'perawatan':
            $id_column = 'id_perawatan';
            break;
        case 'konsultasi':
            $id_column = 'id_konsultasi';
            break;
        default:
            return false;
    }

    // Build query based on type
    switch ($type) {
        case 'produk':
            $query = "UPDATE produk SET 
                     nama_produk = '" . mysqli_real_escape_string($conn, $sanitized_data['nama_produk'] ?? '') . "',
                     kategori = '" . mysqli_real_escape_string($conn, $sanitized_data['kategori'] ?? '') . "',
                     sub_kategori = '" . mysqli_real_escape_string($conn, $sanitized_data['sub_kategori'] ?? '') . "',
                     target_hewan = '" . mysqli_real_escape_string($conn, $sanitized_data['target_hewan'] ?? '') . "',
                     harga = '" . mysqli_real_escape_string($conn, $sanitized_data['harga'] ?? '') . "',
                     stok = '" . mysqli_real_escape_string($conn, $sanitized_data['stok'] ?? '') . "',
                     berat_gram = '" . mysqli_real_escape_string($conn, $sanitized_data['berat_gram'] ?? '') . "',
                     foto_utama = '" . mysqli_real_escape_string($conn, $image) . "',
                     deskripsi = '" . mysqli_real_escape_string($conn, $sanitized_data['deskripsi'] ?? '') . "'
                     WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            break;

        case 'dokter_hewan':
            $query = "UPDATE dokter_hewan SET 
                     nama_dokter = '" . mysqli_real_escape_string($conn, $sanitized_data['nama_dokter'] ?? '') . "',
                     nomor_lisensi = '" . mysqli_real_escape_string($conn, $sanitized_data['nomor_lisensi'] ?? '') . "',
                     spesialisasi = '" . mysqli_real_escape_string($conn, $sanitized_data['spesialisasi'] ?? '') . "',
                     email = '" . mysqli_real_escape_string($conn, $sanitized_data['email'] ?? '') . "',
                     nomor_telepon = '" . mysqli_real_escape_string($conn, $sanitized_data['nomor_telepon'] ?? '') . "',
                     tarif_konsultasi = '" . mysqli_real_escape_string($conn, $sanitized_data['tarif_konsultasi'] ?? '') . "'";
            if ($image) {
                $query .= ", foto_dokter = '$image'";
            }
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            break;

        case 'pelanggan':
            $query = "UPDATE pelanggan SET 
                     nama_lengkap = '" . mysqli_real_escape_string($conn, $sanitized_data['nama_lengkap'] ?? '') . "',
                     email = '" . mysqli_real_escape_string($conn, $sanitized_data['email'] ?? '') . "',
                     nomor_telepon = '" . mysqli_real_escape_string($conn, $sanitized_data['nomor_telepon'] ?? '') . "',
                     alamat = '" . mysqli_real_escape_string($conn, $sanitized_data['alamat'] ?? '') . "',
                     status = '" . mysqli_real_escape_string($conn, $sanitized_data['status'] ?? '') . "'";
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            break;

        case 'anabul':
            $query = "UPDATE anabul SET 
                     nama_hewan = '" . mysqli_real_escape_string($conn, $sanitized_data['nama_hewan'] ?? '') . "',
                     spesies = '" . mysqli_real_escape_string($conn, $sanitized_data['spesies'] ?? '') . "',
                     ras = '" . mysqli_real_escape_string($conn, $sanitized_data['ras'] ?? '') . "',
                     umur_tahun = '" . mysqli_real_escape_string($conn, $sanitized_data['umur_tahun'] ?? '') . "',
                     umur_bulan = '" . mysqli_real_escape_string($conn, $sanitized_data['umur_bulan'] ?? '') . "',
                     berat_kg = '" . mysqli_real_escape_string($conn, $sanitized_data['berat_kg'] ?? '') . "',
                     jenis_kelamin = '" . mysqli_real_escape_string($conn, $sanitized_data['jenis_kelamin'] ?? '') . "',
                     warna = '" . mysqli_real_escape_string($conn, $sanitized_data['warna'] ?? '') . "',
                     ciri_khusus = '" . mysqli_real_escape_string($conn, $sanitized_data['ciri_khusus'] ?? '') . "',
                     riwayat_penyakit = '" . mysqli_real_escape_string($conn, $sanitized_data['riwayat_penyakit'] ?? '') . "',
                     alergi = '" . mysqli_real_escape_string($conn, $sanitized_data['alergi'] ?? '') . "',
                     id_pelanggan = '" . mysqli_real_escape_string($conn, $sanitized_data['id_pelanggan'] ?? '') . "'";
            if ($image) {
                $query .= ", foto_utama = '$image'";
            }
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            break;

        case 'pesanan':
            $query = "UPDATE pesanan SET 
                     status_pesanan = '" . mysqli_real_escape_string($conn, $sanitized_data['status_pesanan'] ?? '') . "',
                     status_pembayaran = '" . mysqli_real_escape_string($conn, $sanitized_data['status_pembayaran'] ?? '') . "'";

            // Add catatan_admin if provided
            if (isset($sanitized_data['catatan_admin'])) {
                $query .= ", catatan_admin = '" . mysqli_real_escape_string($conn, $sanitized_data['catatan_admin']) . "'";
            }

            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            break;

        case 'penitipan':
            $allowed_status = ['pending', 'diproses', 'selesai', 'dibatalkan'];
            $status = isset($sanitized_data['status']) && trim($sanitized_data['status']) !== '' ? strtolower(trim($sanitized_data['status'])) : 'pending';
            if ($status === 'diproses') {
                $status = 'checked_in';
            } elseif ($status === 'selesai') {
                $status = 'checked_out';
            }
            if (!in_array($sanitized_data['status'], $allowed_status)) {
                $status = 'pending';
            }
            $query = "UPDATE penitipan SET 
                     id_anabul = '" . mysqli_real_escape_string($conn, $sanitized_data['id_anabul'] ?? '') . "',
                     tanggal_checkin = '" . mysqli_real_escape_string($conn, $sanitized_data['tanggal_checkin'] ?? '') . "',
                     tanggal_checkout = '" . mysqli_real_escape_string($conn, $sanitized_data['tanggal_checkout'] ?? '') . "',
                     jumlah_hari = '" . mysqli_real_escape_string($conn, $sanitized_data['jumlah_hari'] ?? '') . "',
                     nomor_kandang = '" . mysqli_real_escape_string($conn, $sanitized_data['nomor_kandang'] ?? '') . "',
                     status_checkin = '" . mysqli_real_escape_string($conn, $status) . "',
                     catatan_checkin = '" . mysqli_real_escape_string($conn, $sanitized_data['catatan_khusus'] ?? '') . "'";
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            mysqli_query($conn, $query);
            break;

        case 'perawatan':
            $query = "UPDATE perawatan SET 
                     id_anabul = '" . mysqli_real_escape_string($conn, $sanitized_data['id_anabul'] ?? '') . "',
                     paket_perawatan = '" . mysqli_real_escape_string($conn, $sanitized_data['paket_perawatan'] ?? '') . "',
                     tanggal_perawatan = '" . mysqli_real_escape_string($conn, $sanitized_data['tanggal_perawatan'] ?? '') . "',
                     waktu_mulai = '" . mysqli_real_escape_string($conn, $sanitized_data['waktu_mulai'] ?? '') . "',
                     catatan_perawatan = '" . mysqli_real_escape_string($conn, $sanitized_data['catatan'] ?? '') . "',
                     status_pesanan = '" . mysqli_real_escape_string($conn, $sanitized_data['status_pesanan'] ?? '') . "'";
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            mysqli_query($conn, $query);
            // Update status_pembayaran di tabel pesanan
            // Dapatkan id_pesanan dari id_pesanan_layanan
            $id_perawatan = intval($sanitized_data['id'] ?? 0);
            $q = mysqli_query($conn, "SELECT id_pesanan_layanan FROM perawatan WHERE id_perawatan = $id_perawatan");
            $row = mysqli_fetch_assoc($q);
            if ($row) {
                $id_pesanan_layanan = $row['id_pesanan_layanan'];
                $q2 = mysqli_query($conn, "SELECT id_pesanan FROM pesanan_layanan WHERE id_detail = $id_pesanan_layanan");
                $row2 = mysqli_fetch_assoc($q2);
                if ($row2) {
                    $id_pesanan = $row2['id_pesanan'];
                    $status_pembayaran = mysqli_real_escape_string($conn, $sanitized_data['status_pembayaran'] ?? 'pending');
                    mysqli_query($conn, "UPDATE pesanan SET status_pembayaran = '$status_pembayaran' WHERE id_pesanan = $id_pesanan");
                }
            }
            return mysqli_affected_rows($conn);
            break;

        case 'konsultasi':
            $query = "UPDATE konsultasi SET 
                     id_dokter = '" . mysqli_real_escape_string($conn, $sanitized_data['id_dokter'] ?? '') . "',
                     id_anabul = '" . mysqli_real_escape_string($conn, $sanitized_data['id_anabul'] ?? '') . "',
                     keluhan_utama = '" . mysqli_real_escape_string($conn, $sanitized_data['keluhan_utama'] ?? '') . "',
                     gejala = '" . mysqli_real_escape_string($conn, $sanitized_data['gejala'] ?? '') . "',
                     tanggal_kontrol = '" . mysqli_real_escape_string($conn, $sanitized_data['tanggal_konsultasi'] ?? '') . "',
                     tingkat_keparahan = '" . mysqli_real_escape_string($conn, $sanitized_data['tingkat_keparahan'] ?? '') . "',
                     durasi_gejala = '" . mysqli_real_escape_string($conn, $sanitized_data['durasi_gejala'] ?? '') . "',
                     status_konsultasi = '" . mysqli_real_escape_string($conn, $sanitized_data['status'] ?? '') . "'";
            $query .= " WHERE $id_column = " . intval($sanitized_data['id'] ?? 0);
            mysqli_query($conn, $query);
            // Update status_pembayaran di tabel pesanan jika ada
            if (isset($sanitized_data['status_pembayaran'])) {
                $id_konsultasi = intval($sanitized_data['id'] ?? 0);
                $q = mysqli_query($conn, "SELECT id_pesanan FROM konsultasi WHERE id_konsultasi = $id_konsultasi");
                $row = mysqli_fetch_assoc($q);
                if ($row) {
                    $id_pesanan = $row['id_pesanan'];
                    $status_pembayaran = mysqli_real_escape_string($conn, $sanitized_data['status_pembayaran']);
                    mysqli_query($conn, "UPDATE pesanan SET status_pembayaran = '$status_pembayaran' WHERE id_pesanan = $id_pesanan");
                }
            }
            return mysqli_affected_rows($conn);
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
        'dokter_hewan' => 'id_dokter',
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
            // First delete related records in pesanan_produk
            $query = "DELETE FROM pesanan_produk WHERE id_produk = $id";
            mysqli_query($conn, $query);

            // Then delete the product
            $query = "DELETE FROM produk WHERE $id_column = $id";
            break;

        case 'dokter_hewan':
            $query = "DELETE FROM dokter_hewan WHERE $id_column = $id";
            break;

        case 'pelanggan':
            // First delete related records
            $query = "DELETE FROM anabul WHERE id_pelanggan = $id";
            mysqli_query($conn, $query);

            $query = "DELETE FROM pesanan WHERE id_pelanggan = $id";
            mysqli_query($conn, $query);

            $query = "DELETE FROM penitipan WHERE id_pesanan IN (SELECT id_pesanan FROM pesanan WHERE id_pelanggan = $id)";
            mysqli_query($conn, $query);

            $query = "DELETE FROM perawatan WHERE id_anabul IN (SELECT id_anabul FROM anabul WHERE id_pelanggan = $id)";
            mysqli_query($conn, $query);

            $query = "DELETE FROM konsultasi WHERE id_pesanan IN (SELECT id_pesanan FROM pesanan WHERE id_pelanggan = $id)";
            mysqli_query($conn, $query);

            // Then delete the customer
            $query = "DELETE FROM pelanggan WHERE $id_column = $id";
            break;

        case 'anabul':
            // Hapus data terkait di konsultasi, penitipan, perawatan, pesanan_layanan
            $query = "DELETE FROM konsultasi WHERE id_anabul = $id";
            mysqli_query($conn, $query);

            $query = "DELETE FROM penitipan WHERE id_anabul = $id";
            mysqli_query($conn, $query);

            $query = "DELETE FROM perawatan WHERE id_anabul = $id";
            mysqli_query($conn, $query);

            $query = "DELETE FROM pesanan_layanan WHERE id_anabul = $id";
            mysqli_query($conn, $query);

            // Baru hapus anabul
            $query = "DELETE FROM anabul WHERE $id_column = $id";
            break;

        case 'pesanan':
            // First delete related records in the correct order to avoid foreign key constraint errors

            // 1. Delete from perawatan that references pesanan_layanan
            $query = "DELETE perawatan FROM perawatan 
                     INNER JOIN pesanan_layanan ON perawatan.id_pesanan_layanan = pesanan_layanan.id_detail 
                     WHERE pesanan_layanan.id_pesanan = $id";
            mysqli_query($conn, $query);

            // 2. Delete from pesanan_produk (has CASCADE, but we'll do it explicitly)
            $query = "DELETE FROM pesanan_produk WHERE id_pesanan = $id";
            mysqli_query($conn, $query);

            // 3. Delete from pesanan_layanan (has CASCADE, but we'll do it explicitly)
            $query = "DELETE FROM pesanan_layanan WHERE id_pesanan = $id";
            mysqli_query($conn, $query);

            // 4. Delete from penitipan that references this pesanan
            $query = "DELETE FROM penitipan WHERE id_pesanan = $id";
            mysqli_query($conn, $query);

            // 5. Delete from konsultasi that references this pesanan
            $query = "DELETE FROM konsultasi WHERE id_pesanan = $id";
            mysqli_query($conn, $query);

            // Finally delete the order
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

function upload($file, $type, $target_hewan = null)
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
    $uploadDir = '../uploads/';
    switch ($type) {
        case 'produk':
            // Gunakan parameter target_hewan yang dikirim, bukan dari $_POST
            $target_folder = $target_hewan ? strtolower($target_hewan) : 'other';
            $uploadDir .= 'produk/' . $target_folder . '/';
            break;
        case 'dokter_hewan':
            $uploadDir .= 'dokter/';
            break;
        case 'anabul':
            $uploadDir .= 'anabul/';
            break;
        default:
            $uploadDir .= 'other/';
    }

    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file
    if (move_uploaded_file($tmpName, $uploadDir . $namaFileBaru)) {
        // Return the relative path from the admin directory
        switch ($type) {
            case 'produk':
                $target_folder = $target_hewan ? strtolower($target_hewan) : 'other';
                return 'uploads/produk/' . $target_folder . '/' . $namaFileBaru;
            case 'dokter_hewan':
                return 'uploads/dokter/' . $namaFileBaru;
            case 'anabul':
                return 'uploads/anabul/' . $namaFileBaru;
            default:
                return 'uploads/other/' . $namaFileBaru;
        }
    }

    return false;
}
