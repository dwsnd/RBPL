<?php
require_once '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id_dokter']) || !isset($_GET['tanggal'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$id_dokter = mysqli_real_escape_string($conn, $_GET['id_dokter']);
$tanggal = mysqli_real_escape_string($conn, $_GET['tanggal']);

// Get available times for the selected doctor and date
$query = "SELECT waktu_mulai 
          FROM jadwal_konsultasi 
          WHERE id_dokter = '$id_dokter' 
          AND tanggal = '$tanggal' 
          AND status_jadwal = 'tersedia'
          AND slot_tersedia > slot_terpakai";

$result = mysqli_query($conn, $query);
$available_times = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $available_times[] = $row['waktu_mulai'];
    }
}

echo json_encode([
    'success' => true,
    'times' => $available_times
]);
?>