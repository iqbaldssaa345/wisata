-- ==============================================================================
-- DATABASE: PESONA WISATA PULAU JAWA (`wisata_jawa`)
-- Sistem Informasi Pariwisata, Reservasi E-Tiket & Manajemen Multi-Portal
-- Engine  : InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Kompatibel: MySQL 5.7+, MySQL 8.0+, MariaDB 10+, phpMyAdmin, AppServ
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ------------------------------------------------------------------------------
-- 1. PEMBUATAN DATABASE
-- ------------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `wisata_jawa` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `wisata_jawa`;

-- ==============================================================================
-- 2. TABEL: `provinsi` (Wilayah Administrasi Se-Pulau Jawa)
-- ==============================================================================
DROP TABLE IF EXISTS `provinsi`;
CREATE TABLE `provinsi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_provinsi` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `ibu_kota` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `foto_banner` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_provinsi_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `provinsi` (`id`, `nama_provinsi`, `slug`, `ibu_kota`, `deskripsi`, `foto_banner`) VALUES
(1, 'DKI Jakarta', 'dki-jakarta', 'Jakarta', 'Pusat metropolitan bersejarah dengan pesona Kota Tua, Monas, kepulauan seribu, dan seni budaya Betawi yang kaya.', 'https://images.unsplash.com/photo-1555899434-94d1338eb352?w=800'),
(2, 'Jawa Barat', 'jawa-barat', 'Bandung', 'Tanah Pasundan berhawa sejuk dengan pegunungan nan asri, perkebunan teh, air terjun memukau, dan warisan budaya Sunda.', 'https://images.unsplash.com/photo-1601621915196-2621bfb0cd6e?w=800'),
(3, 'Banten', 'banten', 'Serang', 'Pesisir barat pulau Jawa dengan keindahan pantai Tanjung Lesung, Sawarna, pulau Ujung Kulon, dan kearifan suku Baduy.', 'https://images.unsplash.com/photo-1596401057633-54a8fe8ef647?w=800'),
(4, 'Jawa Tengah', 'jawa-tengah', 'Semarang', 'Pusat peradaban megah nusantara, rumah dari Mahakarya Dunia Candi Borobudur, dataran tinggi Dieng, dan Karimunjawa.', 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800'),
(5, 'DI Yogyakarta', 'di-yogyakarta', 'Yogyakarta', 'Kota Istimewa nan romantis, pusat seni tradisi keraton Jawa, Candi Prambanan megah, dan kehangatan Malioboro.', 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=800'),
(6, 'Jawa Timur', 'jawa-timur', 'Surabaya', 'Pusat petualangan spektakuler dengan kaldera magis Gunung Bromo, api biru Kawah Ijen, dan air terjun Tumpak Sewu.', 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800');

-- ==============================================================================
-- 3. TABEL: `kategori_wisata` (Klasifikasi Jenis Pariwisata)
-- ==============================================================================
DROP TABLE IF EXISTS `kategori_wisata`;
CREATE TABLE `kategori_wisata` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-map-pin',
  `deskripsi` TEXT,
  `badge_color` VARCHAR(30) DEFAULT '#0d9488',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_kategori_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategori_wisata` (`id`, `nama_kategori`, `slug`, `icon`, `deskripsi`, `badge_color`) VALUES
(1, 'Candi & Warisan Budaya', 'candi-warisan-budaya', 'fa-landmark', 'Monumen bersejarah megah dan warisan kebudayaan leluhur adiluhung', '#d97706'),
(2, 'Gunung & Dataran Tinggi', 'gunung-dataran-tinggi', 'fa-mountain', 'Pemandangan alam pegunungan sejuk, kawah vulkanik, dan lanskap spektakuler', '#059669'),
(3, 'Pantai & Bahari', 'pantai-bahari', 'fa-umbrella-beach', 'Garis pantai eksotis pulau Jawa, pasir putih, ombak selancar, dan terumbu karang', '#0284c7'),
(4, 'Taman Hiburan & Rekreasi', 'taman-hiburan-rekreasi', 'fa-ticket-alt', 'Destinasi edukasi modern, taman safari satwa, dan hiburan keluarga', '#7c3aed'),
(5, 'Air Terjun & Alam Tersembunyi', 'air-terjun-alam', 'fa-water', 'Curug memesona, mata air alami, dan hutan pinus tropis yang menyejukkan jiwa', '#10b981');

-- ==============================================================================
-- 4. TABEL: `users` (Pengguna: Admin, Petugas Loket, & Pengunjung)
-- ==============================================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `no_telp` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT,
  `role` ENUM('admin', 'petugas', 'pengunjung') NOT NULL DEFAULT 'pengunjung',
  `foto` VARCHAR(255) DEFAULT 'assets/images/avatar-default.png',
  `status_aktif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_email` (`email`),
  UNIQUE KEY `idx_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kredensial Demo:
-- admin      : admin123
-- petugas    : petugas123
-- iqbal      : user123
-- siti       : user123
-- pengunjung : user123
INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `username`, `password`, `no_telp`, `alamat`, `role`, `foto`, `status_aktif`, `created_at`) VALUES
(1, 'Administrator Eksekutif', 'admin@pesonajawa.id', 'admin', '$2y$10$ET1Mqw8q2KkL7OHFfrlEjO9gHS.ol96eL6R49tzw7ldvqLG2dopUK', '081234567890', 'Gedung Sapta Pesona Jawa, Lt. 8, Jakarta', 'admin', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150', 1, '2026-10-02 07:00:00'),
(2, 'Raden Surya (Petugas Loket)', 'petugas@pesonajawa.id', 'petugas', '$2y$10$AMJy.pJk36GnRKOhAH4scuG18S/yvT3iiTLRLQGZb0TWo0q7fqiNe', '082345678901', 'Kantor Pengelola Wisata Magelang, Jawa Tengah', 'petugas', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150', 1, '2026-10-02 07:00:00'),
(3, 'Muhammad Iqbal', 'iqbal@gmail.com', 'iqbal', '$2y$10$FcaBm0r.Ion2FVyXhOBsfe3kGox/j40BwlHzFPrudynQAKhnAKd7O', '083456789012', 'Jl. Merdeka No. 45, Bandung', 'pengunjung', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150', 1, '2026-10-02 07:00:00'),
(4, 'Siti Nurhaliza', 'siti@gmail.com', 'siti', '$2y$10$FcaBm0r.Ion2FVyXhOBsfe3kGox/j40BwlHzFPrudynQAKhnAKd7O', '084567890123', 'Yogyakarta', 'pengunjung', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150', 1, '2026-10-02 07:00:00'),
(5, 'Wisatawan Pengunjung', 'pengunjung@wisatajawa.id', 'pengunjung', '$2y$10$FcaBm0r.Ion2FVyXhOBsfe3kGox/j40BwlHzFPrudynQAKhnAKd7O', '08123456789', 'Yogyakarta', 'pengunjung', 'assets/images/avatar-default.png', 1, '2026-10-02 07:00:00');

-- ==============================================================================
-- 5. TABEL: `destinasi` (10 Destinasi Ikonik Pulau Jawa)
-- ==============================================================================
DROP TABLE IF EXISTS `destinasi`;
CREATE TABLE `destinasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `provinsi_id` INT(11) NOT NULL,
  `kategori_id` INT(11) NOT NULL,
  `nama_destinasi` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `ringkasan` VARCHAR(255) NOT NULL,
  `deskripsi_lengkap` LONGTEXT NOT NULL,
  `lokasi_spesifik` VARCHAR(255) NOT NULL,
  `harga_tiket` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `jam_buka` VARCHAR(20) DEFAULT '08:00',
  `jam_tutup` VARCHAR(20) DEFAULT '17:00',
  `rating` DECIMAL(3,2) DEFAULT 4.80,
  `jumlah_ulasan` INT(11) DEFAULT 0,
  `kuota_harian` INT(11) DEFAULT 500,
  `gambar_utama` VARCHAR(255) NOT NULL,
  `status` ENUM('aktif', 'pemeliharaan', 'tutup') DEFAULT 'aktif',
  `is_populer` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_destinasi_slug` (`slug`),
  KEY `fk_destinasi_provinsi` (`provinsi_id`),
  KEY `fk_destinasi_kategori` (`kategori_id`),
  CONSTRAINT `fk_destinasi_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_wisata` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_destinasi_provinsi` FOREIGN KEY (`provinsi_id`) REFERENCES `provinsi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `destinasi` (`id`, `provinsi_id`, `kategori_id`, `nama_destinasi`, `slug`, `ringkasan`, `deskripsi_lengkap`, `lokasi_spesifik`, `harga_tiket`, `jam_buka`, `jam_tutup`, `rating`, `jumlah_ulasan`, `kuota_harian`, `gambar_utama`, `status`, `is_populer`, `created_at`) VALUES
(1, 4, 1, 'Candi Borobudur', 'candi-borobudur', 'Mahakarya candi Buddha terbesar di dunia peninggalan Dinasti Syailendra', 'Candi Borobudur adalah monumen candi Buddha terbesar di dunia yang dibangun pada abad ke-8. Ditetapkan sebagai Situs Warisan Dunia UNESCO, candi ini memiliki 72 stupa terawang dan relief pahatan kisah hidup sang Buddha yang tak ternilai harganya. Menikmati mentari pagi yang terbit (sunrise) di antara siluet stupa Borobudur merupakan pengalaman spiritual dan estetika kelas dunia.', 'Borobudur, Magelang, Jawa Tengah', 50000.00, '06:30', '17:00', 4.95, 342, 1200, 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(2, 6, 2, 'Gunung Bromo & Lautan Pasir', 'gunung-bromo', 'Kaldera vulkanik legendaris dengan panorama matahari terbit paling spektakuler', 'Taman Nasional Bromo Tengger Semeru menyuguhkan pemandangan bentang alam kaldera purba, lautan pasir berbisik, serta Kawah Bromo yang terus mengepulkan asap putih magis. Wisatawan dapat menunggang kuda menyusuri lautan pasir, menaiki mobil Jeep 4x4 menuju puncak Penanjakan untuk menyaksikan golden sunrise legendaris.', 'Probolinggo & Pasuruan, Jawa Timur', 35000.00, '03:00', '18:00', 4.92, 410, 800, 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(3, 5, 1, 'Candi Prambanan', 'candi-prambanan', 'Candi Hindu terindah dan termegah di Asia Tenggara dengan legenda Roro Jonggrang', 'Candi Prambanan merupakan kompleks candi Hindu terbesar di Indonesia yang dipersembahkan untuk Trimurti: Brahma, Wisnu, dan Siwa. Menjulang setinggi 47 meter dengan arsitektur ramping dan menjulang tinggi, candi ini juga menyelenggarakan Sendratari Ramayana megah dengan latar candi yang bermandikan cahaya malam hari.', 'Bokoharjo, Sleman, DI Yogyakarta', 50000.00, '07:00', '17:30', 4.88, 280, 1000, 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(4, 2, 2, 'Kawah Putih Ciwidey', 'kawah-putih-ciwidey', 'Danau kawah vulkanik berwarna putih toska kehijauan di dataran tinggi Bandung Selatan', 'Kawah Putih adalah sebuah danau kawah vulkanik yang terbentuk dari letusan Gunung Patuha. Tanah yang bercampur belerang berwarna putih bersih dengan air danau yang bisa berubah warna dari kehijauan hingga putih kebiruan sesuai kadar belerang dan cuaca. Udara dingin pegunungan dan kabut misterius menciptakan nuansa dongeng yang sangat memikat.', 'Ciwidey, Kabupaten Bandung, Jawa Barat', 30000.00, '07:30', '17:00', 4.78, 195, 600, 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(5, 6, 2, 'Kawah Ijen & Blue Fire', 'kawah-ijen', 'Fenomena alam api biru langka dan danau kawah asam tercantik di dunia', 'Gunung Ijen menyajikan fenomena Blue Fire (api biru) yang hanya ada 2 di dunia (satunya berada di Islandia). Wisatawan mendaki pada dini hari untuk menyaksikan nyala api biru belerang yang memukau di dasar kawah, diikuti pemandangan danau kawah berwarna hijau toska yang menakjubkan saat fajar menyingsing.', 'Banyuwangi - Bondowoso, Jawa Timur', 40000.00, '01:00', '12:00', 4.90, 160, 500, 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(6, 4, 2, 'Dataran Tinggi Dieng', 'dataran-tinggi-dieng', 'Negeri di atas awan dengan telaga warna warni, candi kuno, dan kawah sikidang', 'Dieng Plateau berada di ketinggian lebih dari 2.000 mdpl. Kawasan ini terkenal dengan panorama Golden Sunrise di Bukit Sikunir, Telaga Warna yang memancarkan berbagai gradasi air, kawah Sikidang yang aktif bergolak, dan peninggalan Candi Arjuna tertua di Jawa. Pada musim kemarau, sering muncul embun es (bun upas).', 'Banjarnegara & Wonosobo, Jawa Tengah', 25000.00, '06:00', '17:00', 4.82, 175, 750, 'https://images.unsplash.com/photo-1596401057633-54a8fe8ef647?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(7, 3, 3, 'Pantai Sawarna & Tanjung Layar', 'pantai-sawarna', 'Gugusan karang megah, pasir putih berkilau, dan ombak samudra Banten', 'Pantai Sawarna menyuguhkan keindahan alam pantai berpasir putih bersih dengan lanskap batu karang menjulang kembar bernama Tanjung Layar. Tempat ini sangat populer bagi pecinta fotografi lanskap, penikmat senja di Samudra Hindia, serta para peselancar dunia karena ombaknya yang berkelas internasional.', 'Sawarna, Bayah, Lebak, Banten', 20000.00, '06:00', '18:00', 4.75, 120, 500, 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(8, 1, 1, 'Kota Tua & Museum Fatahillah', 'kota-tua-jakarta', 'Jantung sejarah Batavia dengan gedung kolonial klasik dan sepeda onthel warna-warni', 'Kawasan Kota Tua Jakarta adalah cagar budaya peninggalan masa kolonial VOC dengan arsitektur bergaya neo-klasik Eropa abad ke-17. Menikmati sore hari di pelataran Museum Sejarah Jakarta (Fatahillah), menyewa sepeda onthel klasik lengkap dengan topi londo, dan menyeruput kopi hangat di Cafe Batavia adalah daya tarik utama.', 'Tamansari, Jakarta Barat, DKI Jakarta', 10000.00, '08:00', '20:00', 4.70, 310, 2000, 'https://images.unsplash.com/photo-1555899434-94d1338eb352?w=1000', 'aktif', 0, '2026-10-02 07:00:00'),
(9, 6, 5, 'Air Terjun Tumpak Sewu', 'tumpak-sewu', 'Tirai air terjun raksasa menakjubkan yang dijuluki Niagara-nya Indonesia', 'Air Terjun Tumpak Sewu atau Coban Sewu adalah air terjun berketinggian sekitar 120 meter dengan formasi melingkar menyerupai tirai air raksasa di dinding tebing curam. Dikelilingi hutan tropis lebat dengan latar belakang megah Gunung Semeru yang menjulang, menjadikannya salah satu panorama alam terindah di Asia Tenggara.', 'Sidomulyo, Pronojiwo, Lumajang, Jawa Timur', 25000.00, '07:00', '16:00', 4.93, 210, 400, 'https://images.unsplash.com/photo-1546708973-b339540b5162?w=1000', 'aktif', 1, '2026-10-02 07:00:00'),
(10, 4, 3, 'Kepulauan Karimunjawa', 'karimunjawa', 'Surga bahari tropis dengan pasir putih halus, terumbu karang hidup, dan hiu jinak', 'Kepulauan Karimunjawa merupakan taman nasional bahari di Laut Jawa yang terdiri dari 27 pulau tropis nan menawan. Di sini wisatawan dapat snorkeling dan diving bersama ikan nemo di terumbu karang warna-warni, berenang bersama hiu sirip hitam di penangkaran, serta menikmati panorama matahari terbenam magis di Bukit Love.', 'Jepara, Jawa Tengah', 45000.00, '07:00', '18:00', 4.91, 145, 300, 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1000', 'aktif', 1, '2026-10-02 07:00:00');

-- ==============================================================================
-- 6. TABEL: `destinasi_fasilitas` (Fasilitas Pendukung Objek Wisata)
-- ==============================================================================
DROP TABLE IF EXISTS `destinasi_fasilitas`;
CREATE TABLE `destinasi_fasilitas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `destinasi_id` INT(11) NOT NULL,
  `nama_fasilitas` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-check-circle',
  PRIMARY KEY (`id`),
  KEY `fk_fasilitas_destinasi` (`destinasi_id`),
  CONSTRAINT `fk_fasilitas_destinasi` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `destinasi_fasilitas` (`id`, `destinasi_id`, `nama_fasilitas`, `icon`) VALUES
(1, 1, 'Pusat Informasi & Museum', 'fa-info-circle'),
(2, 1, 'Area Parkir VIP & Bus', 'fa-parking'),
(3, 1, 'Mushola & Toilet Bersih', 'fa-mosque'),
(4, 1, 'Pemandu Wisata Resmi Berlisensi', 'fa-user-tie'),
(5, 1, 'Penyewaan Payung & Kereta Golf', 'fa-shuttle-van'),
(6, 2, 'Sewa Jeep 4x4 Wisata', 'fa-car-side'),
(7, 2, 'Tunggang Kuda Adat Tengger', 'fa-horse'),
(8, 2, 'Warung Kuliner Hangat & Kopi', 'fa-mug-hot'),
(9, 3, 'Panggung Terbuka Sendratari', 'fa-theater-masks'),
(10, 4, 'Jembatan Dermaga Apung Kayu', 'fa-bridge'),
(11, 5, 'Penyewaan Masker Gas Belerang & Troli', 'fa-mask');

-- ==============================================================================
-- 7. TABEL: `destinasi_galeri` (Foto Dokumentasi & Pemandangan)
-- ==============================================================================
DROP TABLE IF EXISTS `destinasi_galeri`;
CREATE TABLE `destinasi_galeri` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `destinasi_id` INT(11) NOT NULL,
  `judul_foto` VARCHAR(150) DEFAULT NULL,
  `url_gambar` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_galeri_destinasi` (`destinasi_id`),
  CONSTRAINT `fk_galeri_destinasi` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `destinasi_galeri` (`id`, `destinasi_id`, `judul_foto`, `url_gambar`, `caption`) VALUES
(1, 1, 'Borobudur Sunrise', 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800', 'Pemandangan stupa Borobudur diselimuti kabut pagi hari'),
(2, 1, 'Relief Candi', 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?w=800', 'Pahatan relief adiluhung pada dinding batu andesit'),
(3, 2, 'Lautan Pasir Bromo', 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800', 'Kendaraan Jeep 4x4 melintasi hamparan pasir berbisik'),
(4, 2, 'Kawah Aktif Bromo', 'https://images.unsplash.com/photo-1578469550956-0e16b69c6a3d?w=800', 'Asap belerang mengepul dari kaldera aktif'),
(5, 3, 'Kemegahan Siwa', 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?w=800', 'Candi induk Siwa yang menjulang setinggi 47 meter'),
(6, 4, 'Air Kawah Putih', 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=800', 'Air danau kawah toska berpadu pohon cantigi kering'),
(7, 5, 'Pesona Kawah Ijen', 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=800', 'Danau kawah terasam di dunia dengan warna toska');

-- ==============================================================================
-- 8. TABEL: `pemesanan_tiket` (Transaksi Reservasi Tiket Masuk)
-- ==============================================================================
DROP TABLE IF EXISTS `pemesanan_tiket`;
CREATE TABLE `pemesanan_tiket` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_booking` VARCHAR(30) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `destinasi_id` INT(11) NOT NULL,
  `tanggal_kunjungan` DATE NOT NULL,
  `jumlah_tiket` INT(11) NOT NULL DEFAULT 1,
  `harga_satuan` DECIMAL(12,2) NOT NULL,
  `total_harga` DECIMAL(12,2) NOT NULL,
  `status_pemesanan` ENUM('menunggu_pembayaran', 'lunas', 'selesai', 'dibatalkan') DEFAULT 'menunggu_pembayaran',
  `status_checkin` ENUM('belum', 'sudah') DEFAULT 'belum',
  `waktu_checkin` DATETIME DEFAULT NULL,
  `catatan` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_booking_code` (`kode_booking`),
  KEY `fk_pesanan_user` (`user_id`),
  KEY `fk_pesanan_destinasi` (`destinasi_id`),
  CONSTRAINT `fk_pesanan_destinasi` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pemesanan_tiket` (`id`, `kode_booking`, `user_id`, `destinasi_id`, `tanggal_kunjungan`, `jumlah_tiket`, `harga_satuan`, `total_harga`, `status_pemesanan`, `status_checkin`, `waktu_checkin`, `catatan`, `created_at`) VALUES
(1, 'PJW-2026-001', 3, 1, '2026-10-15', 2, 50000.00, 100000.00, 'lunas', 'belum', NULL, 'Kunjungan keluarga untuk foto sunrise', '2026-10-02 07:10:00'),
(2, 'PJW-2026-002', 3, 2, '2026-10-20', 3, 35000.00, 105000.00, 'lunas', 'sudah', '2026-10-02 07:15:00', 'Rombongan Jeep sunrise', '2026-10-02 07:12:00'),
(3, 'PJW-2026-003', 4, 3, '2026-10-25', 1, 50000.00, 50000.00, 'menunggu_pembayaran', 'belum', NULL, 'Pemesanan tiket masuk sore', '2026-10-02 07:20:00');

-- ==============================================================================
-- 9. TABEL: `transaksi_pembayaran` (Bukti & Verifikasi Pembayaran)
-- ==============================================================================
DROP TABLE IF EXISTS `transaksi_pembayaran`;
CREATE TABLE `transaksi_pembayaran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pemesanan_id` INT(11) NOT NULL,
  `kode_transaksi` VARCHAR(50) NOT NULL,
  `metode_pembayaran` VARCHAR(50) NOT NULL,
  `bank_asal` VARCHAR(50) DEFAULT NULL,
  `nama_pengirim` VARCHAR(100) DEFAULT NULL,
  `jumlah_bayar` DECIMAL(12,2) NOT NULL,
  `bukti_transfer` VARCHAR(255) DEFAULT NULL,
  `status_verifikasi` ENUM('menunggu', 'terverifikasi', 'ditolak') DEFAULT 'menunggu',
  `catatan_admin` TEXT,
  `tanggal_bayar` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `diverifikasi_oleh` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_transaksi_code` (`kode_transaksi`),
  KEY `fk_bayar_pesanan` (`pemesanan_id`),
  CONSTRAINT `fk_bayar_pesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan_tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaksi_pembayaran` (`id`, `pemesanan_id`, `kode_transaksi`, `metode_pembayaran`, `bank_asal`, `nama_pengirim`, `jumlah_bayar`, `bukti_transfer`, `status_verifikasi`, `catatan_admin`, `tanggal_bayar`, `diverifikasi_oleh`) VALUES
(1, 1, 'TRX-PJW-991201', 'transfer_bca', 'BCA', 'Muhammad Iqbal', 100000.00, 'assets/images/sample-receipt.jpg', 'terverifikasi', 'Pembayaran diverifikasi otomatis sistem', '2026-10-01 10:30:00', 1),
(2, 2, 'TRX-PJW-991202', 'qris', 'QRIS Mandiri', 'Muhammad Iqbal', 105000.00, 'assets/images/sample-receipt.jpg', 'terverifikasi', 'Valid dan lunas', '2026-10-01 14:15:00', 1);

-- ==============================================================================
-- 10. TABEL: `checkin_petugas` (Log Validasi Tiket Loket Masuk)
-- ==============================================================================
DROP TABLE IF EXISTS `checkin_petugas`;
CREATE TABLE `checkin_petugas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pemesanan_id` INT(11) NOT NULL,
  `petugas_id` INT(11) NOT NULL,
  `waktu_checkin` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `catatan` VARCHAR(255) DEFAULT 'Pemeriksaan tiket via loket',
  PRIMARY KEY (`id`),
  KEY `fk_checkin_pesanan` (`pemesanan_id`),
  KEY `fk_checkin_petugas` (`petugas_id`),
  CONSTRAINT `fk_checkin_pesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan_tiket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_checkin_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `checkin_petugas` (`id`, `pemesanan_id`, `petugas_id`, `waktu_checkin`, `catatan`) VALUES
(1, 2, 2, '2026-10-02 07:15:00', 'Check-in loket gerbang masuk Bromo berhasil');

-- ==============================================================================
-- 11. TABEL: `ulasan_wisata` (Rating & Testimoni Wisatawan)
-- ==============================================================================
DROP TABLE IF EXISTS `ulasan_wisata`;
CREATE TABLE `ulasan_wisata` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `destinasi_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `rating` INT(11) NOT NULL DEFAULT 5,
  `komentar` TEXT NOT NULL,
  `status_tampil` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ulasan_destinasi` (`destinasi_id`),
  KEY `fk_ulasan_user` (`user_id`),
  CONSTRAINT `fk_ulasan_destinasi` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ulasan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ulasan_wisata` (`id`, `destinasi_id`, `user_id`, `rating`, `komentar`, `status_tampil`, `created_at`) VALUES
(1, 1, 3, 5, 'Sungguh pengalaman yang menakjubkan saat melihat matahari terbit di antara stupa Borobudur. Fasilitas sangat bersih dan terawat dengan amat baik!', 1, '2026-10-01 11:00:00'),
(2, 2, 4, 5, 'Bromo selalu membuat saya terpukau. Lautan pasir dan sunrise-nya luar biasa magis. Jangan lupa bawa jaket tebal karena suhunya sangat dingin!', 1, '2026-10-01 15:20:00'),
(3, 4, 3, 5, 'Kawah Putih suasananya romantis dan syahdu banget. Warna airnya toska jernih, spot fotonya sangat estetik!', 1, '2026-10-01 18:40:00');

-- ==============================================================================
-- 12. TABEL: `berita_wisata` (Artikel, Tips & Wawasan Perjalanan)
-- ==============================================================================
DROP TABLE IF EXISTS `berita_wisata`;
CREATE TABLE `berita_wisata` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `cuplikan` TEXT NOT NULL,
  `isi_lengkap` LONGTEXT NOT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(50) DEFAULT 'Tips & Panduan',
  `author_id` INT(11) NOT NULL,
  `dilihat` INT(11) DEFAULT 150,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_berita_slug` (`slug`),
  KEY `fk_berita_author` (`author_id`),
  CONSTRAINT `fk_berita_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `berita_wisata` (`id`, `judul`, `slug`, `cuplikan`, `isi_lengkap`, `gambar`, `kategori`, `author_id`, `dilihat`, `created_at`) VALUES
(1, 'Panduan Lengkap Menikmati Magis Sunrise di Candi Borobudur', 'panduan-sunrise-borobudur', 'Tips waktu terbaik, titik foto terbaik, dan persiapan perlengkapan untuk menyaksikan matahari terbit tercantik di Jawa.', 'Menyaksikan fajar merekah di Candi Borobudur adalah impian jutaan pelancong dunia. Datanglah sebelum pukul 05:00 pagi saat kabut tipis masih menyelimuti lembah perbukitan Menoreh. Siluet stupa yang bermandikan sinar keemasan menciptakan ketenangan jiwa yang luar biasa.', 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800', 'Panduan Wisata', 1, 420, '2026-10-02 07:00:00'),
(2, 'Eksplorasi Kaldera Bromo: Rute Jeep dan Etika Suku Tengger', 'eksplorasi-kaldera-bromo', 'Ketahui tata cara berkunjung, rute penanjakan, dan menjaga kesucian tanah leluhur Tengger.', 'Kawasan Gunung Bromo adalah kawasan suci bagi masyarakat suku Tengger. Ketika berkunjung, patuhi norma adat, jangan membuang sampah sembarangan di lautan pasir, dan persiapkan pakaian hangat berlapis.', 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800', 'Budaya & Adat', 1, 310, '2026-10-02 07:00:00');

-- ==============================================================================
-- 13. TABEL: `log_aktivitas` (Audit Jejak Rekam Sistem)
-- ==============================================================================
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `role` VARCHAR(30) DEFAULT 'tamu',
  `aktivitas` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `log_aktivitas` (`id`, `user_id`, `role`, `aktivitas`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'admin', 'Inisialisasi sistem Pesona Wisata Pulau Jawa', '127.0.0.1', 'Mozilla/5.0 System Installer', '2026-10-02 07:00:00'),
(2, 2, 'petugas', 'Petugas Raden Surya siap bertugas di loket', '127.0.0.1', 'Mozilla/5.0 Staff Desk', '2026-10-02 07:00:00'),
(3, 3, 'pengunjung', 'Muhammad Iqbal memesan tiket Candi Borobudur', '127.0.0.1', 'Mozilla/5.0 Chrome', '2026-10-02 07:00:00');

-- ------------------------------------------------------------------------------
-- AKTIFKAN KEMBALI FOREIGN KEY CHECKS
-- ------------------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- SELESAI: Database wisata_jawa siap digunakan sepenuhnya.
-- ==============================================================================
