-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 03:11 PM
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
-- Database: `reservasi_lapangan_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id_fasilitas` int(11) NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id_fasilitas`, `nama_fasilitas`) VALUES
(1, 'Loker_Penyimpanan'),
(2, 'Papan_Skor_Digital');

-- --------------------------------------------------------

--
-- Table structure for table `gor`
--

CREATE TABLE `gor` (
  `id_gor` int(11) NOT NULL,
  `nama_gor` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gor`
--

INSERT INTO `gor` (`id_gor`, `nama_gor`, `alamat`, `kota`, `no_telepon`, `deskripsi`) VALUES
(1, 'GOR UNNES Training Center', 'Jl. Sekaran Raya, Gunungpati', 'Semarang', '024-850809', 'Pusat pelatihan olahraga mahasiswa dan umum dengan standar nasional.'),
(2, 'GOR UNDIP Sport Center', 'Jl. Prof. Soedarto, Tembalang', 'Semarang', '024-746004', 'Fasilitas olahraga lengkap untuk mahasiswa UNDIP dan masyarakat umum.');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_lapangan` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('tersedia','dibooking') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_lapangan`, `tanggal`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(1, 1, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(2, 1, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(3, 1, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(4, 1, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(5, 1, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(6, 1, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(7, 1, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(8, 1, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(9, 2, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(10, 2, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(11, 2, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(12, 2, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(13, 2, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(14, 2, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(15, 2, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(16, 2, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(17, 3, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(18, 3, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(19, 3, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(20, 3, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(21, 3, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(22, 3, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(23, 3, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(24, 3, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(25, 4, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(26, 4, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(27, 4, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(28, 4, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(29, 4, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(30, 4, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(31, 4, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(32, 4, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(33, 5, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(34, 5, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(35, 5, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(36, 5, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(37, 5, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(38, 5, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(39, 5, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(40, 5, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(41, 6, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(42, 6, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(43, 6, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(44, 6, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(45, 6, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(46, 6, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(47, 6, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(48, 6, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(49, 7, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(50, 7, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(51, 7, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(52, 7, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(53, 7, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(54, 7, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(55, 7, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(56, 7, '2024-12-01', '15:00:00', '16:00:00', 'tersedia'),
(57, 8, '2024-12-01', '08:00:00', '09:00:00', 'tersedia'),
(58, 8, '2024-12-01', '09:00:00', '10:00:00', 'tersedia'),
(59, 8, '2024-12-01', '10:00:00', '11:00:00', 'tersedia'),
(60, 8, '2024-12-01', '11:00:00', '12:00:00', 'tersedia'),
(61, 8, '2024-12-01', '12:00:00', '13:00:00', 'tersedia'),
(62, 8, '2024-12-01', '13:00:00', '14:00:00', 'tersedia'),
(63, 8, '2024-12-01', '14:00:00', '15:00:00', 'tersedia'),
(64, 8, '2024-12-01', '15:00:00', '16:00:00', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id_lapangan` int(11) NOT NULL,
  `id_gor` int(11) DEFAULT NULL,
  `nama_lapangan` varchar(100) DEFAULT NULL,
  `jenis_lapangan` enum('futsal','voli','badminton','basket') DEFAULT NULL,
  `harga_per_jam` decimal(10,2) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id_lapangan`, `id_gor`, `nama_lapangan`, `jenis_lapangan`, `harga_per_jam`, `status`) VALUES
(1, 1, 'Lapangan Futsal UNNES', 'futsal', 150000.00, 'aktif'),
(2, 1, 'Lapangan Voli UNNES', 'voli', 80000.00, 'aktif'),
(3, 1, 'Lapangan Badminton UNNES', 'badminton', 50000.00, 'aktif'),
(4, 1, 'Lapangan Basket UNNES', 'basket', 120000.00, 'aktif'),
(5, 2, 'Lapangan Futsal UNDIP', 'futsal', 200000.00, 'aktif'),
(6, 2, 'Lapangan Voli UNDIP', 'voli', 100000.00, 'aktif'),
(7, 2, 'Lapangan Badminton UNDIP', 'badminton', 100000.00, 'aktif'),
(8, 2, 'Lapangan Basket UNDIP', 'basket', 150000.00, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `lapangan_fasilitas`
--

CREATE TABLE `lapangan_fasilitas` (
  `id_lapangan` int(11) NOT NULL,
  `id_fasilitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan_fasilitas`
--

INSERT INTO `lapangan_fasilitas` (`id_lapangan`, `id_fasilitas`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_reservasi` int(11) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `total_bayar` decimal(10,2) DEFAULT NULL,
  `status_pembayaran` enum('menunggu','berhasil','gagal') DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_jadwal` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `tanggal_reservasi` date DEFAULT NULL,
  `status_reservasi` enum('dibooking','selesai','dibatalkan') DEFAULT 'dibooking',
  `kode_booking` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi_fasilitas`
--

CREATE TABLE `reservasi_fasilitas` (
  `id_reservasi` int(11) NOT NULL,
  `id_fasilitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `admin_email_unique` (`email`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id_fasilitas`);

--
-- Indexes for table `gor`
--
ALTER TABLE `gor`
  ADD PRIMARY KEY (`id_gor`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `jadwal_id_lapangan_foreign` (`id_lapangan`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id_lapangan`),
  ADD KEY `lapangan_id_gor_foreign` (`id_gor`);

--
-- Indexes for table `lapangan_fasilitas`
--
ALTER TABLE `lapangan_fasilitas`
  ADD PRIMARY KEY (`id_lapangan`,`id_fasilitas`),
  ADD KEY `lapangan_fasilitas_id_fasilitas_foreign` (`id_fasilitas`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `pelanggan_email_unique` (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `pembayaran_id_reservasi_unique` (`id_reservasi`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `reservasi_id_pelanggan_foreign` (`id_pelanggan`),
  ADD KEY `reservasi_id_admin_foreign` (`id_admin`),
  ADD KEY `reservasi_id_jadwal_foreign` (`id_jadwal`);

--
-- Indexes for table `reservasi_fasilitas`
--
ALTER TABLE `reservasi_fasilitas`
  ADD PRIMARY KEY (`id_reservasi`,`id_fasilitas`),
  ADD KEY `id_fasilitas` (`id_fasilitas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id_fasilitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gor`
--
ALTER TABLE `gor`
  MODIFY `id_gor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id_lapangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD CONSTRAINT `lapangan_id_gor_foreign` FOREIGN KEY (`id_gor`) REFERENCES `gor` (`id_gor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lapangan_fasilitas`
--
ALTER TABLE `lapangan_fasilitas`
  ADD CONSTRAINT `lapangan_fasilitas_id_fasilitas_foreign` FOREIGN KEY (`id_fasilitas`) REFERENCES `fasilitas` (`id_fasilitas`) ON DELETE CASCADE,
  ADD CONSTRAINT `lapangan_fasilitas_id_lapangan_foreign` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id_lapangan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_id_reservasi_foreign` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `reservasi_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id_jadwal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservasi_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Constraints for table `reservasi_fasilitas`
--
ALTER TABLE `reservasi_fasilitas`
  ADD CONSTRAINT `reservasi_fasilitas_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_fasilitas_ibfk_2` FOREIGN KEY (`id_fasilitas`) REFERENCES `fasilitas` (`id_fasilitas`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
