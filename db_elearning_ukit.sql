-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 20 Des 2025 pada 09.53
-- Versi server: 8.0.30
-- Versi PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_elearning_ukit`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `concentrations`
--

CREATE TABLE `concentrations` (
  `id` int NOT NULL,
  `nama_konsentrasi` varchar(50) NOT NULL,
  `deskripsi` text,
  `icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `concentrations`
--

INSERT INTO `concentrations` (`id`, `nama_konsentrasi`, `deskripsi`, `icon`) VALUES
(1, 'Software Developer', NULL, NULL),
(2, 'IOT', NULL, NULL),
(3, 'Machine Learning', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `concentration_id` int DEFAULT NULL,
  `nama_mk` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `urutan` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `courses`
--

INSERT INTO `courses` (`id`, `concentration_id`, `nama_mk`, `deskripsi`, `urutan`) VALUES
(1, 1, 'Algoritma Dasar', NULL, 1),
(2, 1, 'Pemrograman Dasar', 'Pelajari fondasi utama dalam menulis kode program, logika if-else, looping, dan fungsi dasar.', 2),
(3, 1, 'GIT Dasar', 'Panduan lengkap menggunakan Version Control System (GIT) untuk kolaborasi tim dan manajemen kode.', 3),
(4, 2, 'Dasar IoT & Microcontroller', 'Pelajari cara menghubungkan perangkat keras ke internet menggunakan ESP32 dan Arduino.', 0),
(5, 3, 'Pengantar Data Science', 'Memahami cara mengolah data menjadi informasi berharga menggunakan Python.', 0),
(6, 1, 'Cloud Computing (AWS/GCP)', 'Pengenalan teknologi awan, serverless, dan deployment aplikasi modern.', 12),
(7, 1, 'Struktur Data & Algoritma Lanjut', 'Mempelajari Tree, Graph, dan optimasi algoritma kompleks.', 6),
(8, 1, 'HTML & CSS Dasar', 'Fondasi utama pembuatan website, layouting, dan styling halaman web.', 4),
(9, 1, 'Backend Development (Node.js)', 'Membangun REST API yang cepat dan scalable menggunakan JavaScript di server.', 8),
(10, 1, 'Cyber Security Awareness', 'Dasar-dasar keamanan sistem, enkripsi, dan pencegahan peretasan.', 10),
(11, 1, 'UI/UX Design Fundamentals', 'Prinsip desain antarmuka pengguna, prototyping figma, dan user experience.', 5),
(12, 1, 'Mobile App Development (Flutter)', 'Membuat aplikasi Android dan iOS dengan satu basis kode menggunakan Flutter.', 9),
(13, 1, 'Software Testing (QA)', 'Teknik pengujian perangkat lunak, unit testing, dan automation test.', 11),
(14, 1, 'Database Management (MySQL)', 'Perancangan basis data relasional, normalisasi, dan query SQL kompleks.', 7),
(15, 1, 'DevOps & CI/CD', 'Otomatisasi deployment, penggunaan Docker, dan manajemen server.', 13);

-- --------------------------------------------------------

--
-- Struktur dari tabel `discussions`
--

CREATE TABLE `discussions` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `discussions`
--

INSERT INTO `discussions` (`id`, `course_id`, `user_id`, `message`, `created_at`) VALUES
(8, 0, 1, 'Halo semua selamat malam', '2025-11-24 00:19:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materials`
--

CREATE TABLE `materials` (
  `id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `judul_materi` varchar(200) DEFAULT NULL,
  `deskripsi_materi` text,
  `video_url` varchar(255) DEFAULT NULL,
  `urutan` int NOT NULL,
  `kategori` enum('video','quiz') DEFAULT 'video'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `materials`
--

INSERT INTO `materials` (`id`, `course_id`, `judul_materi`, `deskripsi_materi`, `video_url`, `urutan`, `kategori`) VALUES
(1, 1, 'Pengenalan Algoritma', 'Algoritma dasar adalah langkah-langkah sederhana dan terurut yang digunakan untuk menyelesaikan suatu masalah atau tugas secara sistematis. Algoritma ini biasanya mencakup operasi dasar seperti input data, proses perhitungan atau logika, pengambilan keputusan (percabangan), pengulangan (looping), dan output hasil. Tujuan algoritma dasar adalah memberikan cara yang jelas, efisien, dan mudah dipahami dalam menyelesaikan suatu permasalahan sebelum diubah ke dalam bentuk program.', 'https://www.youtube.com/embed/uqVJc9lLknA', 1, 'video'),
(2, 1, 'Pelajaran Hidup', '“Jauhkanlah dari padaku kecurangan dan kebohongan; jangan berikan kepadaku kemiskinan atau kekayaan, tetapi cukupkanlah aku dengan makanan yang menjadi bagianku. Supaya aku, kalau kenyang, tidak menyangkal-Mu dan berkata: ‘Siapakah Tuhan itu?’ atau kalau aku miskin, mencuri, dan mencemarkan nama Allahku.”', 'https://www.youtube.com/embed/_JjjJGHfwMU', 3, 'video'),
(5, 1, 'Mini Quiz: Evaluasi Dasar', 'Kerjakan kuis singkat ini untuk menguji pemahaman Anda sebelum lanjut.', '', 2, 'quiz'),
(6, 2, 'Pengenalan Bahasa Pemrograman', 'no deksirisi', 'https://www.youtube.com/embed/iEbZPJfF5k4', 1, 'video'),
(7, 2, 'Setup Lingkungan Kerja (VS Code)', 'Cara instalasi teks editor dan persiapan coding.', 'https://www.youtube.com/embed/8aGhZQkoFbQ', 2, 'video'),
(8, 3, 'Apa itu Version Control?', 'Memahami pentingnya GIT dalam proyek tim.', 'https://www.youtube.com/embed/lX9hsDBI-rI', 1, 'video'),
(9, 3, 'Perintah Dasar GIT (Add, Commit, Push)', 'Praktek langsung menggunakan terminal git.', 'https://www.youtube.com/embed/M8P79Ff32vw', 2, 'video'),
(10, 4, 'Pengenalan Internet of Things', 'Apa itu IoT dan bagaimana ia mengubah dunia industri?', 'https://www.youtube.com/embed/6mBO2vqLv38', 1, 'video'),
(11, 4, 'Mengenal ESP32 dan Sensor', 'Perbedaan Arduino vs ESP32 serta cara baca sensor DHT11.', 'https://www.youtube.com/embed/5pQkJAj0k9k', 2, 'video'),
(12, 4, 'Kuis: Konsep Hardware IoT', 'Uji pemahaman tentang sensor dan aktuator.', '', 3, 'quiz'),
(13, 5, 'Apa itu Data Science?', 'Perbedaan Data Science, AI, dan Machine Learning.', 'https://www.youtube.com/embed/ukzFI9rgwfU', 1, 'video'),
(14, 5, 'Python untuk Analisis Data', 'Dasar library Pandas dan NumPy.', 'https://www.youtube.com/embed/mTv_e1Zf7j8', 2, 'video'),
(15, 5, 'Kuis: Logika Data', 'Evaluasi pemahaman tentang tipe data dan algoritma.', '', 3, 'quiz');

-- --------------------------------------------------------

--
-- Struktur dari tabel `progress`
--

CREATE TABLE `progress` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `status` enum('selesai') DEFAULT 'selesai',
  `tanggal_selesai` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `progress`
--

INSERT INTO `progress` (`id`, `user_id`, `material_id`, `status`, `tanggal_selesai`) VALUES
(1, 1, 1, 'selesai', '2025-11-23 18:44:26'),
(2, 1, 5, 'selesai', '2025-11-23 19:13:01'),
(3, 1, 2, 'selesai', '2025-11-23 19:20:11'),
(4, 1, 10, 'selesai', '2025-11-23 19:48:07'),
(5, 1, 11, 'selesai', '2025-11-23 19:48:10'),
(6, 1, 13, 'selesai', '2025-11-23 19:48:36'),
(7, 1, 14, 'selesai', '2025-11-23 19:48:39'),
(8, 1, 12, 'selesai', '2025-11-23 20:09:02'),
(9, 1, 15, 'selesai', '2025-11-23 20:27:53'),
(10, 1, 6, 'selesai', '2025-11-23 20:37:09'),
(11, 1, 7, 'selesai', '2025-11-23 20:37:16'),
(12, 1, 8, 'selesai', '2025-11-23 20:37:41'),
(13, 1, 9, 'selesai', '2025-11-23 20:37:45'),
(14, 3, 1, 'selesai', '2025-12-15 21:18:58'),
(15, 3, 5, 'selesai', '2025-12-15 21:20:03'),
(16, 3, 2, 'selesai', '2025-12-15 21:20:58'),
(17, 3, 10, 'selesai', '2025-12-16 11:51:46'),
(18, 3, 11, 'selesai', '2025-12-16 11:52:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL,
  `material_id` int DEFAULT NULL,
  `pertanyaan` text,
  `opsi_a` varchar(255) DEFAULT NULL,
  `opsi_b` varchar(255) DEFAULT NULL,
  `opsi_c` varchar(255) DEFAULT NULL,
  `opsi_d` varchar(255) DEFAULT NULL,
  `jawaban_benar` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `material_id`, `pertanyaan`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `jawaban_benar`) VALUES
(1, 1, 'Apa definisi sederhana dari Algoritma?', 'Sebuah bahasa pemrograman', 'Urutan langkah-langkah logis penyelesaian masalah', 'Komponen hardware komputer', 'Sistem operasi berbasis Linux', 'b'),
(2, 1, 'Manakah yang BUKAN merupakan ciri algoritma yang baik?', 'Setiap langkah harus jelas (Definite)', 'Memiliki input dan output', 'Langkah-langkahnya tak terbatas (Infinite)', 'Efektif dan Efisien', 'c'),
(3, 1, 'Simbol flowchart berbentuk BELAH KETUPAT (Diamond) digunakan untuk?', 'Proses (Process)', 'Input/Output Data', 'Mulai/Selesai (Terminator)', 'Pengambilan Keputusan (Decision)', 'd'),
(4, 5, 'Apa tujuan utama dari sebuah Algoritma?', 'Memasak Nasi', 'Menyelesaikan masalah secara sistematis', 'Membuat desain grafis', 'Bermain Game', 'b'),
(5, 5, 'Manakah simbol flowchart untuk Start/End?', 'Persegi Panjang', 'Jajar Genjang', 'Oval (Terminator)', 'Belah Ketupat', 'c'),
(6, 5, 'Bahasa pemrograman PHP dieksekusi di sisi?', 'Client Side', 'Server Side', 'Monitor Side', 'Keyboard Side', 'b'),
(7, 5, 'Variable dalam PHP diawali dengan simbol?', '#', '@', '$', '%', 'c'),
(8, 5, 'Perintah untuk menampilkan teks di PHP adalah?', 'print_r', 'echo', 'system.out', 'console.log', 'b'),
(9, 5, 'Looping yang akan dijalankan minimal satu kali adalah?', 'For', 'While', 'Do-While', 'Foreach', 'c'),
(10, 5, 'Manakah tipe data untuk bilangan desimal?', 'Integer', 'String', 'Boolean', 'Float', 'd'),
(11, 5, 'HTML adalah singkatan dari?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Tech Markup Link', 'Home Tool Markup Language', 'a'),
(12, 12, 'Komponen yang berfungsi mendeteksi perubahan lingkungan disebut?', 'Aktuator', 'Sensor', 'Resistor', 'Kapasitor', 'b'),
(13, 12, 'Manakah microcontroller yang memiliki fitur Wi-Fi bawaan?', 'Arduino Uno', 'Arduino Nano', 'ESP32', 'ATmega328', 'c'),
(14, 15, 'Library Python yang paling populer untuk manipulasi tabel data adalah?', 'Matplotlib', 'Pandas', 'Seaborn', 'Flask', 'b'),
(15, 15, 'Proses membersihkan data yang kotor disebut?', 'Data Mining', 'Data Cleaning', 'Data Warehouse', 'Big Data', 'b');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quiz_scores`
--

CREATE TABLE `quiz_scores` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `skor` int DEFAULT NULL,
  `tanggal_kerja` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `quiz_scores`
--

INSERT INTO `quiz_scores` (`id`, `user_id`, `material_id`, `skor`, `tanggal_kerja`) VALUES
(1, 1, 5, 100, '2025-11-23 21:49:46'),
(2, 1, 12, 50, '2025-11-23 20:08:57'),
(3, 1, 15, 100, '2025-11-23 20:27:49'),
(4, 3, 5, 100, '2025-12-15 13:19:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `submissions`
--

CREATE TABLE `submissions` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tanggal_kumpul` datetime DEFAULT CURRENT_TIMESTAMP,
  `nilai` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `submissions`
--

INSERT INTO `submissions` (`id`, `user_id`, `material_id`, `file_path`, `tanggal_kumpul`, `nilai`) VALUES
(1, 3, 1, 'https://github.com/nugrocode/Pembelajaran-Mesin-Gestur-Tangan.git', '2025-12-15 21:17:10', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nim_nidn` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') DEFAULT 'mahasiswa',
  `foto_profil` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nim_nidn`, `nama_lengkap`, `password`, `role`, `foto_profil`) VALUES
(1, '222611095', 'Nugroho Indrayadi', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'default.png'),
(3, '222611001', 'admin test', '0192023a7bbd73250516f069df18b500', 'mahasiswa', 'default.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `concentrations`
--
ALTER TABLE `concentrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `concentration_id` (`concentration_id`);

--
-- Indeks untuk tabel `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_course_discussion` (`course_id`);

--
-- Indeks untuk tabel `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indeks untuk tabel `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indeks untuk tabel `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indeks untuk tabel `quiz_scores`
--
ALTER TABLE `quiz_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indeks untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_nidn` (`nim_nidn`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `concentrations`
--
ALTER TABLE `concentrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `quiz_scores`
--
ALTER TABLE `quiz_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`concentration_id`) REFERENCES `concentrations` (`id`);

--
-- Ketidakleluasaan untuk tabel `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Ketidakleluasaan untuk tabel `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);

--
-- Ketidakleluasaan untuk tabel `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `quiz_scores`
--
ALTER TABLE `quiz_scores`
  ADD CONSTRAINT `quiz_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_scores_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
