-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 09:13 AM
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
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `nomor_telepon` varchar(15) DEFAULT NULL,
  `password` varchar(75) DEFAULT NULL,
  `alamat` text NOT NULL DEFAULT '-',
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `email`, `nama_lengkap`, `nomor_telepon`, `password`, `alamat`, `foto_profil`) VALUES
(1, 'halo123@gmail.com', 'Andika Dwi Prasetya', '085603315076', 'halo12345', 'Desa 3, RT 1, RW 2, Kel. 5, Kec. 6, 7, 8, 4', NULL),
(2, 'user123@gmail.com', 'Agam', '08987654321', 'user123', '', NULL),
(3, 'punten@gmail.com', 'Egi', '0987654321', '$2y$10$cPXNjWTCDbEHqCS75ZrENuxe.mICp/1Cgl8A3vrMPIIkFStc7cqtC', '', NULL),
(4, '111@gmail.com', 'Diko', '0987654321', '$2y$10$C6nxEdVFlHnPiB3AtvU0SORD8XdjYnv3qQ3HIgWIz4OYPhDw1skMG', '', NULL),
(5, '123@gmail.com', '123', '123', '$2y$10$Tb0kv4Sub5VwKEYog7rSNexuAsxFdHhugZ4QzySY9dTlG5ve/XuNu', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `category` enum('kucing','anjing','hamster','kelinci') DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `name`, `price`, `image`, `category`, `stock`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Snappy Tom Makanan Basah 400gr', 20000.00, '../aset/produk/kucing/produk1.png', 'kucing', 25, 'Makanan basah berkualitas tinggi untuk kucing dewasa', '2025-06-01 09:41:53', '2025-06-01 11:24:57'),
(2, 'Cici Makanan Basah 400gr', 18000.00, '../aset/produk/kucing/produk2.png', 'kucing', 30, 'Makanan basah dalam kemasan kaleng', '2025-06-01 09:41:53', '2025-06-01 11:26:53'),
(3, 'Cici Pouch Makanan Basah 85gr', 8000.00, '../aset/produk/kucing/produk3.png', 'kucing', 20, 'Premium wet food untuk kucing', '2025-06-01 09:41:53', '2025-06-01 11:29:03'),
(4, 'Sheba Pouch Makanan Basah 70gr', 12000.00, '../aset/produk/kucing/produk4.png', 'kucing', 18, 'Makanan camilan untuk kucing', '2025-06-01 09:41:53', '2025-06-01 11:31:07'),
(5, 'Whiskas Makanan Basah 85gr', 8000.00, '../aset/produk/kucing/produk5.png', 'kucing', 12, 'Snack untuk kucing, sangat disukai', '2025-06-01 09:41:53', '2025-06-01 11:30:48'),
(6, 'Pet Dog Makanan Basah 400gr', 22000.00, '../aset/produk/anjing/produk1.png', 'anjing', 10, 'Makanan basah untuk anjing dewasa', '2025-06-01 09:41:53', '2025-06-01 14:38:30'),
(7, 'Snappy Tom Makanan Basah 85gr', 8000.00, '../aset/produk/kucing/produk6.png', 'kucing', 40, 'Snack atau camilan kucing', '2025-06-01 09:41:53', '2025-06-01 14:45:23'),
(8, 'Ciao Churu Snack Kucing 14gr', 27000.00, '../aset/produk/kucing/produk7.png', 'kucing', 8, 'Snack kucing untuk sehari-hari', '2025-06-01 09:41:53', '2025-06-01 14:51:27'),
(9, 'Pet Forest Makanan Basah 400gr', 22000.00, '../aset/produk/kucing/produk8.png', 'kucing', 50, 'Makanan kalengan untuk kucing', '2025-06-01 09:41:53', '2025-06-01 14:50:04'),
(10, 'Pasir Markotops 10liter', 65000.00, '../aset/produk/kucing/produk9.png', 'kucing', 45, 'Pasir tanpa bau untuk kucing kesayangan', '2025-06-01 09:41:53', '2025-06-01 14:49:13'),
(11, 'Royal Canin First Age Mother & Babycat 2kg', 340000.00, '../aset/produk/kucing/produk10.png', 'kucing', 35, 'Makanan karungan untuk kucing harian', '2025-06-01 09:41:53', '2025-06-01 14:47:56'),
(12, 'Frontline Obat Tetes Kuku', 110000.00, '../aset/produk/kucing/produk11.png', 'kucing', 22, 'Obat tetes kuku khusus kucing', '2025-06-01 09:41:53', '2025-06-01 14:47:09'),
(13, 'Makanan Hamster Premium 85gr', 18000.00, '../aset/produk/hamster/produk1.png', 'hamster', 30, 'Makanan premium untuk hamster', '2025-06-01 09:41:53', '2025-06-01 09:41:53'),
(14, 'Vitamin Kelinci 100ml', 25000.00, '../aset/produk/kelinci/produk1.png', 'kelinci', 15, 'Vitamin cair untuk menjaga kesehatan kelinci', '2025-06-01 09:41:53', '2025-06-01 09:41:53'),
(15, 'Friskies Seafood Sensations 3kg', 20000.00, '../aset/produk/kucing/produk12.png', 'kucing', 22, 'Makanan kering karungan khusus kucing', '2025-06-01 09:41:53', '2025-06-01 14:53:23'),
(16, 'Pasir Kucing Kawan 5liter', 35000.00, '../aset/produk/kucing/produk13.png', 'kucing', 22, 'Pasir khusus kucing', '2025-06-01 09:41:53', '2025-06-01 14:54:06'),
(17, 'C-one Concentrate Shampoo 1liter', 80000.00, '../aset/produk/kucing/produk14.png', 'kucing', 22, 'Shampoo khusus hewan', '2025-06-01 09:41:53', '2025-06-01 14:57:41'),
(18, 'Sabina Ocean Fish Repack 1kg', 33000.00, '../aset/produk/kucing/produk15.png', 'kucing', 22, 'Makanan kering khusus kucing dewasa', '2025-06-01 09:41:53', '2025-06-01 14:55:35'),
(19, 'Life Cat Creamy 2pcs x 15gr', 9000.00, '../aset/produk/kucing/produk16.png', 'kucing', 22, 'Makanan snack kucing', '2025-06-01 09:41:53', '2025-06-01 15:23:17'),
(20, 'Bio Creamy 4pcs x 15gr', 16000.00, '../aset/produk/kucing/produk17.png', 'kucing', 22, 'Makanan snack kucing', '2025-06-01 09:41:53', '2025-06-01 15:23:54'),
(21, 'Makanan Kucing Life Cat 400gr', 15000.00, '../aset/produk/kucing/produk18.png', 'kucing', 22, 'Makanan basah kucing', '2025-06-01 09:41:53', '2025-06-01 15:24:43'),
(22, 'Life Cat for Adult 85g', 6000.00, '../aset/produk/kucing/produk19.png', 'kucing', 22, 'Makanan snack kucing sehari-hari', '2025-06-01 09:41:53', '2025-06-01 15:26:28'),
(23, 'Fancy Feast Makanan  Basah 85gr', 18000.00, '../aset/produk/kucing/produk20.png', 'kucing', 22, 'Makanan kucing', '2025-06-01 09:41:53', '2025-06-01 15:27:07'),
(24, 'Whiskas Mackerel Perisa Kembung 480gr', 35000.00, '../aset/produk/kucing/produk21.png', 'kucing', 22, 'Makanan kering khusus kucing', '2025-06-01 09:41:53', '2025-06-01 15:28:01'),
(25, 'Friskies Meaty Grill 1,2kg', 72000.00, '../aset/produk/kucing/produk22.png', 'kucing', 22, 'Makanan kucing karungan', '2025-06-01 09:41:53', '2025-06-01 15:28:33'),
(26, 'Machiko 225ml Tearless Shampoo', 72000.00, '../aset/produk/kucing/produk23.png', 'kucing', 22, 'Shampoo khusus kucing', '2025-06-01 09:41:53', '2025-06-01 15:30:15'),
(27, 'Unicharm Pet Deo Toilet Pasir 2liter', 80000.00, '../aset/produk/kucing/produk24.png', 'kucing', 22, 'Pasir khusus untuk kotoran hewan peliharaan', '2025-06-01 09:41:53', '2025-06-01 15:31:26'),
(28, 'Feeding Kit Pelontar Pil Tablet', 25000.00, '../aset/produk/kucing/produk25.png', 'kucing', 22, 'Obat untuk menjaga kesehatan kewan peliharaan kita', '2025-06-01 09:41:53', '2025-06-01 15:32:16'),
(29, 'Serokan Pasir DC-625 untuk Kucing', 10000.00, '../aset/produk/kucing/produk26.png', 'kucing', 22, 'Serok khusus untuk mengambil kotoran hewan kesayangan', '2025-06-01 09:41:53', '2025-06-01 15:33:17'),
(30, 'Anti-Fungal Spray Powder 150ml', 55000.00, '../aset/produk/kucing/produk27.png', 'kucing', 22, 'Spray khusus', '2025-06-01 09:41:53', '2025-06-01 15:34:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
