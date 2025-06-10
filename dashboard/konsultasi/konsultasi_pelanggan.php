<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Redirect jika belum login
if (!isset($_SESSION['id_pelanggan'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Fetch available doctors with their schedules
$dokter_data = [];
$dokter_query = "SELECT d.id_dokter, d.nama_dokter, d.spesialisasi, d.tarif_konsultasi, d.pengalaman_tahun,
                 GROUP_CONCAT(CONCAT(j.tanggal, ':', j.waktu_mulai) SEPARATOR ',') as jadwal_tersedia
                 FROM dokter_hewan d 
                 LEFT JOIN jadwal_konsultasi j ON d.id_dokter = j.id_dokter 
                 AND j.tanggal >= CURDATE() 
                 AND j.tanggal <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                 AND j.status_jadwal = 'tersedia'
                 AND j.slot_tersedia > j.slot_terpakai
                 WHERE d.status_dokter = 'aktif'
                 GROUP BY d.id_dokter";
$dokter_result = mysqli_query($conn, $dokter_query);
if ($dokter_result && mysqli_num_rows($dokter_result) > 0) {
    while ($row = mysqli_fetch_assoc($dokter_result)) {
        $dokter_data[] = $row;
    }
}

// Create automatic schedules for the next 7 days if not exists
for ($i = 0; $i < 7; $i++) {
    $tanggal = date('Y-m-d', strtotime("+$i days"));

    foreach ($dokter_data as $dokter) {
        $id_dokter = $dokter['id_dokter'];

        // Check if schedule already exists
        $cek_jadwal = "SELECT COUNT(*) as count FROM jadwal_konsultasi 
                       WHERE id_dokter = '$id_dokter' AND tanggal = '$tanggal'";
        $result_cek = mysqli_query($conn, $cek_jadwal);
        $count = mysqli_fetch_assoc($result_cek)['count'];

        if ($count == 0) {
            // Insert schedule for today
            $insert_jadwal = "INSERT INTO jadwal_konsultasi (id_dokter, tanggal, waktu_mulai, waktu_selesai, slot_tersedia, slot_terpakai, status_jadwal) VALUES 
                             ('$id_dokter', '$tanggal', '08:00:00', '10:00:00', 5, 0, 'tersedia'),
                             ('$id_dokter', '$tanggal', '10:00:00', '12:00:00', 5, 0, 'tersedia'),
                             ('$id_dokter', '$tanggal', '13:00:00', '15:00:00', 5, 0, 'tersedia'),
                             ('$id_dokter', '$tanggal', '15:00:00', '17:00:00', 5, 0, 'tersedia')";
            mysqli_query($conn, $insert_jadwal);
        }
    }
}

// Fetch user data
$user_data = [];
$query = "SELECT nama_lengkap, nomor_telepon FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

// Fetch user's pets data
$pets_data = [];
$pets_query = "SELECT id_anabul, nama_hewan, kategori_hewan, karakteristik FROM anabul WHERE id_pelanggan = '$id_pelanggan'";
$pets_result = mysqli_query($conn, $pets_query);
if ($pets_result && mysqli_num_rows($pets_result) > 0) {
    while ($row = mysqli_fetch_assoc($pets_result)) {
        $pets_data[] = $row;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $pet_name = mysqli_real_escape_string($conn, $_POST['pet_name']);
    $pet_category = mysqli_real_escape_string($conn, $_POST['pet_category']);
    $pet_special = mysqli_real_escape_string($conn, $_POST['pet_special']);
    $gejala_utama = mysqli_real_escape_string($conn, $_POST['gejala_utama']);
    $durasi_gejala = mysqli_real_escape_string($conn, $_POST['durasi_gejala']);
    $perubahan_perilaku = mysqli_real_escape_string($conn, $_POST['perubahan_perilaku']);
    $service_date = mysqli_real_escape_string($conn, $_POST['service_date']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $id_dokter = mysqli_real_escape_string($conn, $_POST['id_dokter']);
    $tingkat_keparahan = mysqli_real_escape_string($conn, $_POST['tingkat_keparahan']);
    $informasi_tambahan = mysqli_real_escape_string($conn, $_POST['informasi_tambahan']);
    $id_anabul_existing = isset($_POST['id_anabul_existing']) ? $_POST['id_anabul_existing'] : null;

    // Get doctor's consultation fee
    $dokter_fee_query = "SELECT tarif_konsultasi FROM dokter_hewan WHERE id_dokter = '$id_dokter'";
    $dokter_fee_result = mysqli_query($conn, $dokter_fee_query);
    $dokter_fee = 0;
    if ($dokter_fee_result && mysqli_num_rows($dokter_fee_result) > 0) {
        $dokter_fee = mysqli_fetch_assoc($dokter_fee_result)['tarif_konsultasi'];
    }

    // Check schedule availability
    $cek_jadwal = "SELECT * FROM jadwal_konsultasi 
                   WHERE id_dokter = '$id_dokter' 
                   AND tanggal = '$service_date' 
                   AND waktu_mulai = '$service_time' 
                   AND status_jadwal = 'tersedia'
                   AND slot_tersedia > slot_terpakai";
    $jadwal_result = mysqli_query($conn, $cek_jadwal);

    if (!$jadwal_result || mysqli_num_rows($jadwal_result) == 0) {
        $error_message = "Maaf, jadwal yang dipilih tidak tersedia. Silakan pilih jadwal lain.";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            $id_anabul = null;

            // If existing pet is selected
            if ($id_anabul_existing && $id_anabul_existing !== 'new') {
                $id_anabul = $id_anabul_existing;
            } else {
                // Insert new pet data
                $insert_pet = "INSERT INTO anabul (id_pelanggan, nama_hewan, kategori_hewan, karakteristik, created_at) 
                              VALUES ('$id_pelanggan', '$pet_name', '$pet_category', '$pet_special', NOW())";

                if (mysqli_query($conn, $insert_pet)) {
                    $id_anabul = mysqli_insert_id($conn);
                } else {
                    throw new Exception("Error inserting pet data: " . mysqli_error($conn));
                }
            }

            // Insert consultation data using prepared statement
            $insert_consultation = "INSERT INTO konsultasi 
                            (id_pelanggan, id_anabul, id_dokter, gejala_utama, durasi_gejala, 
                             perubahan_perilaku, tingkat_keparahan, informasi_tambahan,
                             tanggal_konsultasi, waktu_konsultasi, status_konsultasi, 
                             biaya_estimasi, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";

            $stmt = mysqli_prepare($conn, $insert_consultation);
            mysqli_stmt_bind_param(
                $stmt,
                "iiisssssssd",
                $id_pelanggan,
                $id_anabul,
                $id_dokter,
                $gejala_utama,
                $durasi_gejala,
                $perubahan_perilaku,
                $tingkat_keparahan,
                $informasi_tambahan,
                $service_date,
                $service_time,
                $dokter_fee
            );

            if (mysqli_stmt_execute($stmt)) {
                $id_konsultasi = mysqli_insert_id($conn);

                // Update slot terpakai in jadwal_konsultasi
                $update_slot = "UPDATE jadwal_konsultasi 
                               SET slot_terpakai = slot_terpakai + 1,
                                   status_jadwal = CASE 
                                       WHEN slot_terpakai + 1 >= slot_tersedia THEN 'penuh'
                                       ELSE 'tersedia'
                                   END
                               WHERE id_dokter = '$id_dokter' 
                               AND tanggal = '$service_date' 
                               AND waktu_mulai = '$service_time'";
                mysqli_query($conn, $update_slot);

                // Create initial medical record
                $insert_riwayat = "INSERT INTO riwayat_medis 
                                  (id_anabul, id_konsultasi, tanggal_kejadian, 
                                   jenis_kejadian, deskripsi, created_at)
                                  VALUES (?, ?, ?, 'sakit', ?, NOW())";
                $stmt_riwayat = mysqli_prepare($conn, $insert_riwayat);
                mysqli_stmt_bind_param(
                    $stmt_riwayat,
                    "iiss",
                    $id_anabul,
                    $id_konsultasi,
                    $service_date,
                    $gejala_utama
                );
                mysqli_stmt_execute($stmt_riwayat);

                // Commit transaction
                mysqli_commit($conn);

                // Set success message
                $_SESSION['success_message'] = "Konsultasi berhasil dijadwalkan! ID Konsultasi: " . $id_konsultasi;

                // Redirect to pesanan.php
                header('Location: pesanan.php');
                exit();
            } else {
                throw new Exception("Error inserting consultation data: " . mysqli_error($conn));
            }

        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Konsultasi Hewan - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        footer {
            padding: 40px 0;
        }

        .form-control:focus,
        select:focus,
        input:focus,
        textarea:focus {
            border-color: #fd7e14 !important;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25) !important;
        }

        select.form-control {
            color: #888;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23000' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 2.5rem !important;
        }

        select.form-control option {
            color: #888 !important;
        }

        select.form-control option:checked {
            color: #000 !important;
        }

        select.form-control option:first-child {
            color: #888 !important;
        }

        .pet-option {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin-bottom: 6px;
            padding: 8px 12px;
        }

        .pet-option .form-check {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .pet-option .form-check-input {
            margin: 0;
            margin-right: 8px;
            align-self: center;
        }

        .pet-option .form-check-label {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            flex: 1;
        }

        .pet-option .d-flex {
            margin: 0;
            padding: 0;
        }

        .pet-option .fas {
            font-size: 0.9rem;
        }

        .pet-option .text-muted {
            font-size: 0.85rem;
        }

        .pet-option .small {
            font-size: 0.8rem;
        }

        .pet-option:hover {
            background-color: #fff3cd;
            border-color: #ffc107;
        }

        .pet-option.selected {
            background-color: #fff3cd;
            border-color: #ffc107;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-section {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .section-title {
            color: #fd7e14;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #fff3cd;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 6px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
        }

        .form-control:read-only {
            background-color: #f8f9fa;
        }

        .btn-reset {
            padding: 6px 16px;
            border: 2px solid #fd7e14;
            color: #fd7e14;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-reset:hover {
            background-color: #fff3cd;
        }

        .btn-submit {
            padding: 6px 16px;
            background-color: #fd7e14;
            color: white;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-submit:hover {
            background-color: #e96e0a;
        }

        .row {
            margin-bottom: 8px;
        }

        .col-md-6 {
            padding: 0 8px;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .alert {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 6px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        .consultation-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .consultation-info .fas {
            color: #0066cc;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <!-- bagian form konsultasi -->
    <section id="booking-form" class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-lg overflow-hidden shadow-md border border-grey">
                <div class="bg-orange-500 text-white p-6 text-center">
                    <h3 class="text-lg font-semibold mb-2">Form Konsultasi Hewan</h3>
                    <p class="text-sm">Konsultasi kesehatan untuk hewan kesayangan Anda</p>
                </div>

                <div class="p-8">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="consultation-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Informasi Konsultasi:</strong>
                                <p class="mb-0 small mt-1">Konsultasi akan dilakukan oleh dokter hewan berpengalaman.
                                    Biaya konsultasi akan ditentukan setelah pemeriksaan awal.</p>
                            </div>
                        </div>
                    </div>

                    <form method="post" class="space-y-4" id="bookingForm" onsubmit="return validateForm()">
                        <!-- Data Pelanggan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user me-2"></i>Data Pelanggan
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" name="nama_lengkap" class="form-control"
                                        value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="tel" name="nomor_telepon" class="form-control"
                                        value="<?php echo htmlspecialchars($user_data['nomor_telepon']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Data Hewan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-paw me-2"></i>Data Hewan Peliharaan
                            </h5>

                            <?php if (!empty($pets_data)): ?>
                                <div class="mb-4">
                                    <label class="form-label">Pilih Hewan Peliharaan</label>
                                    <div class="pet-selection">
                                        <?php foreach ($pets_data as $pet): ?>
                                            <div class="pet-option">
                                                <div class="form-check item">
                                                    <input class="form-check-input accent-orange-500" type="radio"
                                                        name="id_anabul_existing" value="<?php echo $pet['id_anabul']; ?>"
                                                        id="pet_<?php echo $pet['id_anabul']; ?>"
                                                        data-name="<?php echo htmlspecialchars($pet['nama_hewan']); ?>"
                                                        data-category="<?php echo htmlspecialchars($pet['kategori_hewan']); ?>"
                                                        data-special="<?php echo htmlspecialchars($pet['karakteristik']); ?>">
                                                    <label class="form-check-label" for="pet_<?php echo $pet['id_anabul']; ?>">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-paw me-2 text-orange-500"></i>
                                                            <div>
                                                                <span
                                                                    class="font-semibold"><?php echo htmlspecialchars($pet['nama_hewan']); ?></span>
                                                                <span
                                                                    class="text-muted ms-2">(<?php echo htmlspecialchars($pet['kategori_hewan']); ?>)</span>
                                                                <?php if ($pet['karakteristik']): ?>
                                                                    <div class="text-muted small mt-1">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        <?php echo htmlspecialchars($pet['karakteristik']); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="pet-option">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="id_anabul_existing"
                                                    value="new" id="pet_new">
                                                <label class="form-check-label" for="pet_new">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-plus-circle me-2 text-orange-500"></i>
                                                        <span class="font-semibold">Tambah Hewan Baru</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Form input manual untuk hewan baru -->
                            <div id="manual-pet-form" <?php echo !empty($pets_data) ? 'style="display: none;"' : ''; ?>>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Hewan Peliharaan</label>
                                        <input type="text" name="pet_name" id="pet_name" class="form-control"
                                            placeholder="Masukkan nama hewan peliharaan" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Hewan</label>
                                        <select name="pet_category" id="pet_category" class="form-control" <?php echo empty($pets_data) ? 'required' : ''; ?>>
                                            <option value="">Pilih kategori hewan</option>
                                            <option value="kucing">Kucing</option>
                                            <option value="anjing">Anjing</option>
                                            <option value="kelinci">Kelinci</option>
                                            <option value="hamster">Hamster</option>
                                            <option value="burung">Burung</option>
                                            <option value="reptil">Reptil</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ciri-Ciri Khusus (Opsional)</label>
                                        <input type="text" name="pet_special" id="pet_special" class="form-control"
                                            placeholder="Contoh: Warna bulu, ukuran, atau kondisi khusus">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keluhan Hewan Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-stethoscope me-2"></i>Keluhan Hewan
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Gejala Utama <span class="text-danger">*</span></label>
                                    <textarea name="gejala_utama" class="form-control" rows="3" required
                                        placeholder="Jelaskan gejala utama yang dialami hewan peliharaan Anda (contoh: tidak mau makan, lemas, muntah, diare, batuk, dll)"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Durasi Gejala <span class="text-danger">*</span></label>
                                    <select name="durasi_gejala" class="form-control" required>
                                        <option value="">Pilih durasi gejala</option>
                                        <option value="kurang_dari_1_hari">Kurang dari 1 hari</option>
                                        <option value="1-3_hari">1-3 hari</option>
                                        <option value="4-7_hari">4-7 hari</option>
                                        <option value="1-2_minggu">1-2 minggu</option>
                                        <option value="lebih_dari_2_minggu">Lebih dari 2 minggu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tingkat Keparahan</label>
                                    <select name="tingkat_keparahan" class="form-control">
                                        <option value="">Pilih tingkat keparahan</option>
                                        <option value="ringan">Ringan</option>
                                        <option value="sedang">Sedang</option>
                                        <option value="berat">Berat</option>
                                        <option value="sangat_berat">Sangat Berat</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Perubahan Perilaku <span
                                            class="text-danger">*</span></label>
                                    <textarea name="perubahan_perilaku" class="form-control" rows="3" required
                                        placeholder="Jelaskan perubahan perilaku hewan (contoh: lebih pendiam, agresif, tidak aktif, susah tidur, dll)"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Informasi Tambahan</label>
                                    <textarea name="informasi_tambahan" class="form-control" rows="2"
                                        placeholder="Informasi tambahan yang mungkin berguna untuk dokter (riwayat penyakit, obat yang sedang dikonsumsi, dll)"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal Konsultasi Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-calendar me-2"></i>Jadwal Konsultasi
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Konsultasi <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="service_date" class="form-control"
                                        min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Konsultasi <span
                                            class="text-danger">*</span></label>
                                    <select name="service_time" id="service_time" class="form-control" required>
                                        <option value="">Pilih waktu konsultasi</option>
                                        <option value="08:00:00">08:00 - 10:00 (Pagi)</option>
                                        <option value="10:00:00">10:00 - 12:00 (Siang)</option>
                                        <option value="13:00:00">13:00 - 15:00 (Sore)</option>
                                        <option value="15:00:00">15:00 - 17:00 (Sore Akhir)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pilih Dokter <span class="text-danger">*</span></label>
                                    <select name="id_dokter" class="form-control" required>
                                        <option value="">Pilih dokter hewan</option>
                                        <?php foreach ($dokter_data as $dokter): ?>
                                            <option value="<?php echo $dokter['id_dokter']; ?>"
                                                data-tarif="<?php echo $dokter['tarif_konsultasi']; ?>"
                                                data-jadwal="<?php echo htmlspecialchars($dokter['jadwal_tersedia']); ?>">
                                                <?php echo htmlspecialchars($dokter['nama_dokter']); ?>
                                                (<?php echo htmlspecialchars($dokter['spesialisasi']); ?>) -
                                                Rp <?php echo number_format($dokter['tarif_konsultasi'], 0, ',', '.'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted mt-1">Pilih dokter sesuai dengan spesialisasi yang
                                        dibutuhkan</small>
                                </div>
                                <div class="col-12">
                                    <div class="consultation-info">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-money-bill-wave me-2 mt-1"></i>
                                            <div>
                                                <strong>Informasi Biaya:</strong>
                                                <p class="mb-0 small mt-1">Biaya konsultasi akan ditentukan berdasarkan
                                                    dokter yang dipilih. Pembayaran dapat dilakukan setelah konsultasi
                                                    selesai.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 px-3">
                            <button type="reset" class="btn-reset">
                                <i class="fas fa-redo me-2"></i>Reset Form
                            </button>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-calendar-check me-2"></i>Jadwalkan Konsultasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <?php require '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            const form = document.getElementById('bookingForm');
            const errorMessages = [];

            // Check pet selection
            const selectedPet = form.querySelector('input[name="id_anabul_existing"]:checked');
            if (!selectedPet) {
                errorMessages.push('Silakan pilih hewan peliharaan atau tambahkan hewan baru');
            } else if (selectedPet.value === 'new') {
                const petName = form.querySelector('#pet_name').value.trim();
                const petCategory = form.querySelector('#pet_category').value;
                if (!petName) errorMessages.push('Nama hewan peliharaan harus diisi');
                if (!petCategory) errorMessages.push('Kategori hewan harus dipilih');
            }

            // Check consultation details
            const gejalaUtama = form.querySelector('textarea[name="gejala_utama"]').value.trim();
            const durasiGejala = form.querySelector('select[name="durasi_gejala"]').value;
            const perubahanPerilaku = form.querySelector('textarea[name="perubahan_perilaku"]').value.trim();
            const serviceDate = form.querySelector('input[name="service_date"]').value;
            const serviceTime = form.querySelector('select[name="service_time"]').value;
            const idDokter = form.querySelector('select[name="id_dokter"]').value;

            if (!gejalaUtama) errorMessages.push('Gejala utama harus diisi');
            if (!durasiGejala) errorMessages.push('Durasi gejala harus dipilih');
            if (!perubahanPerilaku) errorMessages.push('Perubahan perilaku harus diisi');
            if (!serviceDate) errorMessages.push('Tanggal konsultasi harus dipilih');
            if (!serviceTime) errorMessages.push('Waktu konsultasi harus dipilih');
            if (!idDokter) errorMessages.push('Dokter harus dipilih');

            // Validate minimum text length
            if (gejalaUtama && gejalaUtama.length < 10) {
                errorMessages.push('Gejala utama minimal 10 karakter');
            }
            if (perubahanPerilaku && perubahanPerilaku.length < 10) {
                errorMessages.push('Perubahan perilaku minimal 10 karakter');
            }

            if (errorMessages.length > 0) {
                alert('Mohon lengkapi data berikut:\n' + errorMessages.join('\n'));
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('bookingForm');
            const petRadios = form.querySelectorAll('input[name="id_anabul_existing"]');
            const manualPetForm = document.getElementById('manual-pet-form');
            const petNameInput = document.getElementById('pet_name');
            const petCategorySelect = document.getElementById('pet_category');
            const petSpecialInput = document.getElementById('pet_special');
            const dokterSelect = form.querySelector('select[name="id_dokter"]');
            const consultationInfo = form.querySelector('.consultation-info');

            // Add required field indicators
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                const label = field.previousElementSibling;
                if (label && label.classList.contains('form-label')) {
                    const hasAsterisk = label.innerHTML.includes('*');
                    if (!hasAsterisk) {
                        label.innerHTML += ' <span class="text-danger">*</span>';
                    }
                }
            });

            // Function to handle select color changes
            function handleSelectColor(select) {
                if (select) {
                    select.style.color = select.value ? '#000' : '#888';
                }
            }

            // Add event listeners for all select elements
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function () {
                    handleSelectColor(this);
                });
                handleSelectColor(select);
            });

            // Function to load available times based on doctor and date
            function loadAvailableTimes(idDokter, tanggal) {
                const timeSelect = document.getElementById('service_time');

                // Reset options
                Array.from(timeSelect.options).forEach((option, index) => {
                    if (index > 0) { // Skip first option
                        option.disabled = false;
                        option.style.color = '#000';
                    }
                });

                if (idDokter && tanggal) {
                    // Make AJAX call to get available times
                    fetch(`get_available_times.php?id_dokter=${idDokter}&tanggal=${tanggal}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const availableTimes = data.times;

                                // Update time options
                                Array.from(timeSelect.options).forEach(option => {
                                    if (option.value === '') return; // Skip default option
                                    const isAvailable = availableTimes.includes(option.value);
                                    option.disabled = !isAvailable;
                                    option.style.color = isAvailable ? '#000' : '#ccc';
                                });
                            }
                        })
                        .catch(error => console.error('Error loading available times:', error));
                }
            }

            // Update doctor selection handler
            if (dokterSelect) {
                dokterSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const tarif = selectedOption.dataset.tarif;

                    if (tarif) {
                        const formattedTarif = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(tarif);

                        consultationInfo.querySelector('p').innerHTML =
                            `Biaya konsultasi dengan dokter yang dipilih: ${formattedTarif}.<br>Pembayaran dapat dilakukan setelah konsultasi selesai.`;
                    }

                    const selectedDate = dateInput ? dateInput.value : '';
                    loadAvailableTimes(this.value, selectedDate);
                });
            }

            // Update date selection handler
            if (dateInput) {
                dateInput.addEventListener('change', function () {
                    const selectedDoctor = dokterSelect ? dokterSelect.value : '';
                    loadAvailableTimes(selectedDoctor, this.value);
                });
            }

            // Handle pet selection
            petRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Remove selected class from all options
                    document.querySelectorAll('.pet-option').forEach(option => {
                        option.classList.remove('selected');
                    });

                    // Add selected class to current option
                    this.closest('.pet-option').classList.add('selected');

                    if (this.value === 'new') {
                        // Show manual form
                        manualPetForm.style.display = 'block';
                        // Make fields required
                        petNameInput.required = true;
                        petCategorySelect.required = true;
                        // Clear fields
                        petNameInput.value = '';
                        petCategorySelect.value = '';
                        petSpecialInput.value = '';
                        handleSelectColor(petCategorySelect);
                    } else {
                        // Hide manual form and populate with existing data
                        manualPetForm.style.display = 'none';
                        // Make fields not required
                        petNameInput.required = false;
                        petCategorySelect.required = false;

                        // Fill with selected pet data
                        petNameInput.value = this.dataset.name || '';
                        petCategorySelect.value = this.dataset.category || '';
                        petSpecialInput.value = this.dataset.special || '';

                        // Update select color
                        handleSelectColor(petCategorySelect);
                    }
                });
            });

            // If no pets exist, show manual form by default
            <?php if (empty($pets_data)): ?>
                if (manualPetForm) {
                    manualPetForm.style.display = 'block';
                }
            <?php endif; ?>

            // Add input validation styles
            form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('invalid', function (e) {
                    e.preventDefault();
                    this.classList.add('is-invalid');
                });

                input.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('change', function () {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Character counter for textareas
            const textareas = form.querySelectorAll('textarea[required]');
            textareas.forEach(textarea => {
                const wrapper = textarea.parentNode;
                const counter = document.createElement('small');
                counter.className = 'text-muted mt-1';
                counter.style.display = 'block';
                wrapper.appendChild(counter);

                function updateCounter() {
                    const length = textarea.value.length;
                    const minLength = textarea.name === 'gejala_utama' || textarea.name === 'perubahan_perilaku' ? 10 : 0;
                    counter.textContent = `${length} karakter${minLength > 0 ? `