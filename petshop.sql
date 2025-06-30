-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 11:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','staff') DEFAULT 'staff',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `email`, `password`, `nama_lengkap`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$JLhHgmG//658nq54ZcmDIuDQePWyu4Ozy2QvSF9vSF38cgzPiHZPO', 'Susanto', 'staff', 'aktif', NULL, '2025-06-13 18:57:26', '2025-06-15 05:20:25');

-- --------------------------------------------------------

--
-- Table structure for table `alamat_pelanggan`
--

CREATE TABLE `alamat_pelanggan` (
  `id_alamat` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `label_alamat` varchar(50) NOT NULL,
  `nama_penerima` varchar(255) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `desa` varchar(100) DEFAULT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kabupaten` varchar(100) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alamat_pelanggan`
--

INSERT INTO `alamat_pelanggan` (`id_alamat`, `id_pelanggan`, `label_alamat`, `nama_penerima`, `nomor_telepon`, `alamat_lengkap`, `desa`, `rt`, `rw`, `kelurahan`, `kecamatan`, `kabupaten`, `provinsi`, `kode_pos`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 6, 'Rumah', 'alex', '0932323', 'vdv', 'vdsv', 'dvsv', 'vdsv', 'vdsv', 'dvsv', 'dsv', 'dvsv', '212', 0, '2025-06-22 08:19:32', '2025-06-22 08:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `anabul`
--

CREATE TABLE `anabul` (
  `id_anabul` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `nama_hewan` varchar(100) NOT NULL,
  `spesies` enum('kucing','anjing','hamster','kelinci','lainnya') NOT NULL,
  `ras` varchar(100) DEFAULT NULL,
  `umur_tahun` int(3) DEFAULT 0,
  `umur_bulan` int(2) DEFAULT 0,
  `berat_kg` decimal(5,2) DEFAULT NULL,
  `jenis_kelamin` enum('jantan','betina') DEFAULT NULL,
  `warna` varchar(50) DEFAULT NULL,
  `ciri_khusus` text DEFAULT NULL,
  `riwayat_penyakit` text DEFAULT NULL,
  `alergi` text DEFAULT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anabul`
--

INSERT INTO `anabul` (`id_anabul`, `id_pelanggan`, `nama_hewan`, `spesies`, `ras`, `umur_tahun`, `umur_bulan`, `berat_kg`, `jenis_kelamin`, `warna`, `ciri_khusus`, `riwayat_penyakit`, `alergi`, `foto_utama`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fluffy', 'kucing', 'Persian', 2, 6, 4.50, 'betina', 'Putih', 'Mata biru, bulu panjang', 'Pernah flu kucing tahun 2024', 'Alergi seafood', 'fluffy.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 1, 'Buddy', 'anjing', 'Golden Retriever', 3, 0, 25.00, 'jantan', 'Emas', 'Sangat ramah, suka bermain air', 'Sehat', NULL, 'buddy.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 2, 'Whiskers', 'kucing', 'Maine Coon', 1, 8, 6.20, 'jantan', 'Cokelat belang', 'Ekor sangat lebat, mata hijau', 'Sehat', NULL, 'whiskers.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(4, 3, 'Max', 'anjing', 'Labrador', 4, 2, 28.50, 'jantan', 'Hitam', 'Sangat aktif, pintar', 'Pernah patah kaki 2023', NULL, 'max.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(19, 6, 'casc', 'anjing', NULL, 0, 0, NULL, NULL, NULL, 'csac', NULL, NULL, NULL, 'tidak_aktif', '2025-06-16 06:18:58', '2025-06-21 17:35:14'),
(20, 6, 'casc', 'anjing', NULL, 0, 0, NULL, NULL, NULL, 'csac', NULL, NULL, NULL, 'tidak_aktif', '2025-06-16 06:19:25', '2025-06-21 17:29:01'),
(21, 6, 'csac', 'kucing', 'vsadv', 1, NULL, 1.10, NULL, NULL, 'scac', NULL, NULL, 'anabul_6856cfa907e65.jpg', 'tidak_aktif', '2025-06-16 07:04:31', '2025-06-21 17:35:17'),
(24, 6, 'd', 'kucing', NULL, NULL, NULL, NULL, NULL, NULL, 'dcdccdacd', NULL, NULL, 'default.jpg', 'tidak_aktif', '2025-06-21 16:40:31', '2025-06-21 17:35:24'),
(25, 6, 'sac', 'kucing', NULL, 0, 0, NULL, NULL, NULL, 'csac', NULL, NULL, NULL, 'tidak_aktif', '2025-06-21 17:32:17', '2025-06-21 17:32:32'),
(26, 6, 'sacdv', 'kucing', NULL, NULL, NULL, NULL, NULL, NULL, 'csac', NULL, NULL, 'anabul_6856ed4319af6.jpg', 'aktif', '2025-06-21 17:34:44', '2025-06-21 17:34:59'),
(27, 6, 'csc', 'kucing', 'heo', 0, 0, 0.00, 'jantan', '', 'csdc', '', '', NULL, 'aktif', '2025-06-29 03:19:09', '2025-06-30 08:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `anabul_foto`
--

CREATE TABLE `anabul_foto` (
  `id_foto` int(11) NOT NULL,
  `id_anabul` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `urutan` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anabul_foto`
--

INSERT INTO `anabul_foto` (`id_foto`, `id_anabul`, `nama_file`, `urutan`, `created_at`) VALUES
(1, 1, 'fluffy_1.jpg', 1, '2025-06-14 07:07:23'),
(2, 1, 'fluffy_2.jpg', 2, '2025-06-14 07:07:23'),
(3, 2, 'buddy_1.jpg', 1, '2025-06-14 07:07:23'),
(4, 2, 'buddy_2.jpg', 2, '2025-06-14 07:07:23'),
(5, 2, 'buddy_3.jpg', 3, '2025-06-14 07:07:23'),
(6, 3, 'whiskers_1.jpg', 1, '2025-06-14 07:07:23'),
(7, 4, 'max_1.jpg', 1, '2025-06-14 07:07:23'),
(8, 4, 'max_2.jpg', 2, '2025-06-14 07:07:23'),
(16, 26, 'anabul_6856ed4319af6.jpg', 1, '2025-06-21 17:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `dokter_hewan`
--

CREATE TABLE `dokter_hewan` (
  `id_dokter` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `nomor_lisensi` varchar(50) NOT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tarif_konsultasi` decimal(10,2) DEFAULT 150000.00,
  `pengalaman_tahun` int(11) DEFAULT 0,
  `foto_dokter` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif','cuti') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokter_hewan`
--

INSERT INTO `dokter_hewan` (`id_dokter`, `nama_dokter`, `nomor_lisensi`, `spesialisasi`, `nomor_telepon`, `email`, `tarif_konsultasi`, `pengalaman_tahun`, `foto_dokter`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Sarah Veterina', 'DRH001234', 'Umum', '081234567800', 'dr.sarah@petshop.com', 150000.00, 8, 'dr_sarah.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 'Dr. Michael Pet', 'DRH001235', 'Bedah', '081234567801', 'dr.michael@petshop.com', 250000.00, 12, 'dr_michael.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 'Dr. Lisa Animal', 'DRH001236', 'Dermatologi', '081234567802', 'dr.lisa@petshop.com', 200000.00, 6, 'dr_lisa.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(4, 'Dr. Robert Care', 'DRH001237', 'Kardiologi', '081234567803', 'dr.robert@petshop.com', 300000.00, 15, 'dr_robert.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(5, 'Dr. Emily Health', 'DRH001238', 'Umum', '081234567804', 'dr.emily@petshop.com', 150000.00, 5, 'dr_emily.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23');

-- --------------------------------------------------------

--
-- Table structure for table `favorit`
--

CREATE TABLE `favorit` (
  `id_favorit` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorit`
--

INSERT INTO `favorit` (`id_favorit`, `id_pelanggan`, `id_produk`, `created_at`) VALUES
(17, 6, 37, '2025-06-21 03:06:52'),
(22, 6, 26, '2025-06-21 08:11:52'),
(23, 6, 36, '2025-06-22 00:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_dokter`
--

CREATE TABLE `jadwal_dokter` (
  `id_jadwal` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `hari` enum('sabtu','minggu') NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `slot_maksimal` int(11) DEFAULT 8,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_dokter`
--

INSERT INTO `jadwal_dokter` (`id_jadwal`, `id_dokter`, `hari`, `waktu_mulai`, `waktu_selesai`, `slot_maksimal`, `status`, `created_at`) VALUES
(1, 1, 'sabtu', '08:00:00', '12:00:00', 8, 'aktif', '2025-06-14 07:07:23'),
(2, 1, 'minggu', '09:00:00', '13:00:00', 8, 'aktif', '2025-06-14 07:07:23'),
(3, 2, 'sabtu', '13:00:00', '17:00:00', 6, 'aktif', '2025-06-14 07:07:23'),
(4, 2, 'minggu', '14:00:00', '18:00:00', 6, 'aktif', '2025-06-14 07:07:23'),
(5, 3, 'sabtu', '08:00:00', '16:00:00', 10, 'aktif', '2025-06-14 07:07:23'),
(6, 4, 'minggu', '10:00:00', '14:00:00', 4, 'aktif', '2025-06-14 07:07:23'),
(7, 5, 'sabtu', '09:00:00', '15:00:00', 8, 'aktif', '2025-06-14 07:07:23'),
(8, 5, 'minggu', '08:00:00', '12:00:00', 8, 'aktif', '2025-06-14 07:07:23');

-- --------------------------------------------------------

--
-- Table structure for table `kapasitas_penitipan`
--

CREATE TABLE `kapasitas_penitipan` (
  `id_kapasitas` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori_layanan` varchar(50) NOT NULL,
  `kapasitas_maksimal` int(11) NOT NULL DEFAULT 10,
  `kapasitas_terpakai` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kapasitas_penitipan`
--

INSERT INTO `kapasitas_penitipan` (`id_kapasitas`, `tanggal`, `kategori_layanan`, `kapasitas_maksimal`, `kapasitas_terpakai`, `created_at`, `updated_at`) VALUES
(1, '2025-06-18', '7', 10, 0, '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(2, '2025-06-19', '7', 10, 0, '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(3, '2025-06-20', '7', 10, 0, '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(4, '2025-06-21', '7', 10, 0, '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(5, '2025-06-22', '7', 10, 0, '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(6, '2025-06-18', 'basic', 10, 3, '2025-06-16 05:20:25', '2025-06-16 05:49:01'),
(7, '2025-06-19', 'basic', 10, 2, '2025-06-16 05:20:25', '2025-06-16 05:46:42'),
(8, '2025-06-20', 'basic', 10, 2, '2025-06-16 05:20:25', '2025-06-16 05:46:42'),
(9, '2025-06-21', 'basic', 10, 2, '2025-06-16 05:20:25', '2025-06-16 05:46:42'),
(10, '2025-06-23', 'basic', 10, 1, '2025-06-16 05:22:05', '2025-06-16 05:22:05'),
(11, '2025-06-24', 'basic', 10, 1, '2025-06-16 05:22:05', '2025-06-16 05:22:05'),
(12, '2025-06-25', 'basic', 10, 1, '2025-06-16 05:22:05', '2025-06-16 05:22:05'),
(13, '2025-06-27', 'basic', 10, 1, '2025-06-16 05:24:45', '2025-06-16 05:24:45'),
(14, '2025-06-28', 'basic', 10, 1, '2025-06-16 05:24:45', '2025-06-16 05:24:45'),
(15, '2025-06-22', 'basic', 10, 1, '2025-06-16 05:41:06', '2025-06-16 05:41:06'),
(16, '2025-06-29', 'basic', 10, 1, '2025-06-16 06:12:34', '2025-06-16 06:12:34'),
(17, '2025-06-18', 'premium', 8, 2, '2025-06-16 06:18:58', '2025-06-16 06:19:25'),
(18, '2025-06-19', 'premium', 8, 2, '2025-06-16 06:18:58', '2025-06-16 06:19:25'),
(19, '2025-07-02', 'premium', 8, 1, '2025-06-16 06:21:37', '2025-06-16 06:21:37'),
(20, '2025-07-03', 'premium', 8, 1, '2025-06-16 06:21:37', '2025-06-16 06:21:37'),
(21, '2025-07-04', 'premium', 8, 1, '2025-06-16 06:21:37', '2025-06-16 06:21:37'),
(22, '2025-06-30', 'basic', 10, 3, '2025-06-21 17:32:17', '2025-06-22 23:20:18'),
(23, '2025-07-01', 'basic', 10, 2, '2025-06-21 17:32:17', '2025-06-22 23:20:18'),
(24, '2025-07-02', 'basic', 10, 2, '2025-06-21 17:32:17', '2025-06-22 23:20:18'),
(25, '2025-07-04', 'basic', 10, 1, '2025-06-21 17:34:44', '2025-06-21 17:34:44'),
(26, '2025-07-05', 'basic', 10, 1, '2025-06-21 17:34:44', '2025-06-21 17:34:44'),
(27, '2025-07-07', 'basic', 10, 1, '2025-06-22 23:31:48', '2025-06-22 23:31:48'),
(28, '2025-07-08', 'basic', 10, 1, '2025-06-22 23:31:48', '2025-06-22 23:31:48');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi`
--

CREATE TABLE `konfigurasi` (
  `id_config` int(11) NOT NULL,
  `nama_setting` varchar(50) NOT NULL,
  `nilai` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tipe_data` enum('string','number','boolean','json') DEFAULT 'string',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `konfigurasi`
--

INSERT INTO `konfigurasi` (`id_config`, `nama_setting`, `nilai`, `deskripsi`, `tipe_data`, `updated_at`) VALUES
(1, 'max_penitipan_basic', '10', 'Kapasitas maksimal penitipan basic per hari', 'number', '2025-06-13 18:47:54'),
(2, 'max_penitipan_premium', '8', 'Kapasitas maksimal penitipan premium per hari', 'number', '2025-06-13 18:47:54'),
(3, 'max_penitipan_vip', '5', 'Kapasitas maksimal penitipan VIP per hari', 'number', '2025-06-13 18:47:54'),
(4, 'jam_operasional', '{\"buka\": \"08:00\", \"tutup\": \"21:00\"}', 'Jam operasional petshop', 'json', '2025-06-13 18:47:54'),
(5, 'biaya_admin', '1000', 'Biaya admin untuk setiap transaksi', 'number', '2025-06-13 18:47:54'),
(6, 'minimal_dp_penitipan', '30', 'Minimal DP penitipan dalam persen', 'number', '2025-06-13 18:47:54'),
(7, 'max_perawatan_per_hari', '15', 'Kapasitas maksimal perawatan per hari', 'number', '2025-06-14 02:47:45'),
(8, 'jam_layanan_perawatan', '{\"mulai\": \"09:00\", \"selesai\": \"16:00\"}', 'Jam layanan perawatan', 'json', '2025-06-14 02:47:45'),
(9, 'max_advance_booking_days', '30', 'Maksimal hari booking di muka', 'number', '2025-06-16 04:49:18'),
(10, 'min_advance_booking_hours', '24', 'Minimal jam booking sebelum check-in', 'number', '2025-06-16 04:49:18'),
(11, 'max_boarding_duration_days', '30', 'Maksimal durasi penitipan dalam hari', 'number', '2025-06-16 04:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id_konsultasi` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `id_anabul` int(11) NOT NULL,
  `keluhan_utama` text NOT NULL,
  `gejala` text DEFAULT NULL,
  `durasi_gejala` varchar(50) DEFAULT NULL,
  `tingkat_keparahan` enum('ringan','sedang','berat','darurat') DEFAULT 'ringan',
  `diagnosis` text DEFAULT NULL,
  `resep_obat` text DEFAULT NULL,
  `saran_perawatan` text DEFAULT NULL,
  `tanggal_kontrol` date DEFAULT NULL,
  `status_konsultasi` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `konsultasi`
--

INSERT INTO `konsultasi` (`id_konsultasi`, `id_pesanan`, `id_dokter`, `id_anabul`, `keluhan_utama`, `gejala`, `durasi_gejala`, `tingkat_keparahan`, `diagnosis`, `resep_obat`, `saran_perawatan`, `tanggal_kontrol`, `status_konsultasi`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 3, 'Kucing tidak mau makan', 'Lesu, tidak mau makan, tidur terus', '2 hari', 'sedang', 'Kemungkinan stress atau gangguan pencernaan', 'Vitamin B Complex, Probiotik', 'Berikan makanan lembut, pantau nafsu makan', '2025-06-20', 'completed', '2025-06-14 07:07:24', '2025-06-14 07:07:24'),
(7, 44, 3, 24, 'cdcdacacadcacda', 'cdcdcdacdacacac', '1-3_hari', 'ringan', NULL, NULL, NULL, '2025-06-28', 'scheduled', '2025-06-21 16:40:31', '2025-06-30 07:23:22'),
(8, 54, 2, 27, 'cdscdcwdcvdvds', 'cdscdcwdcvdvds', 'kurang_dari_1_hari', 'ringan', NULL, NULL, NULL, '2025-07-05', 'ongoing', '2025-06-29 03:49:07', '2025-06-30 07:30:43');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_penitipan`
--

CREATE TABLE `laporan_penitipan` (
  `id_laporan` int(11) NOT NULL,
  `id_penitipan` int(11) NOT NULL,
  `tanggal_laporan` date NOT NULL,
  `kondisi_hewan` enum('sangat_baik','baik','cukup','kurang_baik','sakit') DEFAULT 'baik',
  `aktivitas` text DEFAULT NULL,
  `nafsu_makan` enum('sangat_baik','baik','kurang','tidak_mau_makan') DEFAULT 'baik',
  `catatan_harian` text DEFAULT NULL,
  `foto_hewan` varchar(255) DEFAULT NULL,
  `petugas_piket` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_penitipan`
--

INSERT INTO `laporan_penitipan` (`id_laporan`, `id_penitipan`, `tanggal_laporan`, `kondisi_hewan`, `aktivitas`, `nafsu_makan`, `catatan_harian`, `foto_hewan`, `petugas_piket`, `created_at`) VALUES
(1, 1, '2025-06-20', 'baik', 'Bermain dengan mainan, tidur normal', 'baik', 'Fluffy beradaptasi dengan baik, makan dengan lahap', 'fluffy_day1.jpg', 'Andi (Petugas Harian)', '2025-06-14 07:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int(11) NOT NULL,
  `jenis_layanan` enum('perawatan','penitipan','konsultasi') NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `kategori_layanan` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `durasi_menit` int(11) DEFAULT NULL,
  `target_hewan` set('kucing','anjing','hamster','kelinci','semua') DEFAULT 'semua',
  `fasilitas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fasilitas`)),
  `syarat_ketentuan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `jenis_layanan`, `nama_layanan`, `kategori_layanan`, `deskripsi`, `harga`, `durasi_menit`, `target_hewan`, `fasilitas`, `syarat_ketentuan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'perawatan', 'Paket Basic Kucing', 'basic', 'Layanan grooming anti kutu, grooming anti jamur, pemeriksaan kesehatan, dan pemberian obat cacing untuk kucing', 75000.00, 120, 'kucing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(2, 'perawatan', 'Paket Basic Anjing', 'basic', 'Layanan grooming anti kutu, grooming anti jamur, pemeriksaan kesehatan, dan pemberian obat cacing untuk anjing', 95000.00, 120, 'anjing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(3, 'perawatan', 'Paket Mix Kucing', 'mix', 'Grooming basic + suntik vitamin untuk kesehatan optimal kucing', 125000.00, 150, 'kucing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\", \"suntik_vitamin\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(4, 'perawatan', 'Paket Mix Anjing', 'mix', 'Grooming basic + suntik vitamin untuk kesehatan optimal anjing', 155000.00, 150, 'anjing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\", \"suntik_vitamin\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(5, 'perawatan', 'Paket Lengkap Kucing', 'lengkap', 'Perawatan menyeluruh yang terdiri dari grooming mix + tes Revolution untuk pemeriksaan parasit pada kucing kesayangan Anda', 185000.00, 180, 'kucing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\", \"suntik_vitamin\", \"tes_revolution\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(6, 'perawatan', 'Paket Lengkap Anjing', 'lengkap', 'Perawatan menyeluruh yang terdiri dari grooming mix + tes Revolution untuk pemeriksaan parasit pada anjing kesayangan Anda', 225000.00, 180, 'anjing', '[\"grooming_anti_kutu\", \"grooming_anti_jamur\", \"pemeriksaan_kesehatan\", \"obat_cacing\", \"suntik_vitamin\", \"tes_revolution\"]', NULL, 'aktif', '2025-06-14 02:44:06', '2025-06-14 02:44:06'),
(7, 'penitipan', 'Penitipan Basic', 'basic', 'Kandang standard dengan makanan 2x sehari', 50000.00, 1440, 'semua', '[\"kandang_standard\", \"makanan_2x\", \"air_minum\"]', NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25'),
(8, 'penitipan', 'Penitipan Premium', 'premium', 'Kandang luas + playground + makanan premium 3x sehari', 85000.00, 1440, 'semua', '[\"kandang_premium\", \"playground\", \"makanan_premium_3x\", \"air_minum\", \"mainan\"]', NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25'),
(9, 'penitipan', 'Penitipan VIP', 'vip', 'Suite mewah + AC + TV + makanan premium + grooming harian', 150000.00, 1440, 'semua', '[\"suite_ac\", \"tv_hiburan\", \"makanan_premium_3x\", \"grooming_harian\", \"mainan_eksklusif\", \"laporan_foto_harian\"]', NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25'),
(10, 'konsultasi', 'Konsultasi Umum', 'umum', 'Konsultasi kesehatan umum dengan dokter hewan', 150000.00, 30, 'semua', NULL, NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25'),
(11, 'konsultasi', 'Konsultasi Spesialis', 'spesialis', 'Konsultasi dengan dokter hewan spesialis', 250000.00, 45, 'semua', NULL, NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25'),
(12, 'konsultasi', 'Konsultasi Darurat', 'darurat', 'Konsultasi darurat 24 jam', 350000.00, 60, 'semua', NULL, NULL, 'aktif', '2025-06-14 02:47:25', '2025-06-14 02:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 'john.doe@email.com', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6a7b8c9d0e1f2', '2025-06-15 12:00:00', 1, '2025-06-14 07:07:24'),
(2, 'jane.smith@email.com', 'b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6a7b8c9d0e1f2g3', '2025-06-16 15:30:00', 0, '2025-06-14 07:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `transaction_status` varchar(20) NOT NULL,
  `fraud_status` varchar(20) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `signature_key` varchar(255) DEFAULT NULL,
  `raw_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `email`, `password`, `nama_lengkap`, `nomor_telepon`, `alamat`, `foto_profil`, `remember_token`, `status`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '081234567890', 'Jl. Merdeka No. 123, Jakarta Pusat', 'john_doe.jpg', NULL, 'aktif', '2025-06-01 03:00:00', '2025-06-14 07:07:23', '2025-06-30 08:08:32'),
(2, 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '0812345678911', 'Jl. Sudirman No. 456, Jakarta Selatan', 'jane_smith.jpg', NULL, 'nonaktif', '2025-06-02 04:00:00', '2025-06-14 07:07:23', '2025-06-30 08:19:50'),
(3, 'bob.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob Wilson', '081234567892', 'Jl. Gatot Subroto No. 789, Jakarta Barat', 'bob_wilson.jpg', NULL, 'aktif', '2025-06-03 05:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(6, '1@gmail.com', '$2y$10$p8WOAWTO.aJeKzK9lr3hL.EdZ.k.rWFIiudz4ynnPZA/lsfpEYI6y', 'alex', '0932323', 'Desa dsvs, RT 32, RW 32, Kel. dasc, Kec. cdasc, Kab. asca, casa, 232', 'profil_6_1751163662.png', NULL, 'aktif', NULL, '2025-06-15 01:10:11', '2025-06-29 03:13:25');

-- --------------------------------------------------------

--
-- Table structure for table `penitipan`
--

CREATE TABLE `penitipan` (
  `id_penitipan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_anabul` int(11) NOT NULL,
  `tanggal_checkin` date NOT NULL,
  `tanggal_checkout` date NOT NULL,
  `waktu_checkin` time DEFAULT NULL,
  `waktu_checkout` time DEFAULT NULL,
  `jumlah_hari` int(11) NOT NULL,
  `nomor_kandang` varchar(20) DEFAULT NULL,
  `kondisi_kesehatan` text DEFAULT NULL,
  `makanan_khusus` text DEFAULT NULL,
  `obat_khusus` text DEFAULT NULL,
  `instruksi_khusus` text DEFAULT NULL,
  `kontak_darurat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`kontak_darurat`)),
  `status_checkin` enum('pending','checked_in','checked_out') DEFAULT 'pending',
  `catatan_checkin` text DEFAULT NULL,
  `catatan_checkout` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penitipan`
--

INSERT INTO `penitipan` (`id_penitipan`, `id_pesanan`, `id_anabul`, `tanggal_checkin`, `tanggal_checkout`, `waktu_checkin`, `waktu_checkout`, `jumlah_hari`, `nomor_kandang`, `kondisi_kesehatan`, `makanan_khusus`, `obat_khusus`, `instruksi_khusus`, `kontak_darurat`, `status_checkin`, `catatan_checkin`, `catatan_checkout`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2025-06-20', '2025-06-25', '08:00:00', NULL, 5, 'K-001', 'Sehat, sudah vaksin lengkap', 'Royal Canin Persian', NULL, 'Jangan beri seafood, alergi', '{\"nama\": \"John Doe\", \"telepon\": \"081234567890\", \"hubungan\": \"Pemilik\"}', 'pending', NULL, NULL, '2025-06-14 07:07:24', '2025-06-14 07:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `perawatan`
--

CREATE TABLE `perawatan` (
  `id_perawatan` int(11) NOT NULL,
  `id_pesanan_layanan` int(11) NOT NULL,
  `id_anabul` int(11) NOT NULL,
  `paket_perawatan` enum('basic','mix','lengkap') NOT NULL,
  `tanggal_perawatan` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL,
  `kondisi_awal` text DEFAULT NULL,
  `kondisi_akhir` text DEFAULT NULL,
  `catatan_perawatan` text DEFAULT NULL,
  `foto_sebelum` varchar(255) DEFAULT NULL,
  `foto_sesudah` varchar(255) DEFAULT NULL,
  `status_pesanan` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perawatan`
--

INSERT INTO `perawatan` (`id_perawatan`, `id_pesanan_layanan`, `id_anabul`, `paket_perawatan`, `tanggal_perawatan`, `waktu_mulai`, `waktu_selesai`, `petugas`, `kondisi_awal`, `kondisi_akhir`, `catatan_perawatan`, `foto_sebelum`, `foto_sesudah`, `status_pesanan`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'mix', '2025-06-15', '10:00:00', '12:30:00', 'Sari (Groomer)', 'Bulu kotor, bau, ada kutu sedikit', 'Bulu bersih, wangi, bebas kutu', 'Grooming berjalan lancar, anjing kooperatif', 'buddy_before.jpg', 'buddy_after.jpg', 'completed', '2025-06-14 07:07:24', '2025-06-14 07:07:24'),
(11, 24, 19, 'basic', '2025-06-18', '08:00:00', '10:00:00', '', '', '', 'Booking layanan grooming - Waktu: pagi', '', '', 'scheduled', '2025-06-16 06:46:33', '2025-06-16 06:46:33'),
(15, 28, 19, 'basic', '2025-06-18', '08:00:00', '10:00:00', '', '', '', 'Booking layanan grooming - Waktu: pagi', '', '', 'scheduled', '2025-06-16 06:55:36', '2025-06-16 06:55:36'),
(19, 32, 21, '', '2025-06-30', '08:00:00', '10:00:00', '', '', '', '', '', '', 'in_progress', '2025-06-16 07:04:31', '2025-06-30 07:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `nomor_pesanan` varchar(20) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `jenis_pesanan` enum('produk','penitipan','konsultasi','perawatan') NOT NULL,
  `total_harga` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` enum('cash','transfer','debit','credit','ewallet') DEFAULT NULL,
  `status_pembayaran` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `status_pesanan` enum('pending','confirmed','processing','completed','cancelled') DEFAULT 'pending',
  `tanggal_pesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_layanan` date DEFAULT NULL,
  `waktu_layanan` time DEFAULT NULL,
  `catatan_pelanggan` text DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `nomor_pesanan`, `id_pelanggan`, `jenis_pesanan`, `total_harga`, `metode_pembayaran`, `status_pembayaran`, `status_pesanan`, `tanggal_pesanan`, `tanggal_layanan`, `waktu_layanan`, `catatan_pelanggan`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20250614-001', 1, 'produk', 575000.00, 'transfer', 'paid', 'completed', '2025-06-14 07:07:23', NULL, NULL, 'Tolong kirim hari ini', NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 'ORD-20250614-002', 2, '', 155000.00, 'cash', 'paid', 'completed', '2025-06-14 07:07:23', '2025-06-15', '10:00:00', 'Anjing saya takut, tolong pelan-pelan', NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 'ORD-20250614-003', 3, 'konsultasi', 150000.00, 'transfer', 'paid', 'confirmed', '2025-06-14 07:07:23', '2025-06-16', '14:00:00', 'Kucing tidak mau makan 2 hari', NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(13, 'PES-20250615-9493', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-15 16:02:33', NULL, NULL, 'Booking layanan grooming', '', '2025-06-15 16:02:33', '2025-06-15 16:02:33'),
(14, 'PES-20250615-6669', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-15 16:25:26', NULL, NULL, 'Booking layanan grooming', '', '2025-06-15 16:25:26', '2025-06-15 16:25:26'),
(15, 'PES-20250615-4217', 6, 'perawatan', 95000.00, 'cash', 'pending', 'pending', '2025-06-15 16:25:44', NULL, NULL, 'Booking layanan grooming', '', '2025-06-15 16:25:44', '2025-06-15 16:25:44'),
(16, 'PES-20250616-8585', 6, 'perawatan', 95000.00, 'cash', 'pending', 'pending', '2025-06-15 17:54:25', NULL, NULL, 'Booking layanan grooming', '', '2025-06-15 17:54:25', '2025-06-15 17:54:25'),
(18, 'PES-20250616-8823', 6, '', 250000.00, 'cash', 'pending', 'pending', '2025-06-16 04:50:45', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 04:50:45', '2025-06-16 04:50:45'),
(19, 'PES-20250616-4683', 6, '', 200000.00, 'cash', 'pending', 'pending', '2025-06-16 05:20:25', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:20:25', '2025-06-16 05:20:25'),
(20, 'PES-20250616-1847', 6, '', 150000.00, 'cash', 'pending', 'pending', '2025-06-16 05:22:05', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:22:05', '2025-06-16 05:22:05'),
(21, 'PES-20250616-2200', 6, '', 100000.00, 'cash', 'pending', 'pending', '2025-06-16 05:24:45', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:24:45', '2025-06-16 05:24:45'),
(22, 'PES-20250616-7430', 6, '', 100000.00, 'cash', 'pending', 'pending', '2025-06-16 05:39:41', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:39:41', '2025-06-16 05:39:41'),
(23, 'PES-20250616-1068', 6, '', 100000.00, 'cash', 'pending', 'pending', '2025-06-16 05:41:06', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:41:06', '2025-06-16 05:41:06'),
(24, 'PES-20250616-4858', 6, '', 200000.00, 'cash', 'pending', 'pending', '2025-06-16 05:46:42', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:46:42', '2025-06-16 05:46:42'),
(25, 'PES-20250616-3955', 6, '', 50000.00, 'cash', 'pending', 'pending', '2025-06-16 05:49:01', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 05:49:01', '2025-06-16 05:49:01'),
(26, 'PES-20250616-1125', 6, '', 50000.00, 'cash', 'pending', 'pending', '2025-06-16 06:12:34', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 06:12:34', '2025-06-16 06:12:34'),
(27, 'PES-20250616-1089', 6, '', 170000.00, 'cash', 'pending', 'pending', '2025-06-16 06:18:58', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 06:18:58', '2025-06-16 06:18:58'),
(28, 'PES-20250616-2751', 6, '', 170000.00, 'cash', 'pending', 'pending', '2025-06-16 06:19:25', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 06:19:25', '2025-06-16 06:19:25'),
(29, 'PES-20250616-2449', 6, 'penitipan', 255000.00, 'cash', 'pending', 'pending', '2025-06-16 06:21:37', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-16 06:21:37', '2025-06-22 23:30:49'),
(30, 'PES-20250616-7617', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 06:46:33', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:46:33', '2025-06-16 06:46:33'),
(31, 'PES-20250616-4588', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 06:47:40', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:47:40', '2025-06-16 06:47:40'),
(32, 'PES-20250616-9507', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 06:51:30', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:51:30', '2025-06-16 06:51:30'),
(33, 'PES-20250616-0503', 6, 'perawatan', 95000.00, 'cash', 'pending', 'pending', '2025-06-16 06:53:11', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:53:11', '2025-06-16 06:53:11'),
(34, 'PES-20250616-2085', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 06:55:36', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:55:36', '2025-06-16 06:55:36'),
(35, 'PES-20250616-9049', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 06:55:44', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 06:55:44', '2025-06-16 06:55:44'),
(36, 'PES-20250616-1293', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-16 07:00:24', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 07:00:24', '2025-06-16 07:00:24'),
(37, 'PES-20250616-9891', 6, 'perawatan', 95000.00, 'cash', 'pending', 'pending', '2025-06-16 07:02:33', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 07:02:33', '2025-06-16 07:02:33'),
(38, 'PES-20250616-2214', 6, 'perawatan', 75000.00, 'cash', 'paid', 'pending', '2025-06-16 07:04:31', NULL, NULL, 'Booking layanan grooming', '', '2025-06-16 07:04:31', '2025-06-30 07:15:49'),
(39, '', 6, 'produk', 150000.00, NULL, 'pending', 'pending', '2025-06-16 09:03:48', NULL, NULL, NULL, NULL, '2025-06-16 09:03:48', '2025-06-16 09:03:48'),
(43, 'INV-20250616-9884', 6, 'produk', 150000.00, NULL, 'pending', 'pending', '2025-06-16 09:07:10', NULL, NULL, NULL, NULL, '2025-06-16 09:07:10', '2025-06-16 09:07:10'),
(44, 'INV-20250621-6725', 6, 'produk', 200000.00, NULL, 'pending', 'pending', '2025-06-21 16:40:31', NULL, NULL, NULL, NULL, '2025-06-21 16:40:31', '2025-06-21 16:40:31'),
(45, 'PES-20250622-3220', 6, '', 150000.00, 'cash', 'pending', 'pending', '2025-06-21 17:32:17', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-21 17:32:17', '2025-06-21 17:32:17'),
(47, 'ORD-20250622-708', 6, 'produk', 100000.00, 'transfer', 'pending', 'pending', '2025-06-22 03:27:47', NULL, NULL, NULL, NULL, '2025-06-22 08:27:47', '2025-06-22 08:27:47'),
(52, 'PES-20250623-2126', 6, 'penitipan', 100000.00, 'cash', 'pending', 'pending', '2025-06-22 23:31:48', NULL, NULL, 'Booking layanan penitipan', '', '2025-06-22 23:31:48', '2025-06-22 23:31:48'),
(53, 'PES-20250629-2033', 6, 'perawatan', 75000.00, 'cash', 'pending', 'pending', '2025-06-29 03:19:09', NULL, NULL, 'Booking layanan grooming', '', '2025-06-29 03:19:09', '2025-06-29 03:19:09'),
(54, 'INV-20250629-9504', 6, 'konsultasi', 250000.00, NULL, 'paid', 'pending', '2025-06-29 03:49:07', NULL, NULL, NULL, NULL, '2025-06-29 03:49:07', '2025-06-30 07:34:50'),
(55, 'INV-20250629-4645', 6, 'konsultasi', 250000.00, NULL, 'pending', 'pending', '2025-06-29 03:51:10', NULL, NULL, NULL, NULL, '2025-06-29 03:51:10', '2025-06-29 03:51:10'),
(56, 'ORD-20250629-622', 6, 'produk', 30000.00, '', 'pending', 'pending', '2025-06-29 01:02:36', NULL, NULL, NULL, NULL, '2025-06-29 06:02:36', '2025-06-29 06:02:36'),
(57, 'ORD-20250629-849', 6, 'produk', 30000.00, 'transfer', 'paid', 'pending', '2025-06-29 01:09:55', NULL, NULL, NULL, NULL, '2025-06-29 06:09:55', '2025-06-30 09:50:05'),
(58, 'ORD-20250629-216', 6, 'produk', 30000.00, 'transfer', 'paid', 'pending', '2025-06-29 01:12:35', NULL, NULL, NULL, '', '2025-06-29 06:12:35', '2025-06-30 09:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_layanan`
--

CREATE TABLE `pesanan_layanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `id_anabul` int(11) DEFAULT NULL,
  `harga_layanan` decimal(10,2) NOT NULL,
  `catatan_khusus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan_layanan`
--

INSERT INTO `pesanan_layanan` (`id_detail`, `id_pesanan`, `id_layanan`, `id_anabul`, `harga_layanan`, `catatan_khusus`) VALUES
(1, 2, 4, 2, 155000.00, 'Anjing agresif, perlu handling khusus'),
(2, 3, 10, 3, 150000.00, 'Kucing tidak mau makan sejak 2 hari lalu'),
(21, 27, 8, 19, 170000.00, NULL),
(22, 28, 8, 20, 170000.00, NULL),
(24, 30, 1, 19, 75000.00, NULL),
(28, 34, 1, 19, 75000.00, NULL),
(29, 35, 1, 19, 75000.00, NULL),
(32, 38, 1, 21, 75000.00, NULL),
(33, 45, 7, 25, 150000.00, NULL),
(36, 52, 7, 26, 100000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_produk`
--

CREATE TABLE `pesanan_produk` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan_produk`
--

INSERT INTO `pesanan_produk` (`id_detail`, `id_pesanan`, `id_produk`, `quantity`, `harga_satuan`, `subtotal`) VALUES
(5, 47, 26, 5, 18000.00, 90000.00),
(9, 56, 25, 1, 20000.00, 20000.00),
(10, 57, 25, 1, 20000.00, 20000.00),
(11, 58, 25, 1, 20000.00, 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `kategori` enum('makanan','obat','aksesoris','mainan','perawatan','lainnya') NOT NULL,
  `sub_kategori` varchar(100) DEFAULT NULL,
  `target_hewan` set('kucing','anjing','hamster','kelinci','burung','semua') DEFAULT 'semua',
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `berat_gram` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif','habis') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `sub_kategori`, `target_hewan`, `harga`, `stok`, `berat_gram`, `deskripsi`, `foto_utama`, `status`, `created_at`, `updated_at`) VALUES
(25, 'Snappy Tom Makanan Basah 400gr', 'makanan', 'basah', 'kucing', 20000.00, 22, 400, 'Makanan basah berkualitas tinggi untuk kucing dewasa', 'uploads/produk/kucing/produk1.png', 'aktif', '2025-06-01 02:41:53', '2025-06-29 06:12:35'),
(26, 'Cici Makanan Basah 400gr', 'makanan', 'basah', 'kucing', 18000.00, 25, 400, 'Makanan basah dalam kemasan kaleng', 'uploads/produk/kucing/produk2.png', 'aktif', '2025-06-01 02:41:53', '2025-06-22 08:27:47'),
(27, 'Cici Pouch Makanan Basah 85gr', 'makanan', 'basah', 'kucing', 8000.00, 20, 85, 'Premium wet food untuk kucing', 'uploads/produk/kucing/produk3.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:01:03'),
(28, 'Sheba Pouch Makanan Basah 70gr', 'makanan', 'basah', 'kucing', 12000.00, 18, 70, 'Makanan camilan untuk kucing', 'uploads/produk/kucing/produk4.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:01:22'),
(29, 'Whiskas Makanan Basah 85gr', 'makanan', 'basah', 'kucing', 8000.00, 12, 85, 'Snack untuk kucing, sangat disukai', 'uploads/produk/kucing/produk5.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:01:39'),
(30, 'Chummy Pet Wet Flavor 375gr', 'makanan', 'basah', 'anjing', 18000.00, 10, 375, 'Makanan basah untuk anjing dewasa', 'uploads/produk/anjing/makananbasah1.jpeg', 'aktif', '2025-06-01 02:41:53', '2025-06-30 08:57:40'),
(31, 'Snappy Tom Makanan Basah 85gr', 'makanan', 'basah', 'kucing', 8000.00, 40, 85, 'Snack atau camilan kucing', 'uploads/produk/kucing/produk6.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:04:27'),
(32, 'Ciao Churu Snack Kucing 14gr', 'makanan', 'snack', 'kucing', 27000.00, 8, 14, 'Snack kucing untuk sehari-hari', 'uploads/produk/kucing/produk7.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:05:15'),
(33, 'Pet Forest Makanan Basah 400gr', 'makanan', 'basah', 'kucing', 22000.00, 50, 400, 'Makanan kalengan untuk kucing', 'uploads/produk/kucing/produk8.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:05:36'),
(34, 'Pasir Markotops 10liter', '', 'pasir', 'kucing', 65000.00, 45, 10000, 'Pasir tanpa bau untuk kucing kesayangan', 'uploads/produk/kucing/produk9.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:06:50'),
(35, 'Royal Canin First Age Mother & Babycat 2kg', 'makanan', 'kering', 'kucing', 340000.00, 35, 2000, 'Makanan karungan untuk kucing harian', 'uploads/produk/kucing/produk10.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:07:11'),
(36, 'Frontline Obat Tetes Kuku', 'obat', 'tetes', 'kucing', 110000.00, 22, 50, 'Obat tetes kuku khusus kucing', 'uploads/produk/kucing/produk11.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:07:37'),
(37, 'Vitakraft Menu Vital Hamster 400gr', 'makanan', 'kering', 'hamster', 50000.00, 30, 400, 'Makanan premium untuk hamster', 'uploads/produk/hamster/produk1.jpeg', 'aktif', '2025-06-01 02:41:53', '2025-06-30 08:56:39'),
(38, 'HAMVIT vitamin Kelinci 10ml', 'obat', 'vitamin', 'kelinci', 10000.00, 15, 10, 'Vitamin cair untuk menjaga kesehatan kelinci', 'uploads/produk/kelinci/vitamin1.jpeg', 'aktif', '2025-06-01 02:41:53', '2025-06-30 08:57:57'),
(39, 'Friskies Seafood Sensations 3kg', 'makanan', 'kering', 'kucing', 20000.00, 22, 3000, 'Makanan kering karungan khusus kucing', 'uploads/produk/kucing/produk12.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:08:31'),
(40, 'Pasir Kucing Kawan 5liter', '', 'pasir', 'kucing', 35000.00, 22, 5000, 'Pasir khusus kucing', 'uploads/produk/kucing/produk13.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:08:55'),
(41, 'C-one Concentrate Shampoo 1liter', 'perawatan', 'shampoo', 'kucing', 80000.00, 22, 1000, 'Shampoo khusus hewan', 'uploads/produk/kucing/produk14.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:09:29'),
(42, 'Sabina Ocean Fish Repack 1kg', 'makanan', 'kering', 'kucing', 33000.00, 22, 1000, 'Makanan kering khusus kucing dewasa', 'uploads/produk/kucing/produk15.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:09:50'),
(43, 'Life Cat Creamy 2pcs x 15gr', 'makanan', 'snack', 'kucing', 9000.00, 22, 30, 'Makanan snack kucing', 'uploads/produk/kucing/produk16.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:10:13'),
(44, 'Bio Creamy 4pcs x 15gr', 'makanan', 'snack', 'kucing', 16000.00, 22, 60, 'Makanan snack kucing', 'uploads/produk/kucing/produk17.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:10:35'),
(45, 'Makanan Kucing Life Cat 400gr', 'makanan', 'basah', 'kucing', 15000.00, 22, 400, 'Makanan basah kucing', 'uploads/produk/kucing/produk18.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:10:56'),
(46, 'Life Cat for Adult 85g', 'makanan', 'basah', 'kucing', 6000.00, 22, 85, 'Makanan snack kucing sehari-hari', 'uploads/produk/kucing/produk19.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:11:20'),
(47, 'Fancy Feast Makanan Basah 85gr', 'makanan', 'basah', 'kucing', 18000.00, 22, 85, 'Makanan kucing', 'uploads/produk/kucing/produk20.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:11:40'),
(48, 'Whiskas Mackerel Perisa Kembung 480gr', 'makanan', 'kering', 'kucing', 35000.00, 22, 480, 'Makanan kering khusus kucing', 'uploads/produk/kucing/produk21.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:12:03'),
(49, 'Friskies Meaty Grill 1,2kg', 'makanan', 'kering', 'kucing', 72000.00, 22, 1200, 'Makanan kucing karungan', 'uploads/produk/kucing/produk22.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:12:26'),
(50, 'Machiko 225ml Tearless Shampoo', 'perawatan', 'shampoo', 'kucing', 72000.00, 22, 225, 'Shampoo khusus kucing', 'uploads/produk/kucing/produk23.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:12:47'),
(51, 'Unicharm Pet Deo Toilet Pasir 2liter', '', 'pasir', 'kucing', 80000.00, 22, 2000, 'Pasir khusus untuk kotoran hewan peliharaan', 'uploads/produk/kucing/produk24.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:13:07'),
(52, 'Feeding Kit Pelontar Pil Tablet', '', 'feeding', 'kucing', 25000.00, 22, 50, 'Obat untuk menjaga kesehatan hewan peliharaan kita', 'uploads/produk/kucing/produk25.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:13:36'),
(53, 'Serokan Pasir DC-625 untuk Kucing', '', 'serokan', 'kucing', 10000.00, 22, 100, 'Serok khusus untuk mengambil kotoran hewan kesayangan', 'uploads/produk/kucing/produk26.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:13:59'),
(54, 'Anti-Fungal Spray Powder 150ml', 'obat', 'spray', 'kucing', 55000.00, 22, 150, 'Spray khusus anti jamur', 'uploads/produk/kucing/produk27.png', 'aktif', '2025-06-01 02:41:53', '2025-06-15 05:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_medis`
--

CREATE TABLE `riwayat_medis` (
  `id_riwayat` int(11) NOT NULL,
  `id_anabul` int(11) NOT NULL,
  `id_konsultasi` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jenis_tindakan` enum('konsultasi','vaksin','operasi','pemeriksaan','pengobatan','lainnya') NOT NULL,
  `deskripsi` text NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `penanganan` text DEFAULT NULL,
  `obat_diberikan` text DEFAULT NULL,
  `dokter_penangani` varchar(100) DEFAULT NULL,
  `biaya` decimal(10,2) DEFAULT 0.00,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `riwayat_medis`
--

INSERT INTO `riwayat_medis` (`id_riwayat`, `id_anabul`, `id_konsultasi`, `tanggal`, `jenis_tindakan`, `deskripsi`, `diagnosis`, `penanganan`, `obat_diberikan`, `dokter_penangani`, `biaya`, `dokumen_pendukung`, `created_at`) VALUES
(1, 1, NULL, '2024-01-15', 'vaksin', 'Vaksinasi dasar kucing', 'Sehat untuk vaksin', 'Vaksin Tricat', 'Vaksin Tricat', 'Dr. Sarah Veterina', 200000.00, NULL, '2025-06-14 07:07:24'),
(2, 2, NULL, '2024-02-20', 'vaksin', 'Vaksinasi dasar anjing', 'Sehat untuk vaksin', 'Vaksin DHPPi', 'Vaksin DHPPi', 'Dr. Sarah Veterina', 250000.00, NULL, '2025-06-14 07:07:24'),
(3, 3, 1, '2025-06-16', 'konsultasi', 'Konsultasi nafsu makan menurun', 'Stress ringan', 'Pemberian vitamin dan probiotik', 'Vitamin B Complex, Probiotik', 'Dr. Sarah Veterina', 150000.00, NULL, '2025-06-14 07:07:24'),
(4, 4, NULL, '2023-08-10', 'operasi', 'Operasi patah tulang kaki kiri', 'Fraktur tulang radius', 'Operasi pemasangan pen', 'Antibiotik, Analgesik', 'Dr. Michael Pet', 2500000.00, NULL, '2025-06-14 07:07:24'),
(8, 24, 7, '2025-06-28', 'konsultasi', 'Konsultasi: cdcdacacadcacda | Gejala: cdcdcdacdacacac', NULL, NULL, NULL, NULL, 0.00, NULL, '2025-06-21 16:40:31'),
(9, 27, 8, '2025-07-05', 'konsultasi', 'Konsultasi: cdscdcwdcvdvds | Gejala: cdscdcwdcvdvds', NULL, NULL, NULL, NULL, 0.00, NULL, '2025-06-29 03:49:07'),
(10, 27, NULL, '2025-07-05', 'konsultasi', 'Konsultasi: cdscsdcscd | Gejala: cdscsdcscd', NULL, NULL, NULL, NULL, 0.00, NULL, '2025-06-29 03:51:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `alamat_pelanggan`
--
ALTER TABLE `alamat_pelanggan`
  ADD PRIMARY KEY (`id_alamat`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `anabul`
--
ALTER TABLE `anabul`
  ADD PRIMARY KEY (`id_anabul`),
  ADD KEY `idx_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `anabul_foto`
--
ALTER TABLE `anabul_foto`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `idx_anabul` (`id_anabul`);

--
-- Indexes for table `dokter_hewan`
--
ALTER TABLE `dokter_hewan`
  ADD PRIMARY KEY (`id_dokter`),
  ADD UNIQUE KEY `nomor_lisensi` (`nomor_lisensi`);

--
-- Indexes for table `favorit`
--
ALTER TABLE `favorit`
  ADD PRIMARY KEY (`id_favorit`),
  ADD UNIQUE KEY `unique_favorite` (`id_pelanggan`,`id_produk`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `idx_dokter` (`id_dokter`);

--
-- Indexes for table `kapasitas_penitipan`
--
ALTER TABLE `kapasitas_penitipan`
  ADD PRIMARY KEY (`id_kapasitas`),
  ADD UNIQUE KEY `unique_date_service` (`tanggal`,`kategori_layanan`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_kategori_layanan` (`kategori_layanan`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD UNIQUE KEY `unique_cart_item` (`id_pelanggan`,`id_produk`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `konfigurasi`
--
ALTER TABLE `konfigurasi`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `nama_setting` (`nama_setting`);

--
-- Indexes for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id_konsultasi`),
  ADD KEY `idx_pesanan` (`id_pesanan`),
  ADD KEY `idx_dokter` (`id_dokter`),
  ADD KEY `idx_anabul` (`id_anabul`);

--
-- Indexes for table `laporan_penitipan`
--
ALTER TABLE `laporan_penitipan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `idx_penitipan` (`id_penitipan`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`),
  ADD KEY `idx_jenis` (`jenis_layanan`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `transaction_status` (`transaction_status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_remember_token` (`remember_token`);

--
-- Indexes for table `penitipan`
--
ALTER TABLE `penitipan`
  ADD PRIMARY KEY (`id_penitipan`),
  ADD KEY `idx_pesanan` (`id_pesanan`),
  ADD KEY `idx_anabul` (`id_anabul`);

--
-- Indexes for table `perawatan`
--
ALTER TABLE `perawatan`
  ADD PRIMARY KEY (`id_perawatan`),
  ADD KEY `idx_pesanan_layanan` (`id_pesanan_layanan`),
  ADD KEY `idx_anabul` (`id_anabul`),
  ADD KEY `idx_tanggal` (`tanggal_perawatan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `nomor_pesanan` (`nomor_pesanan`),
  ADD KEY `idx_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_status` (`status_pesanan`),
  ADD KEY `idx_jenis` (`jenis_pesanan`),
  ADD KEY `idx_pesanan_report` (`tanggal_pesanan`,`status_pesanan`,`jenis_pesanan`),
  ADD KEY `idx_layanan_schedule` (`tanggal_layanan`,`waktu_layanan`,`status_pesanan`);

--
-- Indexes for table `pesanan_layanan`
--
ALTER TABLE `pesanan_layanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_pesanan` (`id_pesanan`),
  ADD KEY `idx_layanan` (`id_layanan`),
  ADD KEY `idx_anabul` (`id_anabul`);

--
-- Indexes for table `pesanan_produk`
--
ALTER TABLE `pesanan_produk`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_pesanan` (`id_pesanan`),
  ADD KEY `idx_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_produk_search` (`nama_produk`,`kategori`,`status`);

--
-- Indexes for table `riwayat_medis`
--
ALTER TABLE `riwayat_medis`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `idx_anabul` (`id_anabul`),
  ADD KEY `idx_konsultasi` (`id_konsultasi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alamat_pelanggan`
--
ALTER TABLE `alamat_pelanggan`
  MODIFY `id_alamat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anabul`
--
ALTER TABLE `anabul`
  MODIFY `id_anabul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `anabul_foto`
--
ALTER TABLE `anabul_foto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `dokter_hewan`
--
ALTER TABLE `dokter_hewan`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `favorit`
--
ALTER TABLE `favorit`
  MODIFY `id_favorit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kapasitas_penitipan`
--
ALTER TABLE `kapasitas_penitipan`
  MODIFY `id_kapasitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `konfigurasi`
--
ALTER TABLE `konfigurasi`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id_konsultasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_penitipan`
--
ALTER TABLE `laporan_penitipan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `penitipan`
--
ALTER TABLE `penitipan`
  MODIFY `id_penitipan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `perawatan`
--
ALTER TABLE `perawatan`
  MODIFY `id_perawatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `pesanan_layanan`
--
ALTER TABLE `pesanan_layanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `pesanan_produk`
--
ALTER TABLE `pesanan_produk`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `riwayat_medis`
--
ALTER TABLE `riwayat_medis`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamat_pelanggan`
--
ALTER TABLE `alamat_pelanggan`
  ADD CONSTRAINT `alamat_pelanggan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Constraints for table `anabul`
--
ALTER TABLE `anabul`
  ADD CONSTRAINT `anabul_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Constraints for table `anabul_foto`
--
ALTER TABLE `anabul_foto`
  ADD CONSTRAINT `anabul_foto_ibfk_1` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`) ON DELETE CASCADE;

--
-- Constraints for table `favorit`
--
ALTER TABLE `favorit`
  ADD CONSTRAINT `favorit_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorit_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD CONSTRAINT `jadwal_dokter_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter_hewan` (`id_dokter`) ON DELETE CASCADE;

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD CONSTRAINT `konsultasi_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `konsultasi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter_hewan` (`id_dokter`),
  ADD CONSTRAINT `konsultasi_ibfk_3` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`);

--
-- Constraints for table `laporan_penitipan`
--
ALTER TABLE `laporan_penitipan`
  ADD CONSTRAINT `laporan_penitipan_ibfk_1` FOREIGN KEY (`id_penitipan`) REFERENCES `penitipan` (`id_penitipan`) ON DELETE CASCADE;

--
-- Constraints for table `penitipan`
--
ALTER TABLE `penitipan`
  ADD CONSTRAINT `penitipan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `penitipan_ibfk_2` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`);

--
-- Constraints for table `perawatan`
--
ALTER TABLE `perawatan`
  ADD CONSTRAINT `perawatan_ibfk_1` FOREIGN KEY (`id_pesanan_layanan`) REFERENCES `pesanan_layanan` (`id_detail`) ON DELETE CASCADE,
  ADD CONSTRAINT `perawatan_ibfk_2` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `pesanan_layanan`
--
ALTER TABLE `pesanan_layanan`
  ADD CONSTRAINT `pesanan_layanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_layanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`),
  ADD CONSTRAINT `pesanan_layanan_ibfk_3` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`);

--
-- Constraints for table `pesanan_produk`
--
ALTER TABLE `pesanan_produk`
  ADD CONSTRAINT `pesanan_produk_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_produk_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `riwayat_medis`
--
ALTER TABLE `riwayat_medis`
  ADD CONSTRAINT `riwayat_medis_ibfk_1` FOREIGN KEY (`id_anabul`) REFERENCES `anabul` (`id_anabul`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_medis_ibfk_2` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id_konsultasi`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
