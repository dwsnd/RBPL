<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get booking ID
$id_konsultasi = isset($_POST['id_konsultasi']) ? intval($_POST['id_konsultasi']) : 0;

if ($id_konsultasi <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit();
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get booking details
    $query = "SELECT * FROM konsultasi WHERE id_konsultasi = '$id_konsultasi' AND id_pelanggan = '{$_SESSION['id_pelanggan']}'";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception('Booking not found or unauthorized');
    }

    $booking = mysqli_fetch_assoc($result);

    // Check if booking can be cancelled
    if ($booking['status_konsultasi'] !== 'pending') {
        throw new Exception('Only pending bookings can be cancelled');
    }

    // Update booking status
    $update_query = "UPDATE konsultasi SET status_konsultasi = 'cancelled' WHERE id_konsultasi = '$id_konsultasi'";
    if (!mysqli_query($conn, $update_query)) {
        throw new Exception('Failed to update booking status');
    }

    // Update schedule slot
    $update_slot = "UPDATE jadwal_konsultasi 
                   SET slot_terpakai = slot_terpakai - 1,
                       status_jadwal = CASE 
                           WHEN slot_terpakai - 1 < slot_tersedia THEN 'tersedia'
                           ELSE status_jadwal
                       END
                   WHERE id_dokter = '{$booking['id_dokter']}' 
                   AND tanggal = '{$booking['tanggal_konsultasi']}' 
                   AND waktu_mulai = '{$booking['waktu_konsultasi']}'";
    if (!mysqli_query($conn, $update_slot)) {
        throw new Exception('Failed to update schedule slot');
    }

    // Commit transaction
    mysqli_commit($conn);

    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);

} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>