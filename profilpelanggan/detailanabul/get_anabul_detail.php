<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id_pelanggan'])) {
    echo '<p class="text-red-500">Unauthorized access.</p>';
    exit();
}

// Database connection
require_once '../../includes/db.php';

if (isset($_GET['id'])) {
    $anabul_id = (int) $_GET['id'];
    $pelanggan_id = $_SESSION['id_pelanggan'];

    // Get anabul data with owner verification
    $query = "SELECT * FROM anabul WHERE id_anabul = ? AND id_pelanggan = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$anabul_id, $pelanggan_id]);
    $anabul = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anabul) {
        echo '<p class="text-red-500">Data tidak ditemukan.</p>';
        exit();
    }
    ?>

    <div class="space-y-5">
        <!-- Photo Section -->
        <div class="text-center">
            <div class="w-40 h-40 mx-auto bg-gray-200 rounded-lg overflow-hidden border-2 border-gray-300 shadow-sm">
                <?php if (!empty($anabul['foto_utama'])): ?>
                    <img src="../../uploads/anabul/<?php echo htmlspecialchars($anabul['foto_utama']); ?>"
                        alt="<?php echo htmlspecialchars($anabul['nama_hewan']); ?>"
                        class="w-full h-full object-cover object-center">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-paw text-gray-400 text-5xl"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mt-4">
                <?php echo htmlspecialchars($anabul['nama_hewan']); ?>
            </h3>
            <p class="text-gray-600">
                <?php echo htmlspecialchars($anabul['kategori_hewan']); ?>
                <?php if (!empty($anabul['jenis_ras'])): ?>
                    - <?php echo htmlspecialchars($anabul['jenis_ras']); ?>
                <?php endif; ?>
            </p>
        </div>

        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Informasi Dasar
                </h4>
                <div class="space-y-2.5 text-sm">
                    <?php if (!empty($anabul['umur_tahun']) || !empty($anabul['umur_bulan'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Umur:</span>
                            <span class="font-medium">
                                <?php
                                $umur_parts = [];
                                if (!empty($anabul['umur_tahun']) && $anabul['umur_tahun'] > 0) {
                                    $umur_parts[] = $anabul['umur_tahun'] . ' tahun';
                                }
                                if (!empty($anabul['umur_bulan']) && $anabul['umur_bulan'] > 0) {
                                    $umur_parts[] = $anabul['umur_bulan'] . ' bulan';
                                }
                                echo implode(' ', $umur_parts);
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($anabul['berat'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Berat:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($anabul['berat']); ?> kg</span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($anabul['jenis_kelamin'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis Kelamin:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($anabul['jenis_kelamin']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Terdaftar:</span>
                        <span class="font-medium">
                            <?php echo date('d M Y', strtotime($anabul['created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Health & Characteristics -->
            <div class="space-y-3">
                <?php if (!empty($anabul['riwayat_kesehatan'])): ?>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                            Riwayat Kesehatan
                        </h4>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($anabul['riwayat_kesehatan'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($anabul['karakteristik'])): ?>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            Karakteristik Unik
                        </h4>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($anabul['karakteristik'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-3 pt-4 border-t border-gray-200">
            <button onclick="editAnabul(<?php echo $anabul['id_anabul']; ?>)"
                class="px-5 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2 text-sm">
                <i class="fas fa-edit"></i>
                Edit Data
            </button>
            <button onclick="closeAnabulModal()"
                class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Tutup
            </button>
        </div>
    </div>

    <?php
} else {
    echo '<p class="text-red-500">ID tidak valid.</p>';
}
?>