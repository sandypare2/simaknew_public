-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 05, 2026 at 10:29 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simkppcn`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_jenis_pegawai`
--

CREATE TABLE `master_jenis_pegawai` (
  `id` bigint NOT NULL,
  `kd_jenis` varchar(2) DEFAULT NULL,
  `nama_jenis` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_jenis_pegawai`
--

INSERT INTO `master_jenis_pegawai` (`id`, `kd_jenis`, `nama_jenis`) VALUES
(1, '01', 'TUGAS KARYA'),
(2, '02', 'ORGANIK'),
(3, '03', 'PROHIRE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_jenis_pegawai`
--
ALTER TABLE `master_jenis_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_jenis_pegawai`
--
ALTER TABLE `master_jenis_pegawai`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
