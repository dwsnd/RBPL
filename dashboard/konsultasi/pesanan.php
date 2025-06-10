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

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Konsultasi - Ling-Ling Pet Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .booking-card {
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .pet-info {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 0.8rem;
        }

        .doctor-info {
            border-left: 3px solid #fd7e14;
            padding-left: 1rem;
        }

        .symptoms {
            max-height: 100px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php require '../../includes/header.php'; ?>

    <!-- Main Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Riwayat Konsultasi</h2>
                    <p class="text-gray-600">Lihat riwayat konsultasi hewan peliharaan Anda</p>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Booking List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($bookings)): ?>
                        <div class="col-span-full">
                            <div class="text-center py-8">
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
                            <div class="booking-card bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-6">
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Konsultasi #<?php echo $booking['id_konsultasi']; ?>
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                <?php echo date('d M Y', strtotime($booking['tanggal_konsultasi'])); ?>
                                                <?php echo date('H:i', strtotime($booking['waktu_konsultasi'])); ?>
                                            </p>
                                        </div>
                                        <?php echo formatStatus($booking['status_konsultasi']); ?>
                                    </div>

                                    <!-- Pet Info -->
                                    <div class="pet-info mb-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-paw text-orange-500 me-2"></i>
                                            <div>
                                                <h4 class="font-semibold text-gray-800">
                                                    <?php echo htmlspecialchars($booking['nama_hewan']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-600">
                                                    <?php echo htmlspecialchars($booking['kategori_hewan']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Doctor Info -->
                                    <div class="doctor-info mb-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-user-md text-orange-500 me-2"></i>
                                            <div>
                                                <h4 class="font-semibold text-gray-800">
                                                    <?php echo htmlspecialchars($booking['nama_dokter']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-600">
                                                    <?php echo htmlspecialchars($booking['spesialisasi']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Symptoms -->
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Gejala Utama:</h4>
                                        <div class="symptoms text-sm text-gray-600">
                                            <?php echo nl2br(htmlspecialchars($booking['gejala_utama'])); ?>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex justify-between items-center pt-4 border-t">
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
        </div>
    </section>

    <!-- footer -->
    <?php require '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelBooking(id) {
            if (confirm('Apakah Anda yakin ingin membatalkan konsultasi ini?')) {
                // Add AJAX call to cancel booking
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
</body>

</html>