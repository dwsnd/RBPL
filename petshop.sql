-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 01:49 PM
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
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_lengkap` varchar(200) DEFAULT NULL,
  `nomor_telepon` varchar(15) DEFAULT NULL,
  `password` varchar(75) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `email`, `nama_lengkap`, `nomor_telepon`, `password`) VALUES
(1, 'halo123@gmail.com', 'Andika Dwi Prasetya', '085603315076', 'halo12345'),
(2, 'user123@gmail.com', 'Agam', '08987654321', 'user123'),
(3, 'punten@gmail.com', 'Egi', '0987654321', '$2y$10$cPXNjWTCDbEHqCS75ZrENuxe.mICp/1Cgl8A3vrMPIIkFStc7cqtC'),
(4, '111@gmail.com', 'Diko', '0987654321', '$2y$10$C6nxEdVFlHnPiB3AtvU0SORD8XdjYnv3qQ3HIgWIz4OYPhDw1skMG'),
(5, '123@gmail.com', '123', '123', '$2y$10$Tb0kv4Sub5VwKEYog7rSNexuAsxFdHhugZ4QzySY9dTlG5ve/XuNu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
