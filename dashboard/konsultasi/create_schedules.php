<?php
require_once '../../includes/db.php';

// Create automatic schedules for the next 7 days if not exists
for ($i = 0; $i < 7; $i++) {
    $tanggal = date('Y-m-d', strtotime("+$i days"));

    // Get all active doctors
    $dokter_query = "SELECT id_dokter FROM dokter_hewan WHERE status_dokter = 'aktif'";
    $dokter_result = mysqli_query($conn, $dokter_query);

    while ($dokter = mysqli_fetch_assoc($dokter_result)) {
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

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>