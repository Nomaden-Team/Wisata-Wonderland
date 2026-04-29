-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 29, 2026 at 02:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wonderland`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Generate500Ulasan` ()   BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE random_wahana_id INT;
    DECLARE random_rating INT;
    DECLARE random_nama VARCHAR(100);
    DECLARE random_teks TEXT;
    DECLARE random_date DATETIME;

    -- Loop sampai 500 kali
    WHILE i <= 500 DO
        -- Pilih Wahana secara acak dari ID 1 sampai 6
        SET random_wahana_id = FLOOR(1 + (RAND() * 6));
        
        -- Pilih Rating acak (3 sampai 5 bintang supaya terlihat positif)
        SET random_rating = FLOOR(3 + (RAND() * 3));
        
        -- List Nama Pengunjung Acak
        SET random_nama = ELT(FLOOR(1 + (RAND() * 15)), 
            'Rizky Pratama', 'Siti Fatimah', 'Budi Santoso', 'Dewi Lestari', 
            'Andi Wijaya', 'Putri Utami', 'Fajar Ramadhan', 'Larasati', 
            'Eko Prasetyo', 'Anita Sari', 'Hendra Kusuma', 'Maya Indah',
            'Guntur Saputra', 'Nadia Putri', 'Yanto Subagyo');
            
        -- List Kalimat Ulasan Acak
        SET random_teks = ELT(FLOOR(1 + (RAND() * 10)), 
            'Wahana yang sangat seru dan memacu adrenalin!', 
            'Tempatnya bersih, petugasnya sangat membantu.', 
            'Anak-anak senang sekali diajak ke sini, bakal balik lagi.', 
            'Antriannya tertib dan fasilitas pendukungnya lengkap.', 
            'Pengalaman yang tak terlupakan di Wonderland Samarinda!', 
            'Harganya worth it banget sama keseruannya.', 
            'Sangat direkomendasikan untuk liburan keluarga.', 
            'View dari atas wahana ini keren banget pas sore hari.',
            'Aman untuk anak kecil, safety-nya bener-bener dijaga.',
            'Salah satu wahana terbaik yang pernah saya coba!');

        -- Buat tanggal acak dalam 2 bulan terakhir agar terlihat natural
        SET random_date = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY);

        -- Masukkan data ke tabel ulasan
        INSERT INTO `ulasan` (`wahana_id`, `nama_user`, `ulasan`, `rating`, `status`, `created_at`) 
        VALUES (random_wahana_id, random_nama, random_teks, random_rating, 'approved', random_date);
        
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(5, 'Cameng', 'cameng@wonderlandsamarinda.com', '12345\r\n', '2026-04-12 05:15:00'),
(6, 'Admin Utama', 'admin@wonderlandsamarinda.com', '$2y$10$R1Zp2/KhgRLeuVDhWm9sh.ScKY6sa.vIAyNuMz9Ak2rby1ExtUYCS', '2026-04-12 05:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('tersedia','tidak_tersedia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`, `ikon`, `deskripsi`, `status`, `created_at`) VALUES
(9, 'Toilet & Rest Area', 'fa-restroom', 'Fasilitas toilet dan area istirahat yang bersih dan nyaman', 'tersedia', '2026-04-28 16:28:12'),
(10, 'Mushola', 'fa-mosque', 'Tempat ibadah yang nyaman untuk pengunjung', 'tersedia', '2026-04-28 16:28:12'),
(12, 'Gazebo', 'fa-chair', 'Tempat duduk santai untuk bersantai bersama keluarga', 'tidak_tersedia', '2026-04-28 16:28:12'),
(13, 'Outdoor Area Foto', 'fa-camera', 'Spot foto outdoor menarik untuk pengunjung', 'tersedia', '2026-04-28 16:28:12'),
(14, 'Tenant', 'fa-store', 'Berbagai tenant yang menjual makanan, minuman, dan merchandise', 'tidak_tersedia', '2026-04-28 16:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `foto_pengunjung`
--

CREATE TABLE `foto_pengunjung` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `caption` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `foto_pengunjung`
--

INSERT INTO `foto_pengunjung` (`id`, `user_id`, `nama_file`, `caption`, `status`, `created_at`) VALUES
(1, 1, 'foto_1_1775962452.jpg', NULL, 'approved', '2026-04-12 02:54:12'),
(6, 4, 'foto_4_1777458904.jpeg', '', 'approved', '2026-04-29 10:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int NOT NULL,
  `foto` varchar(255) NOT NULL,
  `ukuran` enum('normal','besar','tinggi') DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `foto`, `ukuran`) VALUES
(1, 'wonderland1.jpg', 'besar'),
(2, 'wonderland2.jpg', 'besar'),
(3, 'wonderland3.jpg', 'besar'),
(4, 'wonderland4.jpg', 'besar'),
(5, 'wondeland5.jpg', 'tinggi'),
(6, 'wonderland6.jpg', 'besar');

-- --------------------------------------------------------

--
-- Table structure for table `kontak_pesan`
--

CREATE TABLE `kontak_pesan` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricelist`
--

CREATE TABLE `pricelist` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `benefit` text,
  `kategori` varchar(50) NOT NULL DEFAULT 'Tiket Masuk',
  `harga_normal` int NOT NULL DEFAULT '0',
  `harga_promo` int NOT NULL DEFAULT '0',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pricelist`
--

INSERT INTO `pricelist` (`id`, `nama`, `deskripsi`, `benefit`, `kategori`, `harga_normal`, `harga_promo`, `status`, `created_at`) VALUES
(3, 'Tiket Reguler Weekday', 'Tiket reguler untuk kunjungan hari Senin sampai Jumat.', 'Kolam Renang\r\nDinosaurus Forest\r\nGhost Forest\r\nKampung Sakura\r\nKampung Jepang\r\nKampung Salju\r\nLorong Pelangi\r\nKastil Wonderland\r\nAustralian Photobooth\r\nPatung Presiden RI\r\nMiniatur Prambanan\r\nMiniatur Ka’bah\r\nMiniatur Gereja\r\nGerbang Naga\r\nGazebo\r\nToilet\r\nMushola\r\nKantin', 'Tiket Masuk', 25000, 0, 'nonaktif', '2026-04-29 02:34:34'),
(4, 'Tiket Reguler Weekend', 'Tiket reguler untuk kunjungan Sabtu, Minggu, dan hari libur.', 'Kolam Renang\r\nDinosaurus Forest\r\nGhost Forest\r\nKampung Sakura\r\nKampung Jepang\r\nKampung Salju\r\nLorong Pelangi\r\nKastil Wonderland\r\nAustralian Photobooth\r\nPatung Presiden RI\r\nMiniatur Prambanan\r\nMiniatur Ka’bah\r\nMiniatur Gereja\r\nGerbang Naga\r\nGazebo\r\nToilet\r\nMushola\r\nKantin', 'Tiket Masuk', 30000, 0, 'aktif', '2026-04-29 02:34:34'),
(5, 'Tiket Terusan Everyday', 'Tiket terusan untuk akses wahana utama dan area wisata. Berlaku setiap hari.', 'Monorail\r\nBombom Car\r\nRainbowslide\r\nKolam Renang\r\nDinosaurus Forest\r\nGhost Forest\r\nKampung Sakura\r\nKampung Jepang\r\nKampung Salju\r\nLorong Pelangi\r\nKastil Wonderland\r\nAustralian Photobooth\r\nPatung Presiden RI\r\nMiniatur Prambanan\r\nMiniatur Ka’bah\r\nMiniatur Gereja\r\nGerbang Naga\r\nGazebo\r\nToilet\r\nMushola\r\nKantin', 'Tiket Terusan', 75000, 0, 'aktif', '2026-04-29 02:34:34'),
(6, 'Tiket Terusan+ Sepuasnya', 'Tiket terusan paling lengkap untuk akses wahana utama, wahana anak, dan area wisata.', 'Monorail\r\nBombom Car\r\nRainbowslide\r\nTamiya Gravity\r\nPlayground Anak\r\nTrampolin Jumbo\r\nKereta Mini\r\nKolam Renang\r\nDinosaurus Forest\r\nGhost Forest\r\nKampung Sakura\r\nKampung Jepang\r\nKampung Salju\r\nLorong Pelangi\r\nKastil Wonderland\r\nAustralian Photobooth\r\nPatung Presiden RI\r\nMiniatur Prambanan\r\nMiniatur Ka’bah\r\nMiniatur Gereja\r\nGerbang Naga\r\nGazebo\r\nToilet\r\nMushola\r\nKantin', 'Tiket Terusan', 125000, 0, 'aktif', '2026-04-29 02:34:34');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` int NOT NULL,
  `nama_promo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `diskon` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `syarat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_berakhir` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nama_kegiatan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kegiatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `jumlah_peserta` int DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','terjadwal','selesai','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `kode_booking` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `user_id`, `nama_kegiatan`, `jenis_kegiatan`, `tanggal`, `jam_mulai`, `jam_selesai`, `jumlah_peserta`, `keterangan`, `status`, `created_at`, `kode_booking`) VALUES
(1, NULL, 'bbbbbb', 'jalan jalan', '2026-04-16', '11:11:00', '11:11:00', 200, 'tidak ada', 'dibatalkan', '2026-04-14 01:17:55', NULL),
(3, NULL, 'aaaa', 'aaaaa', '2026-04-23', '11:11:00', '14:22:00', 11111, 'aaaaa', 'dibatalkan', '2026-04-14 01:47:16', NULL),
(4, NULL, 'bbbb', 'bbbbb', '2026-04-25', '00:12:00', '00:12:00', 999, 'aaaa', 'selesai', '2026-04-14 05:37:14', NULL),
(5, NULL, 'INFORSA MEET', 'Evaluasi Galvin Lood', '2026-04-29', '00:00:13', '00:00:18', 22, 'Gak ada', 'terjadwal', '2026-04-17 05:31:52', NULL),
(6, 2, 'study tour', NULL, '2026-04-30', NULL, NULL, 200, '', 'terjadwal', '2026-04-27 11:52:21', '48BF61F72F'),
(7, 2, 'Kunjungan SMK Cipta Wiyata Jayakarta', 'Study Tour', '2026-04-29', '21:00:00', '22:00:00', 5, '', 'terjadwal', '2026-04-27 12:42:44', '4D21287D5B'),
(8, 2, 'Konser Beyond Dream', '', '2026-05-08', '00:00:00', '00:00:00', 4, '', 'selesai', '2026-04-28 02:01:32', 'E11D80D2EC'),
(9, 2, 'Kunjungan SMK Cipya Wiyata Jayakarta Lagi', NULL, '2026-06-13', NULL, NULL, 500, '', 'terjadwal', '2026-04-28 02:29:46', '9002B71F0F'),
(10, 4, 'FAMILY GATHERING', NULL, '2026-05-01', NULL, NULL, 2, '', 'terjadwal', '2026-04-28 14:33:09', '0E2B45DA73'),
(12, 4, 'FAMILY GATHERING', NULL, '2026-05-08', NULL, NULL, 2, '', 'terjadwal', '2026-04-28 15:10:39', 'WDR-20260428-29D4'),
(13, 4, 'outbound inforsa', NULL, '2026-05-08', NULL, NULL, 1, '', 'terjadwal', '2026-04-28 15:31:32', 'WDR-20260428-5DB2'),
(14, 5, 'family gathering sobi', NULL, '2026-05-09', NULL, NULL, 32, '', 'terjadwal', '2026-04-29 11:25:49', 'WDR-20260429-4489');

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `id` int NOT NULL,
  `nama_tiket` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('weekday','weekend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `segmen` enum('dewasa','anak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` int NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`id`, `nama_tiket`, `kategori`, `segmen`, `harga`, `keterangan`, `status`, `created_at`) VALUES
(1, 'Tiket Reguler Dewasa', 'weekday', 'dewasa', 75000, 'Berlaku Senin - Jumat', 'aktif', '2026-04-07 08:45:18'),
(2, 'Tiket Reguler Anak', 'weekday', 'anak', 50000, 'Tinggi badan di bawah 120cm', 'aktif', '2026-04-07 08:45:18'),
(3, 'Tiket Weekend Dewasa', 'weekend', 'dewasa', 100000, 'Berlaku Sabtu, Minggu & Libur Nasional', 'aktif', '2026-04-07 08:45:18'),
(4, 'Tiket Weekend Anak', 'weekend', 'anak', 75000, 'Berlaku Sabtu, Minggu & Libur Nasional', 'aktif', '2026-04-07 08:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int NOT NULL,
  `wahana_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `nama_user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ulasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint NOT NULL,
  `status` enum('pending','approved') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `wahana_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `wahana_id`, `user_id`, `nama_user`, `ulasan`, `rating`, `status`, `created_at`, `wahana_name`) VALUES
(1, 1, NULL, 'Andi Saputra', 'Kolam renangnya luas dan cocok untuk keluarga. Anak-anak senang bermain di sini.', 5, 'approved', '2026-04-29 11:22:53', 'Kolam Renang'),
(2, 1, NULL, 'Rina Amelia', 'Area kolam cukup nyaman dan suasananya ramai tapi tetap menyenangkan.', 4, 'approved', '2026-04-29 11:22:53', 'Kolam Renang'),
(3, 2, NULL, 'Dewi Lestari', 'Rainbowslide seru sekali, cocok untuk yang suka wahana air.', 5, 'approved', '2026-04-29 11:22:53', 'Rainbowslide'),
(4, 2, NULL, 'Bagus Pratama', 'Wahananya menarik dan jadi salah satu spot favorit anak-anak.', 4, 'approved', '2026-04-29 11:22:53', 'Rainbowslide'),
(5, 3, NULL, 'Fajar Nugroho', 'Monorailnya santai, bisa melihat area Wonderland dari sudut yang berbeda.', 5, 'approved', '2026-04-29 11:22:53', 'Monorail'),
(6, 3, NULL, 'Nadia Putri', 'Cocok untuk keluarga yang ingin keliling tanpa terlalu capek berjalan.', 4, 'approved', '2026-04-29 11:22:53', 'Monorail'),
(7, 4, NULL, 'Siti Rahma', 'Kereta mini sangat cocok untuk anak-anak. Jalurnya menyenangkan.', 5, 'approved', '2026-04-29 11:22:53', 'Kereta Mini'),
(8, 4, NULL, 'Hendra Wijaya', 'Anak saya suka sekali naik kereta mini, pelayanannya juga ramah.', 5, 'approved', '2026-04-29 11:22:53', 'Kereta Mini'),
(9, 5, NULL, 'Yusuf Maulana', 'Bombomcar seru untuk dimainkan bersama teman dan keluarga.', 5, 'approved', '2026-04-29 11:22:53', 'Bombomcar'),
(10, 5, NULL, 'Maya Anggraini', 'Wahana klasik yang tetap menyenangkan. Cocok untuk semua umur.', 4, 'approved', '2026-04-29 11:22:53', 'Bombomcar'),
(11, 7, NULL, 'Dian Puspita', 'Playground anaknya nyaman dan cocok untuk tempat bermain keluarga.', 5, 'approved', '2026-04-29 11:22:53', 'Playground Anak'),
(12, 7, NULL, 'Rizky Ananda', 'Anak-anak betah bermain di playground. Tempatnya cukup menyenangkan.', 4, 'pending', '2026-04-29 11:22:53', 'Playground Anak'),
(13, 8, NULL, 'Ayu Permata', 'Trampolin jumbo seru banget, anak-anak bisa bermain aktif di sini.', 5, 'approved', '2026-04-29 11:22:53', 'Trampolin Jumbo'),
(14, 8, NULL, 'Gilang Ramadhan', 'Wahana yang menyenangkan untuk anak-anak. Semoga perawatannya tetap dijaga.', 4, 'approved', '2026-04-29 11:22:53', 'Trampolin Jumbo'),
(15, 11, NULL, 'Citra Dewi', 'Kampung Sakura sangat bagus untuk foto. Nuansanya cantik.', 5, 'approved', '2026-04-29 11:22:53', 'Kampung Sakura'),
(16, 11, NULL, 'Melati Putri', 'Spot foto yang paling saya suka. Warnanya indah dan rapi.', 5, 'approved', '2026-04-29 11:22:53', 'Kampung Sakura'),
(17, 19, NULL, 'Hasan Basri', 'Miniatur Ka\'bah menarik dan cocok sebagai spot edukasi.', 5, 'approved', '2026-04-29 11:22:53', 'Miniatur Ka\'bah'),
(18, 19, NULL, 'Nurul Azizah', 'Tempatnya bagus dan memberi pengalaman berbeda untuk pengunjung.', 5, 'approved', '2026-04-29 11:22:53', 'Miniatur Ka\'bah');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_telp`, `password`, `created_at`) VALUES
(1, 'Nabilla Imtiyaz Agustin', 'lala@gmail.com', '0808080808', '123456', '2026-04-12 02:32:39'),
(2, 'aku', 'aku@gmail.com', '08123456789', '$2y$10$RARjBQnsV1Q9glV/m2aP6e3hLx.kxfE9jppylXPK/vm3Llk0.6.KK', '2026-04-13 08:27:39'),
(3, 'LALA', 'nabilaimtiyaz25@gmail.com', '0858 4594 1523', '$2y$10$qgMx5nZuZvnRrEi0n.70ceWMhvtfiTcaxW5rl7HKNtAbvTZ6DDpAG', '2026-04-28 14:30:45'),
(4, 'unmul_Nayla Camelia Indraswari', 'naylacame@gmail.com', '0858 4594 1523', '$2y$10$McWLO9G9a7jqlOCBLLNt4.NLZ0wO.I47m0FehEAR1SiuWrp1jXoEu', '2026-04-28 14:32:37'),
(5, 'jen', 'jenagres06@gmail.com', '0858 4594 1523', '$2y$10$pnE6cx/3q8bodoleJ6MC3un1L.WPyg3V.o.qPPS33d6n9NnFOOw5u', '2026-04-29 11:01:28');

-- --------------------------------------------------------

--
-- Table structure for table `wahana`
--

CREATE TABLE `wahana` (
  `id` int NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `harga` int NOT NULL,
  `kapasitas` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_operasional` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wahana`
--

INSERT INTO `wahana` (`id`, `nama`, `kategori`, `deskripsi`, `harga`, `kapasitas`, `foto`, `jam_operasional`, `status`, `created_at`) VALUES
(1, 'Kolam Renang', 'Wahana Air', 'Area kolam renang untuk pengunjung Wonderland Samarinda.', 0, NULL, 'wahana_1777455924_ef59985b.webp', '09:00 - 18:00', 'aktif', '2026-04-29 09:42:44'),
(2, 'Rainbowslide', 'Wahana Air', 'Wahana seluncuran warna-warni yang menjadi salah satu daya tarik pengunjung.', 0, NULL, 'wahana_1777456079_dfe5bbfb.jpg', '09:00 - 18:00', 'aktif', '2026-04-29 09:42:44'),
(3, 'Monorail', 'Transportasi Wahana', 'Wahana transportasi santai untuk menikmati area Wonderland dari jalur monorail.', 0, NULL, 'wahana_1777456061_8a28f020.webp', '09:00 - 18:00', 'aktif', '2026-04-29 09:42:44'),
(4, 'Kereta Mini', 'Transportasi Wahana', 'Wahana kereta mini untuk anak dan keluarga berkeliling area wisata.', 0, NULL, 'wahana_1777455911_d4872be7.webp', '09:00 - 18:00', 'nonaktif', '2026-04-29 09:42:44'),
(5, 'Bombomcar', 'Wahana Keluarga', 'Wahana mobil tabrak yang cocok dimainkan bersama keluarga.', 0, NULL, 'wahana_1777456343_aca1a4ba.webp', '09:00 - 18:00', 'aktif', '2026-04-29 09:42:44'),
(7, 'Playground Anak', 'Wahana Anak', 'Area bermain anak yang aman dan menyenangkan.', 0, NULL, 'wahana_1777456071_585fa35a.webp', '09:00 - 18:00', 'nonaktif', '2026-04-29 09:42:44'),
(8, 'Trampolin Jumbo', 'Wahana Anak', 'Wahana trampolin jumbo untuk aktivitas bermain anak.', 0, NULL, 'wahana_1777455879_7d43188e.jpg', '09:00 - 18:00', 'nonaktif', '2026-04-29 09:42:44'),
(11, 'Kampung Sakura', 'Area Wisata', 'Area spot foto bertema bunga sakura.', 0, NULL, 'wahana_1777456105_5c6a9888.jpg', '09:00 - 18:00', 'nonaktif', '2026-04-29 09:42:44'),
(19, 'Miniatur Ka\'bah', 'Area Wisata', 'Area miniatur Ka\'bah sebagai spot edukasi dan foto.', 0, NULL, 'wahana_1777456316_2e386a97.png', '09:00 - 18:00', 'nonaktif', '2026-04-29 09:42:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foto_pengunjung`
--
ALTER TABLE `foto_pengunjung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kontak_pesan`
--
ALTER TABLE `kontak_pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricelist`
--
ALTER TABLE `pricelist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservasi_user` (`user_id`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ulasan_wahana` (`wahana_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wahana`
--
ALTER TABLE `wahana`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `foto_pengunjung`
--
ALTER TABLE `foto_pengunjung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kontak_pesan`
--
ALTER TABLE `kontak_pesan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricelist`
--
ALTER TABLE `pricelist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wahana`
--
ALTER TABLE `wahana`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `foto_pengunjung`
--
ALTER TABLE `foto_pengunjung`
  ADD CONSTRAINT `foto_pengunjung_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `fk_reservasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `fk_ulasan_wahana` FOREIGN KEY (`wahana_id`) REFERENCES `wahana` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
