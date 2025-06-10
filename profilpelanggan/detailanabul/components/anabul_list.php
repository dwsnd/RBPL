<!-- Data Anabul List -->
<div class="space-y-4">
    <?php foreach ($data_anabul as $anabul): ?>
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Pet Photo -->
                    <div
                        class="w-28 h-28 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 border-2 border-gray-300 shadow-sm">
                        <?php if (!empty($anabul['foto_utama'])): ?>
                            <img src="../../uploads/anabul/<?php echo htmlspecialchars($anabul['foto_utama']); ?>"
                                alt="<?php echo htmlspecialchars($anabul['nama_hewan']); ?>"
                                class="w-full h-full object-contain object-fit object-center">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-paw text-gray-400 text-2xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pet Info -->
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg">
                            <?php echo htmlspecialchars($anabul['nama_hewan']); ?>
                        </h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p>
                                <i class="fas fa-tag text-orange-500 w-4"></i>
                                <?php echo htmlspecialchars($anabul['kategori_hewan']); ?>
                                <?php if (!empty($anabul['jenis_ras'])): ?>
                                    - <?php echo htmlspecialchars($anabul['jenis_ras']); ?>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($anabul['umur_tahun']) || !empty($anabul['umur_bulan'])): ?>
                                <p>
                                    <i class="fas fa-birthday-cake text-orange-500 w-4"></i>
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
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($anabul['berat'])): ?>
                                <p>
                                    <i class="fas fa-weight text-orange-500 w-4"></i>
                                    <?php echo htmlspecialchars($anabul['berat']); ?> kg
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($anabul['jenis_kelamin'])): ?>
                                <p>
                                    <i class="fas fa-venus-mars text-orange-500 w-4"></i>
                                    <?php echo htmlspecialchars($anabul['jenis_kelamin']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button onclick="viewAnabulDetail(<?php echo $anabul['id_anabul']; ?>)"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                        Lihat Detail
                    </button>
                    <button onclick="editAnabul(<?php echo $anabul['id_anabul']; ?>)"
                        class="text-orange-600 hover:text-orange-800 p-2 rounded-lg hover:bg-orange-50 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button
                        onclick="deleteAnabul(<?php echo $anabul['id_anabul']; ?>, '<?php echo htmlspecialchars($anabul['nama_hewan'], ENT_QUOTES); ?>')"
                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Additional Info (Collapsible) -->
            <?php if (!empty($anabul['riwayat_kesehatan']) || !empty($anabul['karakteristik'])): ?>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <?php if (!empty($anabul['riwayat_kesehatan'])): ?>
                            <div>
                                <h5 class="font-medium text-gray-700 mb-2 text-base">
                                    <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                                    Riwayat Kesehatan
                                </h5>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($anabul['riwayat_kesehatan'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($anabul['karakteristik'])): ?>
                            <div>
                                <h5 class="font-medium text-gray-700 mb-2 text-base">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                    Karakteristik Unik
                                </h5>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($anabul['karakteristik'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal for Anabul Detail -->
<div id="anabulDetailModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[85vh] overflow-y-auto rounded-r-3xl">
        <div class="p-5">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-800">Detail Anabul</h3>
                <button onclick="closeAnabulModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="anabulDetailContent" class="space-y-5">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Delete -->
<div id="deleteConfirmModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1000] hidden">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
        <div class="text-xl font-bold text-center text-gray-900 mb-4">
            Hapus Data Anabul
        </div>
        <div class="mb-6">
            <p class="text-base text-center text-gray-500">
                Apakah Anda yakin ingin menghapus data <span id="anabulNameToDelete" class="font-semibold"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()"
                class="px-6 py-2 border border-gray-200 text-gray-700 rounded-full hover:bg-gray-100 transition-colors font-medium text-sm">
                Batal
            </button>
            <button onclick="confirmDelete()"
                class="px-6 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors font-medium text-sm">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
    let anabulToDelete = null;

    function viewAnabulDetail(anabulId) {
        // Show modal
        document.getElementById('anabulDetailModal').classList.remove('hidden');

        // Load content via AJAX
        fetch(`get_anabul_detail.php?id=${anabulId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('anabulDetailContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('anabulDetailContent').innerHTML =
                    '<p class="text-red-500">Error loading detail data.</p>';
            });
    }

    function closeAnabulModal() {
        document.getElementById('anabulDetailModal').classList.add('hidden');
    }

    function editAnabul(anabulId) {
        // Redirect to edit page or load edit form
        window.location.href = `edit_anabul.php?id=${anabulId}`;
    }

    function deleteAnabul(anabulId, anabulName) {
        anabulToDelete = anabulId;
        document.getElementById('anabulNameToDelete').textContent = anabulName;
        document.getElementById('deleteConfirmModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
        anabulToDelete = null;
    }

    function confirmDelete() {
        if (anabulToDelete) {
            // Send delete request
            fetch('delete_anabul.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_anabul=${anabulToDelete}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPopup('Data anabul berhasil dihapus!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showPopup('Gagal menghapus data anabul.', 'error');
                    }
                    closeDeleteModal();
                })
                .catch(error => {
                    showPopup('Terjadi kesalahan saat menghapus data.', 'error');
                    closeDeleteModal();
                });
        }
    }

    // Close modals when clicking outside
    document.getElementById('anabulDetailModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeAnabulModal();
        }
    });

    document.getElementById('deleteConfirmModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>