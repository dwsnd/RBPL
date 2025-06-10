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

// Fetch user data
$user_data = [];
$query = "SELECT nama_lengkap FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

// Fetch booking history
$bookings = [];
$booking_query = "SELECT k.*, d.nama_dokter, d.spesialisasi, a.nama_hewan, a.kategori_hewan
                  FROM konsultasi k
                  JOIN dokter_hewan d ON k.id_dokter = d.id_dokter
                  JOIN anabul a ON k.id_anabul = a.id_anabul
                  WHERE k.id_pelanggan = '$id_pelanggan'
                  ORDER BY k.tanggal_konsultasi DESC, k.waktu_konsultasi DESC";
$booking_result = mysqli_query($conn, $booking_query);
if ($booking_result && mysqli_num_rows($booking_result) > 0) {
    while ($row = mysqli_fetch_assoc($booking_result)) {
        $bookings[] = $row;
    }
}

// Function to format status
function formatStatus($status)
{
    $status_classes = [
        'pending' => 'bg-warning',
        'confirmed' => 'bg-success',
        'completed' => 'bg-info',
        'cancelled' => 'bg-danger'
    ];

    $status_labels = [
        'pending' => 'Menunggu Konfirmasi',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];

    $class = isset($status_classes[$status]) ? $status_classes[$status] : 'bg-secondary';
    $label = isset($status_labels[$status]) ? $status_labels[$status] : ucfirst($status);

    return "<span class='badge $class'>$label</span>";
}
?>

<div class="w-full bg-white rounded-lg shadow-md p-6 border border-grey-100">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Riwayat Konsultasi</h2>
        <p class="text-gray-600">Lihat riwayat konsultasi hewan peliharaan Anda</p>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Booking List -->
    <div class="booking-grid">
        <?php if (empty($bookings)): ?>
            <div class="col-12">
                <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Riwayat Konsultasi</h3>
                    <p class="text-gray-500 mb-4">Anda belum memiliki riwayat konsultasi</p>
                    <a href="konsultasi_pelanggan.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Buat Konsultasi Baru
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card bg-white rounded-lg shadow-sm h-100 border border-gray-100">
                    <div class="p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 mb-1 text-gray-800">
                                    Konsultasi #<?php echo $booking['id_konsultasi']; ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-0">
                                    <?php echo date('d M Y', strtotime($booking['tanggal_konsultasi'])); ?>
                                    <?php echo date('H:i', strtotime($booking['waktu_konsultasi'])); ?>
                                </p>
                            </div>
                            <?php echo formatStatus($booking['status_konsultasi']); ?>
                        </div>

                        <!-- Pet Info -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-paw text-orange-500 me-2"></i>
                                <div>
                                    <h4 class="h6 mb-0 text-gray-800">
                                        <?php echo htmlspecialchars($booking['nama_hewan']); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-0">
                                        <?php echo htmlspecialchars($booking['kategori_hewan']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Info -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-md text-orange-500 me-2"></i>
                                <div>
                                    <h4 class="h6 mb-0 text-gray-800">
                                        <?php echo htmlspecialchars($booking['nama_dokter']); ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-0">
                                        <?php echo htmlspecialchars($booking['spesialisasi']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Symptoms -->
                        <div class="mb-3">
                            <h4 class="h6 text-gray-700 mb-2">Gejala Utama:</h4>
                            <div class="text-sm text-gray-600">
                                <?php echo nl2br(htmlspecialchars($booking['gejala_utama'])); ?>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Rp <?php echo number_format($booking['biaya_estimasi'], 0, ',', '.'); ?>
                            </div>
                            <?php if ($booking['status_konsultasi'] === 'pending'): ?>
                                <button class="btn btn-sm btn-danger"
                                    onclick="cancelBooking(<?php echo $booking['id_konsultasi']; ?>)">
                                    <i class="fas fa-times me-1"></i>Batalkan
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function cancelBooking(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan konsultasi ini?')) {
            fetch('cancel_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_konsultasi=' + id
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat membatalkan konsultasi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membatalkan konsultasi');
                });
        }
    }
</script>