-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Mar 2026 pada 14.46
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_digital`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `penulis`, `kategori`, `deskripsi`, `penerbit`, `tahun_terbit`, `stok`, `gambar`) VALUES
(1, 'Rahasia-rahasia Senja', 'Rizal De Loesie', 'Cerpen', 'Senja, dengan keindahan cahayanya yang merona, seringkali menjadi saksi dari beragam perasaan dan pengalaman dalam kehidupan kita. Dalam buku ini, para penulis muda kami menjelajahi berbagai aspek kehidupan, baik yang romantis, misterius, maupun penuh perjalanan. Setiap cerita di sini adalah jendela ke dalam imajinasi dan pemikiran mereka, yang dihiasi dengan kata-kata penuh makna.', 'Guepedia', '2019', 19, 'book_1774941829_105.jpg'),
(2, 'Seporsi Mie Ayam Sebelum Mati', 'Brian Khrisna', 'NOVEL', 'Ale, seorang pria berusia 37 tahun memiliki tinggi badan 189 cm dan berat 138 kg. Badannya bongsor, berkulit hitam, dan memiliki masalah dengan bau badan. Sejak kecil, Ale hidup di lingkungan keluarga yang tidak mendukungnya. Ia tak memiliki teman dekat dan menjadi korban perundungan di sekolahnya.\r\nAle didiagnosis psikiaternya mengalami depresi akut. Bukannya Ale tidak peduli untuk memperbaiki dirinya sendiri, ia peduli. Ale telah berusaha mengatasi masalah-masalah yang timbul dari dirinya agar ia diterima di lingkungan pertemanan. Namun usahanya tidak pernah berhasil. Bahkan keluarganya pun tidak mendukungnya saat Ale membutuhkan sandaran dan dukungan.\r\n\r\nAtas itu semua, Ale memutuskan untuk mati. Ia mempersiapkan kematiannya dengan baik. Agar ketika mati pun, Ale tidak banyak merepotkan orang. Dua puluh empat jam dari sekarang, ia akan menelan obat antidepresan yang dia punya sekaligus. Sebelum waktu itu tiba, Ale membersihkan apartemennya yang berantakan, makan makanan mahal yang tak pernah ia beli, pergi berkaraoke dan menyanyi sepuasnya hingga mabuk.\r\n\r\nSaat 24 jam itu tiba, Ale telah bersiap dengan kemeja hitam dan celana hitam, bak baju melayat ke pemakamannya sendiri. Ia kenakan topi kecurut ulang tahun dan meletuskan konfeti yang ia beli untuk dirinya sendiri.\r\n“Selamat ulang tahun yang terakhir, Ale.”\r\n\r\nAle siap menenggak seluruh obat antidepresan yang ia punya. Saat ia memain-mainkan botolnya, Ale terdiam saat membaca anjuran di kemasan botol itu, dikonsumsi sesudah makan. Seketika perutnya berbunyi. Dan Ale pun memutuskan untuk makan dulu sebelum mengakhiri hidupnya. Setidaknya, itu akan menjadi satu-satunya keputusan yang bisa dia ambil atas kehendaknya sendiri. Setelah selama hidupnya ia tak pernah mampu melakukan hal-hal yang ia inginkan.\r\n\r\nAle akan makan seporsi mie ayam sebelum mati.', 'Gramedia Widiasarana Indonesia', '2025', 28, 'book_1774943887_482.avif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_buku` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','kembali') DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 1, 1, '2026-03-31', '2026-03-31', 'kembali'),
(2, 1, 1, '2026-03-31', '2026-03-31', 'kembali'),
(3, 1, 1, '2026-03-31', '2026-03-31', 'kembali'),
(4, 1, 2, '2026-03-31', '2026-03-31', 'kembali'),
(5, 1, 2, '2026-03-31', '2026-03-31', 'kembali'),
(6, 1, 2, '2026-03-31', '2026-04-07', 'dipinjam'),
(7, 4, 1, '2026-03-31', '2026-04-07', 'dipinjam'),
(8, 4, 2, '2026-03-31', '2026-04-07', 'dipinjam'),
(9, 2, 2, '2026-03-31', '2026-03-31', 'kembali'),
(10, 2, 2, '2026-03-31', '2026-03-31', 'kembali'),
(11, 2, 2, '2026-03-31', '2026-03-31', 'kembali');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `role` enum('admin','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `telepon`, `role`) VALUES
(1, 'ronijuliana', '$2y$10$pDzhV0DkpMUI67TKpWQqKOpPcg9mqprkq//ZA/L23Tf82JpoO6Utu', 'Roni Juliana', '+6282126412243', 'siswa'),
(2, 'admin', '$2y$10$dLxc3ad3RkVbZPrX0oqd9./mgE224OXSs09EEG5CAqJAgLx.ignT2', 'Administrator Zanith', '08123456789', 'admin'),
(4, 'gusyanto', '$2y$10$VryuqndXefXWbkwrcNbqPuewjyXmVds6PrMBnkgvSbOMQ.C688Spi', 'Gusyanton', NULL, 'siswa'),
(5, 'asepsaptu', '$2y$10$r8hWOEaeBktImLDG.ASkzuBHQKWSH.Ne.qsiA1wGluhUH5is/RWE2', 'asepsaptu', NULL, 'siswa'),
(6, 'bangbam', '$2y$10$XQdfQpJpzOCmACvRAdxLE.daXW3s0ozOz0l7SA6ZbpEL9KXCyzqKa', 'bangbam', NULL, 'siswa'),
(7, 'donisiu', '$2y$10$iSq3Jgx48nwNogBHc9PHJepn9tXFSGUUVMpgFzF6L1tztJLcub/FO', 'donisiu', NULL, 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
