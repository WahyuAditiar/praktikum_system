-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 02:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `praktikum_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `praktikum_id` int(11) NOT NULL,
  `nama_praktikum` varchar(100) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `nama_mahasiswa` varchar(100) DEFAULT NULL,
  `jadwal_praktikum_id` int(11) NOT NULL,
  `pertemuan` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','izin','sakit','alfa') DEFAULT 'alfa',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `praktikum_id`, `nama_praktikum`, `kelas`, `mahasiswa_id`, `nama_mahasiswa`, `jadwal_praktikum_id`, `pertemuan`, `group_id`, `tanggal`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(451, 1, 'Prak. Algoritma dan Pemrograman', 'A', 1, 'Ahmad Fadhil', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(452, 1, 'Prak. Algoritma dan Pemrograman', 'A', 2, 'Budi Santoso', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(453, 1, 'Prak. Algoritma dan Pemrograman', 'A', 3, 'Citra Amelia', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(454, 1, 'Prak. Algoritma dan Pemrograman', 'A', 4, 'Dewi Lestari', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(455, 1, 'Prak. Algoritma dan Pemrograman', 'A', 5, 'Eko Prasetyo', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(456, 1, 'Prak. Algoritma dan Pemrograman', 'A', 6, 'Fitri Handayani', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(457, 1, 'Prak. Algoritma dan Pemrograman', 'A', 7, 'Gilang Permana', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(458, 1, 'Prak. Algoritma dan Pemrograman', 'A', 8, 'Hana Rahmawati', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(459, 1, 'Prak. Algoritma dan Pemrograman', 'A', 9, 'Indra Saputra', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(460, 1, 'Prak. Algoritma dan Pemrograman', 'A', 10, 'Joko Pranoto', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(461, 1, 'Prak. Algoritma dan Pemrograman', 'A', 11, 'Kartika Dewi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(462, 1, 'Prak. Algoritma dan Pemrograman', 'A', 12, 'Lukman Hakim', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(463, 1, 'Prak. Algoritma dan Pemrograman', 'A', 13, 'Maya Sari', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(464, 1, 'Prak. Algoritma dan Pemrograman', 'A', 14, 'Nanda Putra', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(465, 1, 'Prak. Algoritma dan Pemrograman', 'A', 15, 'Oktaviani Nur', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(466, 1, 'Prak. Algoritma dan Pemrograman', 'A', 16, 'Putri Ayu', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(467, 1, 'Prak. Algoritma dan Pemrograman', 'A', 17, 'Qori Rahman', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(468, 1, 'Prak. Algoritma dan Pemrograman', 'A', 18, 'Rama Dwi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(469, 1, 'Prak. Algoritma dan Pemrograman', 'A', 19, 'Siti Aminah', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(470, 1, 'Prak. Algoritma dan Pemrograman', 'A', 20, 'Taufik Hidayat', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(471, 1, 'Prak. Algoritma dan Pemrograman', 'A', 21, 'Umar Zain', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(472, 1, 'Prak. Algoritma dan Pemrograman', 'A', 22, 'Vina Lestari', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(473, 1, 'Prak. Algoritma dan Pemrograman', 'A', 23, 'Wahyu Adi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(474, 1, 'Prak. Algoritma dan Pemrograman', 'A', 24, 'Xenia Putri', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(475, 1, 'Prak. Algoritma dan Pemrograman', 'A', 25, 'Yoga Firmansyah', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(476, 1, 'Prak. Algoritma dan Pemrograman', 'A', 26, 'Zahra Amelia', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(477, 1, 'Prak. Algoritma dan Pemrograman', 'A', 27, 'Agus Suryana', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(478, 1, 'Prak. Algoritma dan Pemrograman', 'A', 28, 'Bella Anjani', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(479, 1, 'Prak. Algoritma dan Pemrograman', 'A', 29, 'Cahyo Nugroho', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(480, 1, 'Prak. Algoritma dan Pemrograman', 'A', 30, 'Dian Puspita', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(481, 1, 'Prak. Algoritma dan Pemrograman', 'A', 31, 'Erlangga Pradana', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(482, 1, 'Prak. Algoritma dan Pemrograman', 'A', 32, 'Fani Kusuma', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(483, 1, 'Prak. Algoritma dan Pemrograman', 'A', 33, 'Galih Ramadhan', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(484, 1, 'Prak. Algoritma dan Pemrograman', 'A', 34, 'Hendra Wijaya', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(485, 1, 'Prak. Algoritma dan Pemrograman', 'A', 35, 'Intan Maharani', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(486, 1, 'Prak. Algoritma dan Pemrograman', 'A', 36, 'Julianto Reza', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(487, 1, 'Prak. Algoritma dan Pemrograman', 'A', 37, 'Kiki Anwar', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(488, 1, 'Prak. Algoritma dan Pemrograman', 'A', 38, 'Laras Ningsih', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(489, 1, 'Prak. Algoritma dan Pemrograman', 'A', 39, 'Miko Satria', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(490, 1, 'Prak. Algoritma dan Pemrograman', 'A', 40, 'Nina Pertiwi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(491, 1, 'Prak. Algoritma dan Pemrograman', 'A', 41, 'Omar Rizki', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(492, 1, 'Prak. Algoritma dan Pemrograman', 'A', 42, 'Putra Mahendra', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(493, 1, 'Prak. Algoritma dan Pemrograman', 'A', 43, 'Rizka Aulia', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(494, 1, 'Prak. Algoritma dan Pemrograman', 'A', 44, 'Sandi Wijaya', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(495, 1, 'Prak. Algoritma dan Pemrograman', 'A', 45, 'Tina Marlina', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(496, 1, 'Prak. Algoritma dan Pemrograman', 'A', 46, 'Utami Dwi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(497, 1, 'Prak. Algoritma dan Pemrograman', 'A', 47, 'Vicky Ramlan', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(498, 1, 'Prak. Algoritma dan Pemrograman', 'A', 48, 'Wulan Ayuningtyas', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(499, 1, 'Prak. Algoritma dan Pemrograman', 'A', 49, 'Yuli Pratiwi', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(500, 1, 'Prak. Algoritma dan Pemrograman', 'A', 50, 'Zidan Haryanto', 1, 1, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:08:40', '2025-10-12 16:09:10'),
(501, 1, 'Prak. Algoritma dan Pemrograman', 'A', 1, 'Ahmad Fadhil', 1, 3, 1, '2025-10-12', 'sakit', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(502, 1, 'Prak. Algoritma dan Pemrograman', 'A', 2, 'Budi Santoso', 1, 3, 1, '2025-10-12', 'sakit', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(503, 1, 'Prak. Algoritma dan Pemrograman', 'A', 3, 'Citra Amelia', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(504, 1, 'Prak. Algoritma dan Pemrograman', 'A', 4, 'Dewi Lestari', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(505, 1, 'Prak. Algoritma dan Pemrograman', 'A', 5, 'Eko Prasetyo', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(506, 1, 'Prak. Algoritma dan Pemrograman', 'A', 6, 'Fitri Handayani', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(507, 1, 'Prak. Algoritma dan Pemrograman', 'A', 7, 'Gilang Permana', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(508, 1, 'Prak. Algoritma dan Pemrograman', 'A', 8, 'Hana Rahmawati', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(509, 1, 'Prak. Algoritma dan Pemrograman', 'A', 9, 'Indra Saputra', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(510, 1, 'Prak. Algoritma dan Pemrograman', 'A', 10, 'Joko Pranoto', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(511, 1, 'Prak. Algoritma dan Pemrograman', 'A', 11, 'Kartika Dewi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(512, 1, 'Prak. Algoritma dan Pemrograman', 'A', 12, 'Lukman Hakim', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(513, 1, 'Prak. Algoritma dan Pemrograman', 'A', 13, 'Maya Sari', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(514, 1, 'Prak. Algoritma dan Pemrograman', 'A', 14, 'Nanda Putra', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(515, 1, 'Prak. Algoritma dan Pemrograman', 'A', 15, 'Oktaviani Nur', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(516, 1, 'Prak. Algoritma dan Pemrograman', 'A', 16, 'Putri Ayu', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(517, 1, 'Prak. Algoritma dan Pemrograman', 'A', 17, 'Qori Rahman', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(518, 1, 'Prak. Algoritma dan Pemrograman', 'A', 18, 'Rama Dwi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(519, 1, 'Prak. Algoritma dan Pemrograman', 'A', 19, 'Siti Aminah', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(520, 1, 'Prak. Algoritma dan Pemrograman', 'A', 20, 'Taufik Hidayat', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(521, 1, 'Prak. Algoritma dan Pemrograman', 'A', 21, 'Umar Zain', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(522, 1, 'Prak. Algoritma dan Pemrograman', 'A', 22, 'Vina Lestari', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(523, 1, 'Prak. Algoritma dan Pemrograman', 'A', 23, 'Wahyu Adi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(524, 1, 'Prak. Algoritma dan Pemrograman', 'A', 24, 'Xenia Putri', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(525, 1, 'Prak. Algoritma dan Pemrograman', 'A', 25, 'Yoga Firmansyah', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(526, 1, 'Prak. Algoritma dan Pemrograman', 'A', 26, 'Zahra Amelia', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(527, 1, 'Prak. Algoritma dan Pemrograman', 'A', 27, 'Agus Suryana', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(528, 1, 'Prak. Algoritma dan Pemrograman', 'A', 28, 'Bella Anjani', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(529, 1, 'Prak. Algoritma dan Pemrograman', 'A', 29, 'Cahyo Nugroho', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(530, 1, 'Prak. Algoritma dan Pemrograman', 'A', 30, 'Dian Puspita', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(531, 1, 'Prak. Algoritma dan Pemrograman', 'A', 31, 'Erlangga Pradana', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(532, 1, 'Prak. Algoritma dan Pemrograman', 'A', 32, 'Fani Kusuma', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(533, 1, 'Prak. Algoritma dan Pemrograman', 'A', 33, 'Galih Ramadhan', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(534, 1, 'Prak. Algoritma dan Pemrograman', 'A', 34, 'Hendra Wijaya', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(535, 1, 'Prak. Algoritma dan Pemrograman', 'A', 35, 'Intan Maharani', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(536, 1, 'Prak. Algoritma dan Pemrograman', 'A', 36, 'Julianto Reza', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(537, 1, 'Prak. Algoritma dan Pemrograman', 'A', 37, 'Kiki Anwar', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(538, 1, 'Prak. Algoritma dan Pemrograman', 'A', 38, 'Laras Ningsih', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(539, 1, 'Prak. Algoritma dan Pemrograman', 'A', 39, 'Miko Satria', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(540, 1, 'Prak. Algoritma dan Pemrograman', 'A', 40, 'Nina Pertiwi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(541, 1, 'Prak. Algoritma dan Pemrograman', 'A', 41, 'Omar Rizki', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(542, 1, 'Prak. Algoritma dan Pemrograman', 'A', 42, 'Putra Mahendra', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(543, 1, 'Prak. Algoritma dan Pemrograman', 'A', 43, 'Rizka Aulia', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(544, 1, 'Prak. Algoritma dan Pemrograman', 'A', 44, 'Sandi Wijaya', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(545, 1, 'Prak. Algoritma dan Pemrograman', 'A', 45, 'Tina Marlina', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(546, 1, 'Prak. Algoritma dan Pemrograman', 'A', 46, 'Utami Dwi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(547, 1, 'Prak. Algoritma dan Pemrograman', 'A', 47, 'Vicky Ramlan', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(548, 1, 'Prak. Algoritma dan Pemrograman', 'A', 48, 'Wulan Ayuningtyas', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(549, 1, 'Prak. Algoritma dan Pemrograman', 'A', 49, 'Yuli Pratiwi', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(550, 1, 'Prak. Algoritma dan Pemrograman', 'A', 50, 'Zidan Haryanto', 1, 3, 1, '2025-10-12', 'hadir', '', '2025-10-11 19:10:34', '2025-10-12 16:09:10'),
(551, 1, 'Prak. Algoritma dan Pemrograman', 'A', 1, 'Ahmad Fadhil', 1, 2, 1, '2025-10-11', 'izin', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(552, 1, 'Prak. Algoritma dan Pemrograman', 'A', 2, 'Budi Santoso', 1, 2, 1, '2025-10-11', 'izin', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(553, 1, 'Prak. Algoritma dan Pemrograman', 'A', 3, 'Citra Amelia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(554, 1, 'Prak. Algoritma dan Pemrograman', 'A', 4, 'Dewi Lestari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(555, 1, 'Prak. Algoritma dan Pemrograman', 'A', 5, 'Eko Prasetyo', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(556, 1, 'Prak. Algoritma dan Pemrograman', 'A', 6, 'Fitri Handayani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(557, 1, 'Prak. Algoritma dan Pemrograman', 'A', 7, 'Gilang Permana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(558, 1, 'Prak. Algoritma dan Pemrograman', 'A', 8, 'Hana Rahmawati', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(559, 1, 'Prak. Algoritma dan Pemrograman', 'A', 9, 'Indra Saputra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(560, 1, 'Prak. Algoritma dan Pemrograman', 'A', 10, 'Joko Pranoto', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(561, 1, 'Prak. Algoritma dan Pemrograman', 'A', 11, 'Kartika Dewi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(562, 1, 'Prak. Algoritma dan Pemrograman', 'A', 12, 'Lukman Hakim', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(563, 1, 'Prak. Algoritma dan Pemrograman', 'A', 13, 'Maya Sari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(564, 1, 'Prak. Algoritma dan Pemrograman', 'A', 14, 'Nanda Putra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(565, 1, 'Prak. Algoritma dan Pemrograman', 'A', 15, 'Oktaviani Nur', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(566, 1, 'Prak. Algoritma dan Pemrograman', 'A', 16, 'Putri Ayu', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(567, 1, 'Prak. Algoritma dan Pemrograman', 'A', 17, 'Qori Rahman', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(568, 1, 'Prak. Algoritma dan Pemrograman', 'A', 18, 'Rama Dwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(569, 1, 'Prak. Algoritma dan Pemrograman', 'A', 19, 'Siti Aminah', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(570, 1, 'Prak. Algoritma dan Pemrograman', 'A', 20, 'Taufik Hidayat', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(571, 1, 'Prak. Algoritma dan Pemrograman', 'A', 21, 'Umar Zain', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(572, 1, 'Prak. Algoritma dan Pemrograman', 'A', 22, 'Vina Lestari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(573, 1, 'Prak. Algoritma dan Pemrograman', 'A', 23, 'Wahyu Adi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(574, 1, 'Prak. Algoritma dan Pemrograman', 'A', 24, 'Xenia Putri', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(575, 1, 'Prak. Algoritma dan Pemrograman', 'A', 25, 'Yoga Firmansyah', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(576, 1, 'Prak. Algoritma dan Pemrograman', 'A', 26, 'Zahra Amelia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(577, 1, 'Prak. Algoritma dan Pemrograman', 'A', 27, 'Agus Suryana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(578, 1, 'Prak. Algoritma dan Pemrograman', 'A', 28, 'Bella Anjani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(579, 1, 'Prak. Algoritma dan Pemrograman', 'A', 29, 'Cahyo Nugroho', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(580, 1, 'Prak. Algoritma dan Pemrograman', 'A', 30, 'Dian Puspita', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(581, 1, 'Prak. Algoritma dan Pemrograman', 'A', 31, 'Erlangga Pradana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(582, 1, 'Prak. Algoritma dan Pemrograman', 'A', 32, 'Fani Kusuma', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(583, 1, 'Prak. Algoritma dan Pemrograman', 'A', 33, 'Galih Ramadhan', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(584, 1, 'Prak. Algoritma dan Pemrograman', 'A', 34, 'Hendra Wijaya', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(585, 1, 'Prak. Algoritma dan Pemrograman', 'A', 35, 'Intan Maharani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(586, 1, 'Prak. Algoritma dan Pemrograman', 'A', 36, 'Julianto Reza', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(587, 1, 'Prak. Algoritma dan Pemrograman', 'A', 37, 'Kiki Anwar', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(588, 1, 'Prak. Algoritma dan Pemrograman', 'A', 38, 'Laras Ningsih', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(589, 1, 'Prak. Algoritma dan Pemrograman', 'A', 39, 'Miko Satria', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(590, 1, 'Prak. Algoritma dan Pemrograman', 'A', 40, 'Nina Pertiwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(591, 1, 'Prak. Algoritma dan Pemrograman', 'A', 41, 'Omar Rizki', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(592, 1, 'Prak. Algoritma dan Pemrograman', 'A', 42, 'Putra Mahendra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(593, 1, 'Prak. Algoritma dan Pemrograman', 'A', 43, 'Rizka Aulia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(594, 1, 'Prak. Algoritma dan Pemrograman', 'A', 44, 'Sandi Wijaya', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(595, 1, 'Prak. Algoritma dan Pemrograman', 'A', 45, 'Tina Marlina', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(596, 1, 'Prak. Algoritma dan Pemrograman', 'A', 46, 'Utami Dwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(597, 1, 'Prak. Algoritma dan Pemrograman', 'A', 47, 'Vicky Ramlan', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(598, 1, 'Prak. Algoritma dan Pemrograman', 'A', 48, 'Wulan Ayuningtyas', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(599, 1, 'Prak. Algoritma dan Pemrograman', 'A', 49, 'Yuli Pratiwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(600, 1, 'Prak. Algoritma dan Pemrograman', 'A', 50, 'Zidan Haryanto', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:11:41', '2025-10-12 16:09:10'),
(601, 1, 'Prak. Algoritma dan Pemrograman', 'A', 1, 'Ahmad Fadhil', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(602, 1, 'Prak. Algoritma dan Pemrograman', 'A', 2, 'Budi Santoso', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(603, 1, 'Prak. Algoritma dan Pemrograman', 'A', 3, 'Citra Amelia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(604, 1, 'Prak. Algoritma dan Pemrograman', 'A', 4, 'Dewi Lestari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(605, 1, 'Prak. Algoritma dan Pemrograman', 'A', 5, 'Eko Prasetyo', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(606, 1, 'Prak. Algoritma dan Pemrograman', 'A', 6, 'Fitri Handayani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(607, 1, 'Prak. Algoritma dan Pemrograman', 'A', 7, 'Gilang Permana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(608, 1, 'Prak. Algoritma dan Pemrograman', 'A', 8, 'Hana Rahmawati', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(609, 1, 'Prak. Algoritma dan Pemrograman', 'A', 9, 'Indra Saputra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(610, 1, 'Prak. Algoritma dan Pemrograman', 'A', 10, 'Joko Pranoto', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(611, 1, 'Prak. Algoritma dan Pemrograman', 'A', 11, 'Kartika Dewi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(612, 1, 'Prak. Algoritma dan Pemrograman', 'A', 12, 'Lukman Hakim', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(613, 1, 'Prak. Algoritma dan Pemrograman', 'A', 13, 'Maya Sari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(614, 1, 'Prak. Algoritma dan Pemrograman', 'A', 14, 'Nanda Putra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(615, 1, 'Prak. Algoritma dan Pemrograman', 'A', 15, 'Oktaviani Nur', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(616, 1, 'Prak. Algoritma dan Pemrograman', 'A', 16, 'Putri Ayu', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(617, 1, 'Prak. Algoritma dan Pemrograman', 'A', 17, 'Qori Rahman', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(618, 1, 'Prak. Algoritma dan Pemrograman', 'A', 18, 'Rama Dwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(619, 1, 'Prak. Algoritma dan Pemrograman', 'A', 19, 'Siti Aminah', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(620, 1, 'Prak. Algoritma dan Pemrograman', 'A', 20, 'Taufik Hidayat', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(621, 1, 'Prak. Algoritma dan Pemrograman', 'A', 21, 'Umar Zain', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(622, 1, 'Prak. Algoritma dan Pemrograman', 'A', 22, 'Vina Lestari', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(623, 1, 'Prak. Algoritma dan Pemrograman', 'A', 23, 'Wahyu Adi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(624, 1, 'Prak. Algoritma dan Pemrograman', 'A', 24, 'Xenia Putri', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(625, 1, 'Prak. Algoritma dan Pemrograman', 'A', 25, 'Yoga Firmansyah', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(626, 1, 'Prak. Algoritma dan Pemrograman', 'A', 26, 'Zahra Amelia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(627, 1, 'Prak. Algoritma dan Pemrograman', 'A', 27, 'Agus Suryana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(628, 1, 'Prak. Algoritma dan Pemrograman', 'A', 28, 'Bella Anjani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(629, 1, 'Prak. Algoritma dan Pemrograman', 'A', 29, 'Cahyo Nugroho', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(630, 1, 'Prak. Algoritma dan Pemrograman', 'A', 30, 'Dian Puspita', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(631, 1, 'Prak. Algoritma dan Pemrograman', 'A', 31, 'Erlangga Pradana', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(632, 1, 'Prak. Algoritma dan Pemrograman', 'A', 32, 'Fani Kusuma', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(633, 1, 'Prak. Algoritma dan Pemrograman', 'A', 33, 'Galih Ramadhan', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(634, 1, 'Prak. Algoritma dan Pemrograman', 'A', 34, 'Hendra Wijaya', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(635, 1, 'Prak. Algoritma dan Pemrograman', 'A', 35, 'Intan Maharani', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(636, 1, 'Prak. Algoritma dan Pemrograman', 'A', 36, 'Julianto Reza', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(637, 1, 'Prak. Algoritma dan Pemrograman', 'A', 37, 'Kiki Anwar', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(638, 1, 'Prak. Algoritma dan Pemrograman', 'A', 38, 'Laras Ningsih', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(639, 1, 'Prak. Algoritma dan Pemrograman', 'A', 39, 'Miko Satria', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(640, 1, 'Prak. Algoritma dan Pemrograman', 'A', 40, 'Nina Pertiwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(641, 1, 'Prak. Algoritma dan Pemrograman', 'A', 41, 'Omar Rizki', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(642, 1, 'Prak. Algoritma dan Pemrograman', 'A', 42, 'Putra Mahendra', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(643, 1, 'Prak. Algoritma dan Pemrograman', 'A', 43, 'Rizka Aulia', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(644, 1, 'Prak. Algoritma dan Pemrograman', 'A', 44, 'Sandi Wijaya', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(645, 1, 'Prak. Algoritma dan Pemrograman', 'A', 45, 'Tina Marlina', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(646, 1, 'Prak. Algoritma dan Pemrograman', 'A', 46, 'Utami Dwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(647, 1, 'Prak. Algoritma dan Pemrograman', 'A', 47, 'Vicky Ramlan', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(648, 1, 'Prak. Algoritma dan Pemrograman', 'A', 48, 'Wulan Ayuningtyas', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(649, 1, 'Prak. Algoritma dan Pemrograman', 'A', 49, 'Yuli Pratiwi', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(650, 1, 'Prak. Algoritma dan Pemrograman', 'A', 50, 'Zidan Haryanto', 1, 2, 1, '2025-10-11', 'hadir', '', '2025-10-11 19:12:03', '2025-10-12 16:09:10'),
(651, 1, 'Prak. Algoritma dan Pemrograman', 'A', 14, 'Nanda Putra', 1, 4, 1, '2025-10-12', 'sakit', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(652, 1, 'Prak. Algoritma dan Pemrograman', 'A', 15, 'Oktaviani Nur', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(653, 1, 'Prak. Algoritma dan Pemrograman', 'A', 16, 'Putri Ayu', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(654, 1, 'Prak. Algoritma dan Pemrograman', 'A', 17, 'Qori Rahman', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(655, 1, 'Prak. Algoritma dan Pemrograman', 'A', 18, 'Rama Dwi', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(656, 1, 'Prak. Algoritma dan Pemrograman', 'A', 19, 'Siti Aminah', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(657, 1, 'Prak. Algoritma dan Pemrograman', 'A', 20, 'Taufik Hidayat', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(658, 1, 'Prak. Algoritma dan Pemrograman', 'A', 21, 'Umar Zain', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(659, 1, 'Prak. Algoritma dan Pemrograman', 'A', 22, 'Vina Lestari', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(660, 1, 'Prak. Algoritma dan Pemrograman', 'A', 23, 'Wahyu Adi', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(661, 1, 'Prak. Algoritma dan Pemrograman', 'A', 24, 'Xenia Putri', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(662, 1, 'Prak. Algoritma dan Pemrograman', 'A', 25, 'Yoga Firmansyah', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(663, 1, 'Prak. Algoritma dan Pemrograman', 'A', 26, 'Zahra Amelia', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(664, 1, 'Prak. Algoritma dan Pemrograman', 'A', 39, 'Miko Satria', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(665, 1, 'Prak. Algoritma dan Pemrograman', 'A', 40, 'Nina Pertiwi', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(666, 1, 'Prak. Algoritma dan Pemrograman', 'A', 41, 'Omar Rizki', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(667, 1, 'Prak. Algoritma dan Pemrograman', 'A', 42, 'Putra Mahendra', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(668, 1, 'Prak. Algoritma dan Pemrograman', 'A', 43, 'Rizka Aulia', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(669, 1, 'Prak. Algoritma dan Pemrograman', 'A', 44, 'Sandi Wijaya', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(670, 1, 'Prak. Algoritma dan Pemrograman', 'A', 45, 'Tina Marlina', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(671, 1, 'Prak. Algoritma dan Pemrograman', 'A', 46, 'Utami Dwi', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(672, 1, 'Prak. Algoritma dan Pemrograman', 'A', 47, 'Vicky Ramlan', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(673, 1, 'Prak. Algoritma dan Pemrograman', 'A', 48, 'Wulan Ayuningtyas', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(674, 1, 'Prak. Algoritma dan Pemrograman', 'A', 49, 'Yuli Pratiwi', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(675, 1, 'Prak. Algoritma dan Pemrograman', 'A', 50, 'Zidan Haryanto', 1, 4, 1, '2025-10-12', 'hadir', '', '2025-10-12 16:04:31', '2025-10-12 16:09:10'),
(677, 1, NULL, NULL, 1, NULL, 1, 5, 1, '2025-10-12', 'alfa', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(678, 1, NULL, NULL, 2, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(679, 1, NULL, NULL, 3, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(680, 1, NULL, NULL, 4, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(681, 1, NULL, NULL, 5, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(682, 1, NULL, NULL, 6, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(683, 1, NULL, NULL, 7, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(684, 1, NULL, NULL, 8, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(685, 1, NULL, NULL, 9, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(686, 1, NULL, NULL, 10, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(687, 1, NULL, NULL, 11, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(688, 1, NULL, NULL, 12, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(689, 1, NULL, NULL, 13, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(690, 1, NULL, NULL, 27, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(691, 1, NULL, NULL, 28, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(692, 1, NULL, NULL, 29, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(693, 1, NULL, NULL, 30, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(694, 1, NULL, NULL, 31, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(695, 1, NULL, NULL, 32, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(696, 1, NULL, NULL, 33, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(697, 1, NULL, NULL, 34, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(698, 1, NULL, NULL, 35, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(699, 1, NULL, NULL, 36, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(700, 1, NULL, NULL, 37, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(701, 1, NULL, NULL, 38, NULL, 1, 5, 1, '2025-10-12', 'hadir', '', '2025-10-12 17:07:45', '2025-10-12 17:07:45'),
(702, 1, NULL, NULL, 14, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(703, 1, NULL, NULL, 15, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(704, 1, NULL, NULL, 16, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(705, 1, NULL, NULL, 17, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(706, 1, NULL, NULL, 18, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(707, 1, NULL, NULL, 19, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(708, 1, NULL, NULL, 20, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(709, 1, NULL, NULL, 21, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(710, 1, NULL, NULL, 22, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(711, 1, NULL, NULL, 23, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(712, 1, NULL, NULL, 24, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(713, 1, NULL, NULL, 25, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(714, 1, NULL, NULL, 26, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(715, 1, NULL, NULL, 39, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(716, 1, NULL, NULL, 40, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(717, 1, NULL, NULL, 41, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(718, 1, NULL, NULL, 42, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(719, 1, NULL, NULL, 43, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(720, 1, NULL, NULL, 44, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(721, 1, NULL, NULL, 45, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(722, 1, NULL, NULL, 46, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(723, 1, NULL, NULL, 47, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(724, 1, NULL, NULL, 48, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(725, 1, NULL, NULL, 49, NULL, 1, 5, 2, '2025-10-12', 'hadir', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29'),
(726, 1, NULL, NULL, 50, NULL, 1, 5, 2, '2025-10-12', 'alfa', '', '2025-10-12 17:08:29', '2025-10-12 17:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `absen_asisten`
--

CREATE TABLE `absen_asisten` (
  `id` int(10) UNSIGNED NOT NULL,
  `nim` varchar(30) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `praktikum_id` int(11) DEFAULT NULL,
  `praktikum_name` varchar(255) DEFAULT NULL,
  `kelas` varchar(30) DEFAULT NULL,
  `pertemuan` enum('Briefing','1','2','3','4','5','6','7','8','9','10','11','12','13','14','Presentasi Tugas Akhir','Pengisian Nilai Akhir','Praktikum') NOT NULL DEFAULT '1',
  `tanggal` date NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_akhir` time DEFAULT NULL,
  `status_hadir` enum('hadir','izin','sakit','alpha') NOT NULL DEFAULT 'alpha',
  `signature_data` varchar(255) DEFAULT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `laporan_path` varchar(255) DEFAULT NULL,
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absen_asisten`
--

INSERT INTO `absen_asisten` (`id`, `nim`, `nama`, `praktikum_id`, `praktikum_name`, `kelas`, `pertemuan`, `tanggal`, `jam_mulai`, `jam_akhir`, `status_hadir`, `signature_data`, `foto_path`, `laporan_path`, `gps_lat`, `gps_lng`, `tahun_ajaran`, `created_by`, `created_at`, `updated_at`) VALUES
(50, '4521210064', 'Agus Wahyu Prasetyo', NULL, 'Prak. Algoritma dan Pemrograman', 'A', 'Briefing', '2025-10-08', '02:36:00', NULL, 'hadir', 'absen_asisten/signature_4521210064_Briefing_1759865814.png', 'absen_asisten/foto_4521210064_Briefing_1759865814.jpg', '', -6.3668220, 106.7941890, NULL, 'Agus Wahyu Prasetyo', '2025-10-07 21:36:54', NULL),
(51, '4521210064', 'Agus Wahyu Prasetyo', NULL, 'Prak. Algoritma dan Pemrograman', 'A', '1', '2025-10-08', '02:38:00', NULL, 'hadir', 'absen_asisten/signature_4521210064_1_1759865940.png', 'absen_asisten/foto_4521210064_1_1759865940.jpg', '', -6.3668220, 106.7941890, NULL, 'Agus Wahyu Prasetyo', '2025-10-07 21:39:00', NULL),
(52, '4521210064', 'Agus Wahyu Prasetyo', NULL, 'Prak. Algoritma dan Pemrograman', 'A', '2', '2025-10-08', '03:01:00', '03:01:00', 'hadir', 'absen_asisten/signature_4521210064_2_1759867297.png', 'absen_asisten/foto_4521210064_2_1759867297.jpg', '', -6.3668220, 106.7941890, NULL, 'Agus Wahyu Prasetyo', '2025-10-07 22:01:37', '2025-10-08 03:01:54'),
(53, '4521210076', 'Verdianto Karnadi Wibowo', NULL, 'Prak. Algoritma dan Pemrograman', 'A', 'Briefing', '2025-10-08', '03:03:00', '03:03:00', 'hadir', 'absen_asisten/signature_4521210076_Briefing_1759867380.png', 'absen_asisten/foto_4521210076_Briefing_1759867380.jpg', '', -6.3668220, 106.7941890, NULL, 'Verdianto Karnadi Wibowo', '2025-10-07 22:03:00', '2025-10-08 03:03:27'),
(54, '4523210132', 'M Akbar Ramadhan Ola Sili', NULL, 'Prak. Basis Data', 'G', 'Briefing', '2025-10-10', '00:56:00', '00:57:00', 'hadir', 'absen_asisten/signature_4523210132_Briefing_1760032601.png', 'absen_asisten/foto_4523210132_Briefing_1760032601.jpg', '', -6.3897600, 106.7941890, NULL, 'M Akbar Ramadhan Ola Sili', '2025-10-09 19:56:41', '2025-10-10 00:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `asisten_praktikum`
--

CREATE TABLE `asisten_praktikum` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `praktikum_id` int(11) DEFAULT NULL,
  `nama_praktikum` varchar(100) NOT NULL,
  `kelas` enum('A','B','C','D','E','G','H') NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `tahun_ajaran` varchar(50) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asisten_praktikum`
--

INSERT INTO `asisten_praktikum` (`id`, `nim`, `nama`, `praktikum_id`, `nama_praktikum`, `kelas`, `semester`, `tahun_ajaran`, `status`, `created_at`) VALUES
(19, '4521210064', 'Agus Wahyu Prasetyo', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:42:21'),
(20, '4521210076', 'Verdianto Karnadi Wibowo', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:42:48'),
(21, '4521210009', 'M Adyatma Widyadhana', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:43:08'),
(22, '4521210013', 'Valerie Audry Hidayat', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:43:27'),
(23, '4523210057', 'Kevin Khozimah Zaki', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:43:47'),
(24, '4522210062', 'Sakahayu Pribadi', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:43:59'),
(25, '4524210069', 'Muhammad Shehan Algi', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:44:16'),
(26, '4523210052', 'Handra Putra Alma', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:44:39'),
(27, '4524210050', 'Kornelius Timothy Setiawan', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:45:01'),
(28, '4524210021', 'Bunga Putri Nuriman', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:45:20'),
(29, '4524210053', 'Naila Putri Fahel', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:45:35'),
(30, '4524210054', 'Muhamad Edvin Hidayat', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:45:48'),
(31, '4524210031', 'Evelin Ade Oktalia', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:46:02'),
(32, '4524210096', 'Sarah Syafitri Hilmi', 1, 'Prak. Algoritma dan Pemrograman', 'B', 'Ganjil', '2025/2026', 'active', '2025-10-05 08:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `jurusan` varchar(50) NOT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `status` enum('tetap','tidak_tetap','inactive') DEFAULT 'tetap',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nidn`, `nama`, `email`, `no_hp`, `jurusan`, `bidang_keahlian`, `status`, `created_at`) VALUES
(16, '0012524545', 'Dr. Ionia Veritawati, S.Si., M.T', NULL, '088784564564564', '', NULL, 'tetap', '2025-09-23 15:43:21'),
(19, '2543565578', 'Tundo, M.Kom', NULL, '435675574321342', '', NULL, 'tidak_tetap', '2025-09-23 15:45:12'),
(20, '1155115616', 'Dr. Dyah Sulistyowati Rahayu, S.Kom., M.Kom', NULL, '087777454564654', '', NULL, 'tetap', '2025-09-23 18:50:27'),
(21, '3434234234', 'Desti Fitriati, S.Kom., M.Kom', NULL, '423423423423423', '', NULL, 'tetap', '2025-09-23 18:51:12'),
(22, '4234546666', 'Dr. Dra. Andiani, M.Kom', NULL, '213521353132123', '', NULL, 'tetap', '2025-09-23 18:51:40'),
(23, '3242342342', 'Dra. Sri Rezeki C. Nursari, M.Kom', NULL, '135213213213213', '', NULL, 'tetap', '2025-09-23 18:51:55'),
(24, '4234234234', 'Adi Wahyu Pribadi, S.Si., M.Kom', NULL, '324553453454353', '', NULL, 'tetap', '2025-09-23 18:53:10'),
(25, '8989798983', 'Dr. Ir. Iman Paryudi, M.Cs', NULL, '324234234234234', '', NULL, 'tetap', '2025-09-23 18:53:39'),
(26, '3248675543', 'Dr. Bambang Hariyanto', NULL, '342423423424234', '', NULL, 'tetap', '2025-09-23 18:54:06'),
(27, '4290384092', 'Bambang Riono A, S.Kom., M.M.S.I', NULL, '234234121455', '', NULL, 'tetap', '2025-09-23 18:54:47'),
(28, '6543454232', 'Ninuk Wiliani, P.Hd', NULL, '234234234234234', '', NULL, 'tetap', '2025-09-23 18:55:18'),
(29, '8294732894', 'Gregorius Hendita A. K., S.Si., M.Cs', NULL, '23423452535354', '', NULL, 'tetap', '2025-09-23 18:55:52'),
(30, '3432423423', 'Amir Murtako, S.Kom., M.Kom', NULL, '23423423423423', '', NULL, 'tetap', '2025-09-23 18:56:15'),
(31, '2342342342', 'Febri Maspiyanti, S.Kom., M.Kom', NULL, '342553451321321', '', NULL, 'tetap', '2025-09-23 18:56:36'),
(32, '3453454676', 'Warsim', NULL, '54564564654564', '', NULL, 'tidak_tetap', '2025-09-23 18:56:50');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kuliah`
--

CREATE TABLE `jadwal_kuliah` (
  `id` int(11) NOT NULL,
  `mata_kuliah_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan_id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `status` enum('active','canceled','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_kuliah`
--

INSERT INTO `jadwal_kuliah` (`id`, `mata_kuliah_id`, `hari`, `jam_mulai`, `jam_selesai`, `ruangan_id`, `dosen_id`, `kelas`, `status`, `created_at`) VALUES
(29, 17, 'Jumat', '08:00:00', '11:20:00', 7, 25, 'B', 'active', '2025-10-02 16:50:10'),
(30, 18, 'Jumat', '13:00:00', '14:40:00', 13, 30, 'B', 'active', '2025-10-02 16:51:16'),
(31, 19, 'Jumat', '13:00:00', '15:30:00', 6, 16, 'A', 'active', '2025-10-02 16:51:49'),
(33, 10, 'Jumat', '08:00:00', '08:50:00', 9, 28, 'B', 'active', '2025-10-02 16:55:13'),
(34, 19, 'Jumat', '13:00:00', '15:30:00', 9, 20, 'B', 'active', '2025-10-02 16:56:03'),
(35, 10, 'Jumat', '08:00:00', '10:30:00', 10, 22, 'A', 'active', '2025-10-02 16:57:17'),
(36, 17, 'Jumat', '08:00:00', '09:40:00', 11, 20, 'G', 'active', '2025-10-02 16:59:38'),
(37, 17, 'Jumat', '08:00:00', '09:40:00', 12, 31, 'A', 'active', '2025-10-02 17:00:20'),
(38, 10, 'Jumat', '08:00:00', '08:50:00', 14, 28, 'G', 'active', '2025-10-02 18:24:54');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_praktikum`
--

CREATE TABLE `jadwal_praktikum` (
  `id` int(11) NOT NULL,
  `praktikum_id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `ruangan_id` int(11) NOT NULL,
  `hari` varchar(20) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `kelas` varchar(5) NOT NULL,
  `group` tinyint(1) NOT NULL DEFAULT 1,
  `kode_random` varchar(20) DEFAULT NULL,
  `absen_open_until` datetime DEFAULT NULL,
  `status` enum('active','canceled','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `group_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`group_config`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_praktikum`
--

INSERT INTO `jadwal_praktikum` (`id`, `praktikum_id`, `dosen_id`, `ruangan_id`, `hari`, `jam_mulai`, `jam_selesai`, `kelas`, `group`, `kode_random`, `absen_open_until`, `status`, `created_at`, `updated_at`, `group_config`) VALUES
(1, 1, 24, 15, 'Senin', '09:40:00', '14:40:00', 'A', 0, 'JPMYXH', '2025-10-12 19:49:16', 'active', '2025-10-11 11:07:55', '2025-10-12 11:49:16', '{\"total_groups\":2,\"max_capacity\":30,\"group_schedules\":[]}');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_praktikum_asisten`
--

CREATE TABLE `jadwal_praktikum_asisten` (
  `id` int(11) NOT NULL,
  `jadwal_id` int(11) NOT NULL,
  `asisten_id` int(11) NOT NULL,
  `is_penanggungjawab` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `praktikum_id` int(11) DEFAULT NULL,
  `semester` enum('Gasal','Genap') NOT NULL,
  `tahun_akademik` varchar(9) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `created_by` enum('mahasiswa','staff_lab') NOT NULL DEFAULT 'mahasiswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `jadwal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `kelas`, `email`, `praktikum_id`, `semester`, `tahun_akademik`, `prodi`, `created_by`, `created_at`, `updated_at`, `jadwal_id`) VALUES
(1, '2310001', 'Ahmad Fadhil', 'A', 'ahmad.fadhil@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(2, '2310002', 'Budi Santoso', 'A', 'budi.santoso@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(3, '2310003', 'Citra Amelia', 'A', 'citra.amelia@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(4, '2310004', 'Dewi Lestari', 'A', 'dewi.lestari@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(5, '2310005', 'Eko Prasetyo', 'A', 'eko.prasetyo@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(6, '2310006', 'Fitri Handayani', 'A', 'fitri.handayani@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(7, '2310007', 'Gilang Permana', 'A', 'gilang.permana@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(8, '2310008', 'Hana Rahmawati', 'A', 'hana.rahmawati@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(9, '2310009', 'Indra Saputra', 'A', 'indra.saputra@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(10, '2310010', 'Joko Pranoto', 'A', 'joko.pranoto@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(11, '2310011', 'Kartika Dewi', 'A', 'kartika.dewi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(12, '2310012', 'Lukman Hakim', 'A', 'lukman.hakim@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(13, '2310013', 'Maya Sari', 'A', 'maya.sari@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(14, '2310014', 'Nanda Putra', 'A', 'nanda.putra@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(15, '2310015', 'Oktaviani Nur', 'A', 'oktaviani.nur@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(16, '2310016', 'Putri Ayu', 'A', 'putri.ayu@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(17, '2310017', 'Qori Rahman', 'A', 'qori.rahman@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(18, '2310018', 'Rama Dwi', 'A', 'rama.dwi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(19, '2310019', 'Siti Aminah', 'A', 'siti.aminah@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(20, '2310020', 'Taufik Hidayat', 'A', 'taufik.hidayat@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(21, '2310021', 'Umar Zain', 'A', 'umar.zain@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(22, '2310022', 'Vina Lestari', 'A', 'vina.lestari@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(23, '2310023', 'Wahyu Adi', 'A', 'wahyu.adi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(24, '2310024', 'Xenia Putri', 'A', 'xenia.putri@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(25, '2310025', 'Yoga Firmansyah', 'A', 'yoga.firmansyah@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(26, '2310026', 'Zahra Amelia', 'A', 'zahra.amelia@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(27, '2310027', 'Agus Suryana', 'A', 'agus.suryana@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(28, '2310028', 'Bella Anjani', 'A', 'bella.anjani@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(29, '2310029', 'Cahyo Nugroho', 'A', 'cahyo.nugroho@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(30, '2310030', 'Dian Puspita', 'A', 'dian.puspita@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(31, '2310031', 'Erlangga Pradana', 'A', 'erlangga.pradana@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(32, '2310032', 'Fani Kusuma', 'A', 'fani.kusuma@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(33, '2310033', 'Galih Ramadhan', 'A', 'galih.ramadhan@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(34, '2310034', 'Hendra Wijaya', 'A', 'hendra.wijaya@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(35, '2310035', 'Intan Maharani', 'A', 'intan.maharani@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(36, '2310036', 'Julianto Reza', 'A', 'julianto.reza@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(37, '2310037', 'Kiki Anwar', 'A', 'kiki.anwar@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(38, '2310038', 'Laras Ningsih', 'A', 'laras.ningsih@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(39, '2310039', 'Miko Satria', 'A', 'miko.satria@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(40, '2310040', 'Nina Pertiwi', 'A', 'nina.pertiwi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(41, '2310041', 'Omar Rizki', 'A', 'omar.rizki@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(42, '2310042', 'Putra Mahendra', 'A', 'putra.mahendra@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(43, '2310043', 'Rizka Aulia', 'A', 'rizka.aulia@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(44, '2310044', 'Sandi Wijaya', 'A', 'sandi.wijaya@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(45, '2310045', 'Tina Marlina', 'A', 'tina.marlina@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(46, '2310046', 'Utami Dwi', 'A', 'utami.dwi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(47, '2310047', 'Vicky Ramlan', 'A', 'vicky.ramlan@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(48, '2310048', 'Wulan Ayuningtyas', 'A', 'wulan.ayuningtyas@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(49, '2310049', 'Yuli Pratiwi', 'A', 'yuli.pratiwi@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL),
(50, '2310050', 'Zidan Haryanto', 'A', 'zidan.haryanto@example.com', 1, '', '2025/2026', 'Teknik Informatika', 'mahasiswa', '2025-10-11 09:55:45', '2025-10-11 09:55:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` int(11) NOT NULL,
  `kode_mk` varchar(20) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` enum('1','2','3','4','5','6','7','8') NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id`, `kode_mk`, `nama_mk`, `sks`, `semester`, `jurusan`, `deskripsi`, `status`, `created_at`) VALUES
(1, '14536002', 'Komunikasi Data', 2, '3', 'Teknik Informatika', '-', 'active', '2025-10-01 05:02:14'),
(2, '14533003', 'Prak. Basis Data', 2, '3', 'Teknik Informatika', 'Dasar-dasar pemrograman web', 'active', '2025-10-01 06:08:23'),
(3, '14568001', 'Metodologi Penelitian', 3, '6', 'Teknik Informatika', 'Pengenalan basis data', 'active', '2025-10-01 06:08:23'),
(4, '14554002', 'Pemrograman Berbasis Web', 3, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(5, '14536001', 'Arsitektur dan Organisasi Komputer', 2, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(6, '14512002', 'Algoritma dan Pemrograman', 3, '1', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(7, '14557001', 'Sistem Pendukung Keputusan', 3, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(8, '14579016', 'Intelligent system', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(9, '14512003', 'Prak. Algoritma dan Pemrograman', 1, '1', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(10, '14511034', 'Aljabar Linear', 3, '1', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(11, '10052014', 'Kepancasilaan', 2, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(12, '14579004', 'Sistem Kecerdasan Bisnis', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(13, '14579003', 'Secure Programming', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(14, '14573001', 'Keamanan Teknologi Informasi', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(15, '14534001', 'Pemrograman Berorientasi Objek', 3, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(16, '14534002', 'Prak. Pemrograman Berorientasi Objek', 1, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(17, '14531001', 'Statistik dan Probabilitas 1', 2, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(18, '14572001', 'Komputer Grafik', 2, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(19, '14532006', 'Desain dan Analisis Algoritma', 3, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(20, '14554003', 'Prak. Pemrograman Berbasis Web', 1, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(21, '14551001', 'Statistik dan Probabilitas 2', 2, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(22, '14554004', 'Pemrograman Paralel', 2, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(23, '14521002', 'Fisika', 2, '2', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(24, '14569004', 'Pengantar Data Science', 3, '6', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(25, '14533002', 'Basis Data', 3, '3', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(26, '14557002', 'Pembelajaran Mesin', 3, '5', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(27, '14511035', 'Logika Matematika', 3, '1', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(28, '14571001', 'Metode Numerik', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(29, '14579015', 'Multimedia', 3, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(30, '14579001', 'Kerja Praktek', 2, '7', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35'),
(31, '14589001', 'Skripsi', 6, '8', 'Teknik Informatika', '', 'active', '2025-10-01 07:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `praktikum`
--

CREATE TABLE `praktikum` (
  `id` int(11) NOT NULL,
  `mata_kuliah_id` int(11) NOT NULL,
  `nama_praktikum` varchar(150) NOT NULL,
  `semester` enum('ganjil','genap') NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `praktikum`
--

INSERT INTO `praktikum` (`id`, `mata_kuliah_id`, `nama_praktikum`, `semester`, `tahun_ajaran`, `status`, `created_at`, `updated_at`) VALUES
(1, 9, 'Prak. Algoritma dan Pemrograman', 'ganjil', '2025/2026', 'aktif', '2025-10-04 05:01:57', '2025-10-04 05:01:57'),
(2, 2, 'Prak. Basis Data', 'ganjil', '2025/2026', 'aktif', '2025-10-04 05:03:06', '2025-10-04 05:03:06'),
(3, 20, 'Prak. Pemrograman Berbasis Web', 'ganjil', '2025/2026', 'aktif', '2025-10-04 05:04:08', '2025-10-04 05:04:08'),
(4, 16, 'Prak. Pemrograman Berorientasi Objek', 'ganjil', '2025/2026', 'aktif', '2025-10-04 05:04:13', '2025-10-04 05:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `praktikum_enroll`
--

CREATE TABLE `praktikum_enroll` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `jadwal_praktikum_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `praktikum_group_assignment`
--

CREATE TABLE `praktikum_group_assignment` (
  `id` int(11) NOT NULL,
  `jadwal_praktikum_id` int(11) NOT NULL,
  `entity_type` enum('mahasiswa','asisten') NOT NULL,
  `entity_id` int(11) NOT NULL,
  `group_number` int(11) NOT NULL,
  `reference_table` varchar(50) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `praktikum_group_assignment`
--

INSERT INTO `praktikum_group_assignment` (`id`, `jadwal_praktikum_id`, `entity_type`, `entity_id`, `group_number`, `reference_table`, `assigned_by`, `assigned_at`) VALUES
(129, 1, 'asisten', 19, 1, 'asisten_praktikum', 7, '2025-10-12 12:43:18'),
(130, 1, 'asisten', 23, 1, 'asisten_praktikum', 7, '2025-10-12 12:43:18'),
(131, 1, 'asisten', 21, 1, 'asisten_praktikum', 7, '2025-10-12 12:43:18'),
(132, 1, 'mahasiswa', 27, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(133, 1, 'mahasiswa', 1, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(134, 1, 'mahasiswa', 28, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(135, 1, 'mahasiswa', 2, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(136, 1, 'mahasiswa', 29, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(137, 1, 'mahasiswa', 3, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(138, 1, 'mahasiswa', 4, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(139, 1, 'mahasiswa', 30, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(140, 1, 'mahasiswa', 5, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(141, 1, 'mahasiswa', 31, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(142, 1, 'mahasiswa', 32, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(143, 1, 'mahasiswa', 6, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(144, 1, 'mahasiswa', 33, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(145, 1, 'mahasiswa', 7, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(146, 1, 'mahasiswa', 8, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(147, 1, 'mahasiswa', 34, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(148, 1, 'mahasiswa', 9, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(149, 1, 'mahasiswa', 35, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(150, 1, 'mahasiswa', 10, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(151, 1, 'mahasiswa', 36, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(152, 1, 'mahasiswa', 11, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(153, 1, 'mahasiswa', 37, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(154, 1, 'mahasiswa', 38, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(155, 1, 'mahasiswa', 12, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(156, 1, 'mahasiswa', 13, 1, 'mahasiswa', 7, '2025-10-12 12:44:19'),
(157, 1, 'asisten', 24, 2, 'asisten_praktikum', 7, '2025-10-12 12:45:01'),
(158, 1, 'asisten', 22, 2, 'asisten_praktikum', 7, '2025-10-12 12:45:01'),
(159, 1, 'asisten', 20, 2, 'asisten_praktikum', 7, '2025-10-12 12:45:01'),
(160, 1, 'mahasiswa', 39, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(161, 1, 'mahasiswa', 14, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(162, 1, 'mahasiswa', 40, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(163, 1, 'mahasiswa', 15, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(164, 1, 'mahasiswa', 41, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(165, 1, 'mahasiswa', 42, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(166, 1, 'mahasiswa', 16, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(167, 1, 'mahasiswa', 17, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(168, 1, 'mahasiswa', 18, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(169, 1, 'mahasiswa', 43, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(170, 1, 'mahasiswa', 44, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(171, 1, 'mahasiswa', 19, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(172, 1, 'mahasiswa', 20, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(173, 1, 'mahasiswa', 45, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(174, 1, 'mahasiswa', 21, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(175, 1, 'mahasiswa', 46, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(176, 1, 'mahasiswa', 47, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(177, 1, 'mahasiswa', 22, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(178, 1, 'mahasiswa', 23, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(179, 1, 'mahasiswa', 48, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(180, 1, 'mahasiswa', 24, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(181, 1, 'mahasiswa', 25, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(182, 1, 'mahasiswa', 49, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(183, 1, 'mahasiswa', 26, 2, 'mahasiswa', 7, '2025-10-12 12:45:24'),
(184, 1, 'mahasiswa', 50, 2, 'mahasiswa', 7, '2025-10-12 12:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `praktikum_group_config`
--

CREATE TABLE `praktikum_group_config` (
  `id` int(11) NOT NULL,
  `jadwal_praktikum_id` int(11) NOT NULL,
  `config_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config_data`)),
  `updated_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `praktikum_group_config`
--

INSERT INTO `praktikum_group_config` (`id`, `jadwal_praktikum_id`, `config_data`, `updated_by`, `updated_at`, `created_at`) VALUES
(1, 1, '{\"total_groups\":2,\"max_mahasiswa_per_group\":25,\"max_asisten_per_group\":2,\"auto_assignment\":true}', 7, '2025-10-12 12:43:49', '2025-10-12 11:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(11) NOT NULL,
  `kode_ruangan` varchar(20) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `status` enum('active','maintenance','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `kode_ruangan`, `nama_ruangan`, `kapasitas`, `lokasi`, `fasilitas`, `status`, `created_at`) VALUES
(6, '207', 'Smartclass Room - 207', 50, 'Lantai 2', 'Meja, Kursi, Ac, Whiteboard, PC, TV Smart, Proyektor', 'active', '2025-09-23 18:58:15'),
(7, '209', 'Smartclass Room - 209', 50, 'Lantai 2', 'Meja, Kursi, Ac, Whiteboard, PC, TV Smart, Proyektor', 'active', '2025-09-23 18:58:37'),
(9, '309', 'Smartclass Room - 309', 81, 'Lantai 3', 'Meja, Kursi, Ac, Whiteboard, PC, TV Smart, Proyektor', 'active', '2025-10-02 15:56:30'),
(10, '415', 'Kelas Biasa - 415', 80, 'Lantai 4', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 15:59:23'),
(11, '418', 'Kelas Biasa - 418', 50, 'Lantai 4', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 16:00:17'),
(12, '419', 'Kelas Biasa - 419', 70, 'Lantai 4', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 16:01:09'),
(13, '205', 'Kelas Biasa - 205', 45, 'Lantai 2', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 16:02:17'),
(14, '307', 'Smartclass Room - 307', 81, 'Lantai 3', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 16:03:13'),
(15, '211B', 'Kelas Biasa - 211B', 20, 'Lantai 2', 'Meja, Kursi, Ac, Whiteboard, PC, Proyektor', 'active', '2025-10-02 16:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff_lab','staff_prodi','asisten_praktikum') NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `signature_data` longtext DEFAULT NULL,
  `signature_updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama`, `nim`, `status`, `created_at`, `updated_at`, `signature_data`, `signature_updated_at`) VALUES
(6, 'staffprodi', '$2y$10$dTPVg9za87eaIpF8Dnl8z.6a4XNgQ5sk6d7DXMdOXDml5Mka6g53W', 'staff_prodi', 'Ridho Alamsyah', NULL, 'active', '2025-09-23 18:40:59', '2025-09-23 18:40:59', NULL, NULL),
(7, 'admin', '$2y$10$vfjWaL6Htc0HEdlQcQso0e8sF9m7LPw831aPCtBGCgyeq9JmEXM3G', 'admin', 'admin', NULL, 'active', '2025-09-23 19:56:35', '2025-09-23 19:56:35', NULL, NULL),
(8, 'lab', '$2y$10$C11QqPg3lH6p6t49SEwuc.s6ATkQL7ZdmOzJ085KLK5s7OTT/5JQu', 'staff_lab', 'lab', NULL, 'active', '2025-09-24 13:36:32', '2025-09-24 13:36:32', NULL, NULL),
(21, 'Agus Wahyu Prasetyo', '$2y$10$cs9r4C6JSNdDJOZGucqjs.OvUun3RDk/XghIAJ4iBn/KXDhi/4MNe', 'asisten_praktikum', 'Agus Wahyu Prasetyo', '4521210064', 'active', '2025-10-07 19:34:52', '2025-10-07 19:36:08', 'data:image/png;base64,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', '2025-10-07 19:36:08'),
(22, 'Verdianto Karnadi Wibowo', '$2y$10$3yl.jqAYLlj0JLNl8dKCW.od6du5FcJGBfkinQ7sV3S2Ujy2rxBKa', 'asisten_praktikum', 'Verdianto Karnadi Wibowo', '4521210076', 'active', '2025-10-07 20:02:28', '2025-10-07 20:02:45', 'data:image/png;base64,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', '2025-10-07 20:02:45');
INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama`, `nim`, `status`, `created_at`, `updated_at`, `signature_data`, `signature_updated_at`) VALUES
(23, 'M Akbar Ramadhan Ola Sili', '$2y$10$GPBOPsY4PeS0a16eW2Rikea18dOIypUKWVSjSTGwxudEUMaAr2DHK', 'asisten_praktikum', 'M Akbar Ramadhan Ola Sili', '4523210132', 'active', '2025-10-09 17:56:01', '2025-10-09 17:56:21', 'data:image/png;base64,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', '2025-10-09 17:56:21'),
(24, 'Jovan Alfito Praditia', '$2y$10$5Pt1o/xr6sl/SuajPYU0HerA/vp.i2Z9l5K47JWInZNa81VkGKBwa', 'asisten_praktikum', 'Jovan Alfito Praditia', '4523210055', 'active', '2025-10-09 18:00:52', '2025-10-09 18:00:52', NULL, NULL),
(25, 'Devica Putri Hadiyanti', '$2y$10$d.j3XxKNK6OAKQVt440pl.g0LOQI1Zhq4FCzCPHMXP.TNdVxeNTpG', 'asisten_praktikum', 'Devica Putri Hadiyanti', '4523210036', 'active', '2025-10-09 18:01:11', '2025-10-09 18:01:11', NULL, NULL),
(26, 'Chaerul Cahyadi', '$2y$10$IljJoZ6zPzngxr1aD8A/7.jlsO1zW1s4g8j/MSL/s5UQgAZBp5HNG', 'asisten_praktikum', 'Chaerul Cahyadi', '4523210120', 'active', '2025-10-09 18:01:23', '2025-10-09 18:01:23', NULL, NULL),
(27, 'Revalina Adelia', '$2y$10$JAFeiCZ3MtA7KjkFODaCyOnKpmVM5XJbP.MbVQhi1/0WARPAx5yV2', 'asisten_praktikum', 'Revalina Adelia', '4523210091', 'active', '2025-10-09 18:01:37', '2025-10-09 18:01:37', NULL, NULL),
(28, 'Zahra Tsabitah', '$2y$10$mDOo2Hd2vhSCiCyM93gu8ej20hRvWtCFx4pdPwTnHF9LlxXGxLsLK', 'asisten_praktikum', 'Zahra Tsabitah', '4523210145', 'active', '2025-10-09 18:01:54', '2025-10-09 18:01:54', NULL, NULL),
(29, 'Valerie Audry Hidayat', '$2y$10$IS7MyhDZKR2Fyw1AT3HKPe..uglF518sJV2Zb4UQmTBfPrQpHllNO', 'asisten_praktikum', 'Valerie Audry Hidayat', '4521210013', 'active', '2025-10-12 16:00:07', '2025-10-12 16:00:21', 'data:image/png;base64,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', '2025-10-12 16:00:21');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_jadwal_kuliah`
-- (See below for the actual view)
--
CREATE TABLE `view_jadwal_kuliah` (
`id` int(11)
,`nama_mk` varchar(100)
,`hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')
,`jam_mulai` time
,`jam_selesai` time
,`nama_ruangan` varchar(100)
,`dosen` varchar(100)
,`kelas` varchar(10)
,`status` enum('active','canceled','completed')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_jadwal_praktikum`
-- (See below for the actual view)
--
CREATE TABLE `view_jadwal_praktikum` (
`id_jadwal` int(11)
,`praktikum_id` int(11)
,`dosen_id` int(11)
,`ruangan_id` int(11)
,`hari` varchar(20)
,`jam_mulai` time
,`jam_selesai` time
,`kelas` varchar(5)
,`group` tinyint(1)
,`kode_random` varchar(20)
,`absen_open_until` datetime
,`status` enum('active','canceled','completed')
,`created_at` timestamp
,`updated_at` timestamp
,`id_praktikum` int(11)
,`nama_praktikum` varchar(150)
,`id_dosen` int(11)
,`nama_dosen` varchar(100)
,`id_ruangan` int(11)
,`kode_ruangan` varchar(20)
);

-- --------------------------------------------------------

--
-- Structure for view `view_jadwal_kuliah`
--
DROP TABLE IF EXISTS `view_jadwal_kuliah`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_jadwal_kuliah`  AS SELECT `jk`.`id` AS `id`, `mk`.`nama_mk` AS `nama_mk`, `jk`.`hari` AS `hari`, `jk`.`jam_mulai` AS `jam_mulai`, `jk`.`jam_selesai` AS `jam_selesai`, `r`.`nama_ruangan` AS `nama_ruangan`, `d`.`nama` AS `dosen`, `jk`.`kelas` AS `kelas`, `jk`.`status` AS `status` FROM (((`jadwal_kuliah` `jk` join `mata_kuliah` `mk` on(`jk`.`mata_kuliah_id` = `mk`.`id`)) join `ruangan` `r` on(`jk`.`ruangan_id` = `r`.`id`)) join `dosen` `d` on(`jk`.`dosen_id` = `d`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_jadwal_praktikum`
--
DROP TABLE IF EXISTS `view_jadwal_praktikum`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_jadwal_praktikum`  AS SELECT `jp`.`id` AS `id_jadwal`, `jp`.`praktikum_id` AS `praktikum_id`, `jp`.`dosen_id` AS `dosen_id`, `jp`.`ruangan_id` AS `ruangan_id`, `jp`.`hari` AS `hari`, `jp`.`jam_mulai` AS `jam_mulai`, `jp`.`jam_selesai` AS `jam_selesai`, `jp`.`kelas` AS `kelas`, `jp`.`group` AS `group`, `jp`.`kode_random` AS `kode_random`, `jp`.`absen_open_until` AS `absen_open_until`, `jp`.`status` AS `status`, `jp`.`created_at` AS `created_at`, `jp`.`updated_at` AS `updated_at`, `p`.`id` AS `id_praktikum`, `p`.`nama_praktikum` AS `nama_praktikum`, `d`.`id` AS `id_dosen`, `d`.`nama` AS `nama_dosen`, `r`.`id` AS `id_ruangan`, `r`.`kode_ruangan` AS `kode_ruangan` FROM (((`jadwal_praktikum` `jp` left join `praktikum` `p` on(`jp`.`praktikum_id` = `p`.`id`)) left join `dosen` `d` on(`jp`.`dosen_id` = `d`.`id`)) left join `ruangan` `r` on(`jp`.`ruangan_id` = `r`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absensi_praktikum` (`praktikum_id`),
  ADD KEY `fk_absensi_mahasiswa` (`mahasiswa_id`),
  ADD KEY `fk_absensi_jadwal` (`jadwal_praktikum_id`);

--
-- Indexes for table `absen_asisten`
--
ALTER TABLE `absen_asisten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nim` (`nim`),
  ADD KEY `praktikum_id` (`praktikum_id`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indexes for table `asisten_praktikum`
--
ALTER TABLE `asisten_praktikum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `praktikum_id` (`praktikum_id`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidn` (`nidn`),
  ADD KEY `idx_nidn` (`nidn`);

--
-- Indexes for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mata_kuliah_id` (`mata_kuliah_id`),
  ADD KEY `ruangan_id` (`ruangan_id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indexes for table `jadwal_praktikum`
--
ALTER TABLE `jadwal_praktikum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_praktikum_asisten`
--
ALTER TABLE `jadwal_praktikum_asisten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_id` (`jadwal_id`),
  ADD KEY `asisten_id` (`asisten_id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `praktikum_id` (`praktikum_id`);

--
-- Indexes for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_mk` (`kode_mk`);

--
-- Indexes for table `praktikum`
--
ALTER TABLE `praktikum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_praktikum_mk` (`mata_kuliah_id`);

--
-- Indexes for table `praktikum_enroll`
--
ALTER TABLE `praktikum_enroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enroll_mahasiswa` (`mahasiswa_id`),
  ADD KEY `fk_enroll_jadwal` (`jadwal_praktikum_id`);

--
-- Indexes for table `praktikum_group_assignment`
--
ALTER TABLE `praktikum_group_assignment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`jadwal_praktikum_id`,`entity_type`,`entity_id`);

--
-- Indexes for table `praktikum_group_config`
--
ALTER TABLE `praktikum_group_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_jadwal_config` (`jadwal_praktikum_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_ruangan` (`kode_ruangan`),
  ADD KEY `idx_kode_ruangan` (`kode_ruangan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=727;

--
-- AUTO_INCREMENT for table `absen_asisten`
--
ALTER TABLE `absen_asisten`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `asisten_praktikum`
--
ALTER TABLE `asisten_praktikum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `jadwal_praktikum`
--
ALTER TABLE `jadwal_praktikum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `praktikum`
--
ALTER TABLE `praktikum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `praktikum_enroll`
--
ALTER TABLE `praktikum_enroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `praktikum_group_assignment`
--
ALTER TABLE `praktikum_group_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `praktikum_group_config`
--
ALTER TABLE `praktikum_group_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_jadwal` FOREIGN KEY (`jadwal_praktikum_id`) REFERENCES `jadwal_praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_absensi_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_absensi_praktikum` FOREIGN KEY (`praktikum_id`) REFERENCES `praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `asisten_praktikum`
--
ALTER TABLE `asisten_praktikum`
  ADD CONSTRAINT `fk_asprak_praktikum` FOREIGN KEY (`praktikum_id`) REFERENCES `jadwal_praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `fk_mahasiswa_praktikum` FOREIGN KEY (`praktikum_id`) REFERENCES `jadwal_praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `praktikum_enroll`
--
ALTER TABLE `praktikum_enroll`
  ADD CONSTRAINT `fk_enroll_jadwal` FOREIGN KEY (`jadwal_praktikum_id`) REFERENCES `jadwal_praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enroll_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
