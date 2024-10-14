-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 07:32 AM
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
-- Database: `ci4`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(5) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nim_nis` varchar(25) NOT NULL,
  `jenis_user` varchar(100) DEFAULT NULL,
  `instansi_pendidikan` varchar(25) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
  `status` varchar(25) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `lokasi` varchar(50) DEFAULT NULL,
  `checkin_time` varchar(25) NOT NULL,
  `checkout_time` varchar(25) DEFAULT NULL,
  `foto_profile` varchar(255) NOT NULL,
  `foto_absen` varchar(50) NOT NULL,
  `waktu_absen` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `nama_lengkap`, `email`, `nim_nis`, `jenis_user`, `instansi_pendidikan`, `nama_instansi`, `status`, `keterangan`, `lokasi`, `checkin_time`, `checkout_time`, `foto_profile`, `foto_absen`, `waktu_absen`) VALUES
(918, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Izin', 'asdad', NULL, '13:52', NULL, 'profile.png', '2024.07.25 - 13.56.34.jpeg', '2024-07-25'),
(920, 'Daffa Reivan', '', '141141', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Masuk', '', NULL, '10:01', NULL, 'profile.png', '2024.09.09 - 10.01.41.jpeg', '2024-09-09'),
(921, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Izin', 'pulang', NULL, '10:02', NULL, 'profile.png', '2024.09.09 - 10.03.02.jpeg', '2024-09-09'),
(922, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Sakit', 'SAKIT PUSING', NULL, '10:42', NULL, 'profile.png', '2024.09.09 - 10.42.42.jpeg', '2024-09-09'),
(923, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Alpa', 'cabut', NULL, '10:58', NULL, 'profile.png', '2024.09.09 - 10.58.36.jpeg', '2024-09-09'),
(924, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Masuk', '', NULL, '11:06', NULL, 'profile.png', '2024.09.09 - 11.06.43.jpeg', '2024-09-09'),
(925, 'Daffa Reivan', '', '141141', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Masuk', '', NULL, '19:21', NULL, 'profile.png', '2024.09.25 - 19.21.34.jpeg', '2024-09-25'),
(926, 'Daffa Reivan', '', '141141', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Masuk', '', NULL, '10:46', NULL, 'profile.png', '2024.10.14 - 10.46.13.jpeg', '2024-10-14'),
(927, '', '', '', NULL, '', '', '', '', NULL, '', '15:40', '', '', '2024-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `instansi`
--

CREATE TABLE `instansi` (
  `id` int(255) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
  `foto_instansi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instansi`
--

INSERT INTO `instansi` (`id`, `nama_instansi`, `foto_instansi`) VALUES
(1, 'SMK BINA MANDIRI', '2023.12.28 - 08.44.10.jpeg'),
(2, 'SMKN 2 JAKARTA', '2023.12.28 - 08.44.44.jpeg'),
(3, 'UNIVERSITAS TERBUKA', '2023.12.28 - 08.45.22.jpeg'),
(4, 'UNIVERSITAS BINA SARANA INFORMATIKA', '2024.01.18 - 16.29.01.jpeg'),
(5, 'PERGURUAN TINGGI NEGARA KEMENHUB', '2023.12.28 - 08.48.31.jpeg'),
(6, 'SMK TRIMULIA', '2024.01.02 - 10.22.34.jpeg'),
(7, 'SMKN 2 JKT48', '2024.01.02 - 12.48.18.jpeg'),
(8, 'UNIVERSITAS NEGERI JAKARTA', '2024.01.18 - 16.26.48.jpeg'),
(9, 'UNIVERSITAS BINA NUSANTARA', '2024.03.01 - 15.46.34.jpeg'),
(10, 'SMK MUHAMMADIYAH 4 JAKARTA', '2024.03.01 - 15.48.36.jpeg'),
(11, 'SMKN 1 KOTA BEKASI', '2024.10.10 - 19.12.51.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(5) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nim_nis` varchar(25) NOT NULL,
  `jenis_user` varchar(100) DEFAULT NULL,
  `instansi_pendidikan` varchar(25) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
  `status` varchar(25) NOT NULL,
  `kegiatan` text NOT NULL,
  `foto_profile` varchar(255) NOT NULL,
  `waktu_laporan` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `waktu_mulai` varchar(25) NOT NULL,
  `waktu_selesai` varchar(25) DEFAULT NULL,
  `foto_laporan` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `nama_lengkap`, `email`, `nim_nis`, `jenis_user`, `instansi_pendidikan`, `nama_instansi`, `status`, `kegiatan`, `foto_profile`, `waktu_laporan`, `lokasi`, `waktu_mulai`, `waktu_selesai`, `foto_laporan`) VALUES
(5, 'Daffa Reivan', '', '141141', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Closed', 'tes1', 'profile.png', '2024-10-14', 'karya', '', NULL, '2024.10.14 - 11.54.15.png'),
(6, 'Bagoes Sholeh', '', '2214312221', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Progres', 'tes4', 'profile.png', '2024-10-16', 'Cipta', '', NULL, '2024.10.14 - 12.05.01.png'),
(7, 'Chelsea Ramadhani', '', '12222222', 'Student', 'School', 'SMKD1', 'Progres', 'tes5', 'profile.png', '2024-10-14', 'karya', '', NULL, '2024.10.14 - 12.06.46.png'),
(8, 'Nur Aini', '', '2214312', 'Student', 'School', 'SMK SSDD', 'Progres', 'tes', 'profile.png', '2024-10-14', 'Cipta', '', NULL, '2024.10.14 - 12.10.51.png'),
(10, 'Chelsea Ramadhani', '', '12222222', 'Student', 'School', 'SMKD1', 'Progress', 'tes6', 'profile.png', '2024-10-14', 'Merdeka Timur', '', NULL, '2024.10.14 - 12.21.11.png'),
(11, 'Daffa Reivan', '', '141141', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Closed', 'tes7', 'profile.png', '2024-10-14', 'Merdeka Timur', '', NULL, '2024.10.14 - 12.28.27.png'),
(12, 'Chelsea Ramadhani', '', '12222222', 'Student', 'School', 'SMKD1', 'Closed', 'tes8', 'profile.png', '0000-00-00', 'Merdeka Timur', '13:30', '14:30', '2024.10.14 - 12.30.43.png');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `member_id` int(5) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `nim_nis` varchar(20) NOT NULL,
  `no_hp` varchar(13) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Male','Female') NOT NULL,
  `jenis_user` varchar(100) DEFAULT NULL,
  `instansi_pendidikan` varchar(255) NOT NULL COMMENT 'Student/College Student',
  `nama_instansi` varchar(255) NOT NULL,
  `level` enum('Super Admin','Admin','User') DEFAULT 'User',
  `token` varchar(255) DEFAULT NULL,
  `is_verifikasi` enum('yes','no','pending') NOT NULL DEFAULT 'no',
  `tanggal_bergabung` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nama_pembimbing` varchar(255) NOT NULL,
  `no_pembimbing` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Good'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`member_id`, `username`, `password`, `email`, `nama_lengkap`, `nim_nis`, `no_hp`, `foto`, `jenis_kelamin`, `jenis_user`, `instansi_pendidikan`, `nama_instansi`, `level`, `token`, `is_verifikasi`, `tanggal_bergabung`, `updated_at`, `nama_pembimbing`, `no_pembimbing`, `status`) VALUES
(39, 'daffa.reivan', '12345678', 'daffa.sweet100@gmail.com', 'Daffa Reivan', '141141', '085894861350', 'profile.png', 'Male', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Super Admin', NULL, 'yes', '2024-07-15', '2024-07-25 07:06:26', 'Chelsea Ramadhani', '085714108993', 'Written Agreement'),
(54, 'bagoes', '12345678', 'bagoessholehm.s.n@gmail.com', 'Bagoes Sholeh', '2214312221', '085714108993', 'profile.png', 'Male', 'Student', 'School', 'SMKN 1 KOTA BEKASI', 'Admin', NULL, 'yes', '2024-07-16', '2024-07-25 08:14:41', 'Dela Chaerani', '081290624643', 'Terminated'),
(55, 'celsi', '12345678', 'chelsearamadhani@gmail.com', 'Chelsea Ramadhani', '12222222', '087872825833', 'profile.png', 'Female', 'Student', 'School', 'SMKD1', 'User', NULL, 'yes', '2024-07-16', '2024-10-14 02:11:43', 'tes', '08238238291', 'Written Agreement'),
(56, 'sdsjj', '12345678', 'sssa@gmail.com', 'Nur Aini', '2214312', '081290624643', 'profile.png', 'Female', 'Student', 'School', 'SMK SSDD', 'User', NULL, 'yes', '2024-07-16', '2024-07-16 04:17:16', '', '', 'Good'),
(57, 'sds', '12345678', 'chelsearamadhani91@gmail.com', 'Chelsea Ramadhani', '2214315', '085714108997', 'profile.png', 'Female', 'College Student', 'University', 'UNIVERSITAS GAJAH MADA', 'User', NULL, 'yes', '2024-07-18', '2024-10-10 12:05:38', '', '', 'Terminated'),
(58, '', '', '', '', '', '', '', 'Male', NULL, '', '', 'User', NULL, 'no', '2024-07-25', '2024-07-25 06:29:59', '', '', 'Good');

--
-- Triggers `member`
--
DELIMITER $$
CREATE TRIGGER `set_jenis_user_trigger` BEFORE UPDATE ON `member` FOR EACH ROW BEGIN
    IF NEW.instansi_pendidikan = 'School' THEN
        SET NEW.jenis_user = 'Student';
    ELSEIF NEW.instansi_pendidikan = 'University' THEN
        SET NEW.jenis_user = 'College Student';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_insert` BEFORE INSERT ON `member` FOR EACH ROW BEGIN
	IF NEW.instansi_pendidikan = 'School' THEN
    	SET NEW.jenis_user = 'Student';
	ELSEIF NEW.instansi_pendidikan = 'University' THEN
    	SET NEW.jenis_user = 'College Student';
	END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instansi`
--
ALTER TABLE `instansi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=928;

--
-- AUTO_INCREMENT for table `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `member_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
