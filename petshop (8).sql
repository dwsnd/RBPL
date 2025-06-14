-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 03:03 PM
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
(1, 'admin', 'admin@admin.com', '$2y$10$jqexRc6IAgHMyOOm0qzXAeRPc1BxH7HD1AJvxCn/Gk/8y3aNJNk/a', 'Susanto', 'staff', 'aktif', NULL, '2025-06-13 18:57:26', '2025-06-13 18:58:58');

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
(5, 4, 'Luna', 'kucing', 'Siamese', 2, 0, 3.80, 'betina', 'Cream dengan ujung gelap', 'Mata biru, sangat vokal', 'Sehat', 'Alergi susu', 'luna.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23');

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
(9, 5, 'luna_1.jpg', 1, '2025-06-14 07:07:23');

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
(1, 1, 1, '2025-06-14 07:07:23'),
(2, 1, 4, '2025-06-14 07:07:23'),
(3, 1, 6, '2025-06-14 07:07:23'),
(4, 2, 2, '2025-06-14 07:07:23'),
(5, 2, 7, '2025-06-14 07:07:23'),
(6, 3, 8, '2025-06-14 07:07:23'),
(7, 4, 9, '2025-06-14 07:07:23'),
(9, 5, 3, '2025-06-14 07:07:23'),
(10, 5, 5, '2025-06-14 07:07:23');

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

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id_keranjang`, `id_pelanggan`, `id_produk`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 1, 4, 2, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 2, 2, 1, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(4, 2, 7, 1, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(5, 3, 8, 3, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(6, 4, 9, 1, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(8, 5, 3, 5, '2025-06-14 07:07:23', '2025-06-14 07:07:23');

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
(8, 'jam_layanan_perawatan', '{\"mulai\": \"09:00\", \"selesai\": \"16:00\"}', 'Jam layanan perawatan', 'json', '2025-06-14 02:47:45');

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
(1, 3, 1, 3, 'Kucing tidak mau makan', 'Lesu, tidak mau makan, tidur terus', '2 hari', 'sedang', 'Kemungkinan stress atau gangguan pencernaan', 'Vitamin B Complex, Probiotik', 'Berikan makanan lembut, pantau nafsu makan', '2025-06-20', 'completed', '2025-06-14 07:07:24', '2025-06-14 07:07:24');

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
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `email`, `password`, `nama_lengkap`, `nomor_telepon`, `alamat`, `foto_profil`, `status`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '081234567890', 'Jl. Merdeka No. 123, Jakarta Pusat', 'john_doe.jpg', 'aktif', '2025-06-01 03:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '081234567891', 'Jl. Sudirman No. 456, Jakarta Selatan', 'jane_smith.jpg', 'aktif', '2025-06-02 04:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 'bob.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob Wilson', '081234567892', 'Jl. Gatot Subroto No. 789, Jakarta Barat', 'bob_wilson.jpg', 'aktif', '2025-06-03 05:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(4, 'alice.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice Brown', '081234567893', 'Jl. Thamrin No. 321, Jakarta Pusat', NULL, 'aktif', '2025-06-04 06:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(5, 'charlie.davis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charlie Davis', '081234567894', 'Jl. Rasuna Said No. 654, Jakarta Selatan', NULL, 'aktif', '2025-06-05 07:00:00', '2025-06-14 07:07:23', '2025-06-14 07:07:23');

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
  `status_perawatan` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perawatan`
--

INSERT INTO `perawatan` (`id_perawatan`, `id_pesanan_layanan`, `id_anabul`, `paket_perawatan`, `tanggal_perawatan`, `waktu_mulai`, `waktu_selesai`, `petugas`, `kondisi_awal`, `kondisi_akhir`, `catatan_perawatan`, `foto_sebelum`, `foto_sesudah`, `status_perawatan`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'mix', '2025-06-15', '10:00:00', '12:30:00', 'Sari (Groomer)', 'Bulu kotor, bau, ada kutu sedikit', 'Bulu bersih, wangi, bebas kutu', 'Grooming berjalan lancar, anjing kooperatif', 'buddy_before.jpg', 'buddy_after.jpg', 'completed', '2025-06-14 07:07:24', '2025-06-14 07:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `nomor_pesanan` varchar(20) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `jenis_pesanan` enum('produk','layanan','konsultasi','perawatan') NOT NULL,
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
(2, 'ORD-20250614-002', 2, 'layanan', 155000.00, 'cash', 'paid', 'completed', '2025-06-14 07:07:23', '2025-06-15', '10:00:00', 'Anjing saya takut, tolong pelan-pelan', NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 'ORD-20250614-003', 3, 'konsultasi', 150000.00, 'transfer', 'paid', 'confirmed', '2025-06-14 07:07:23', '2025-06-16', '14:00:00', 'Kucing tidak mau makan 2 hari', NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(5, 'ORD-20250614-005', 5, 'produk', 125000.00, 'ewallet', 'pending', 'pending', '2025-06-14 07:07:23', NULL, NULL, NULL, NULL, '2025-06-14 07:07:23', '2025-06-14 07:07:23');

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
(2, 3, 10, 3, 150000.00, 'Kucing tidak mau makan sejak 2 hari lalu');

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
(1, 1, 1, 1, 385000.00, 385000.00),
(2, 1, 4, 2, 95000.00, 190000.00),
(3, 5, 2, 1, 425000.00, 425000.00),
(4, 5, 7, 1, 85000.00, 85000.00);

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
(1, 'Royal Canin Kitten 2kg', 'makanan', 'Dry Food', 'kucing', 385000.00, 25, 2000, 'Makanan khusus anak kucing umur 4-12 bulan', 'royal_canin_kitten.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(2, 'Pedigree Adult Chicken 10kg', 'makanan', 'Dry Food', 'anjing', 425000.00, 15, 10000, 'Makanan anjing dewasa rasa ayam', 'pedigree_adult.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(3, 'Whiskas Wet Food Tuna', 'makanan', 'Wet Food', 'kucing', 15000.00, 100, 85, 'Makanan basah kucing rasa tuna dalam kaleng', 'whiskas_tuna.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(4, 'Frontline Plus Kucing', 'obat', 'Anti Kutu', 'kucing', 95000.00, 30, 5, 'Obat kutu dan tungau untuk kucing', 'frontline_cat.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(5, 'NexGard Anjing 10-25kg', 'obat', 'Anti Kutu', 'anjing', 125000.00, 20, 10, 'Obat kutu dan caplak untuk anjing', 'nexgard_dog.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(6, 'Kalung Kucing Lucu', 'aksesoris', 'Kalung', 'kucing', 35000.00, 50, 25, 'Kalung adjustable dengan lonceng untuk kucing', 'collar_cat.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(7, 'Leash Anjing Retractable', 'aksesoris', 'Tali', 'anjing', 85000.00, 25, 200, 'Tali anjing otomatis panjang 5 meter', 'leash_retractable.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(8, 'Mainan Bola Interaktif', 'mainan', 'Bola', 'kucing,anjing', 45000.00, 40, 150, 'Bola interaktif dengan suara untuk kucing dan anjing', 'interactive_ball.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(9, 'Shampo Anti Jamur', 'perawatan', 'Shampo', 'kucing,anjing', 55000.00, 35, 250, 'Shampo khusus untuk mengatasi jamur pada hewan', 'antifungal_shampoo.jpg', 'aktif', '2025-06-14 07:07:23', '2025-06-14 07:07:23'),
(13, 'cdsc', 'makanan', 'd', 'kucing', 3324.00, 32, 42, 'fvedv', '', 'aktif', '2025-06-14 08:31:36', '2025-06-14 08:31:36'),
(14, 'rver', 'obat', 'rverv', 'kucing', 43.00, 43, 43, 'btbrt', '', 'aktif', '2025-06-14 08:34:17', '2025-06-14 08:34:17'),
(16, 'vdsv', 'makanan', 'dvs', 'kucing', 322.00, 23, 23, 'dvd', 'assets/img/produk/684d35e5792ab.png', 'aktif', '2025-06-14 08:42:13', '2025-06-14 08:42:13'),
(17, 'svsvs', 'makanan', 'vsvs', 'kucing', 32232.00, 323, 0, 'dsvdsv', '', 'aktif', '2025-06-14 09:32:30', '2025-06-14 09:32:30'),
(20, 'vdsd', 'makanan', 'dsv', 'kucing', 3223.00, 322, 32, 'sdv', 'assets/img/produk/684d50f548b58.png', 'aktif', '2025-06-14 09:51:28', '2025-06-14 10:37:41'),
(22, 'vsdvs', 'makanan', 'ds', 'kucing', 3323.00, 32, 23, 'ewfe', 'assets/img/produk/684d51a17fd42.png', 'aktif', '2025-06-14 10:19:23', '2025-06-14 10:40:33'),
(24, 'vdsvs', 'makanan', 'vdsv', 'kucing', 32.00, 32, 32, 'dsd', 'assets/img/produk/684d537751b05.png', 'aktif', '2025-06-14 10:48:09', '2025-06-14 10:48:23');

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
(5, 5, NULL, '2024-12-05', 'pemeriksaan', 'Pemeriksaan kesehatan rutin', 'Sehat', 'Tidak ada tindakan khusus', NULL, 'Dr. Emily Health', 100000.00, NULL, '2025-06-14 07:07:24');

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
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `anabul`
--
ALTER TABLE `anabul`
  MODIFY `id_anabul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `anabul_foto`
--
ALTER TABLE `anabul_foto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dokter_hewan`
--
ALTER TABLE `dokter_hewan`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `favorit`
--
ALTER TABLE `favorit`
  MODIFY `id_favorit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `konfigurasi`
--
ALTER TABLE `konfigurasi`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id_konsultasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penitipan`
--
ALTER TABLE `penitipan`
  MODIFY `id_penitipan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `perawatan`
--
ALTER TABLE `perawatan`
  MODIFY `id_perawatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pesanan_layanan`
--
ALTER TABLE `pesanan_layanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan_produk`
--
ALTER TABLE `pesanan_produk`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `riwayat_medis`
--
ALTER TABLE `riwayat_medis`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

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
