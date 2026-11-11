-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 09 Nov 2026 pada 09.49
-- Versi server: 8.4.3
-- Versi PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `rovix_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int UNSIGNED NOT NULL,
  `report_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `action_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `report_id`, `user_id`, `action_name`, `previous_status`, `new_status`, `created_at`) VALUES
(1, 1, 1, 'VERIFIED_AND_DISPATCHED', 'submitted', 'in_progress', '2026-11-08 04:46:28'),
(2, 4, 5, 'LAPORAN_BARU_WARGA', NULL, 'submitted', '2026-11-07 22:23:43'),
(3, 5, 5, 'LAPORAN_BARU_WARGA', NULL, 'submitted', '2026-11-07 22:25:15'),
(4, 6, 5, 'LAPORAN_BARU_WARGA', NULL, 'submitted', '2026-11-07 22:47:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `districts`
--

CREATE TABLE `districts` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `districts`
--

INSERT INTO `districts` (`id`, `name`, `city_code`, `created_at`, `updated_at`) VALUES
(1, 'Kecamatan Jagakarsa', 'JKS-01', '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(2, 'Kecamatan Pasar Minggu', 'JKS-02', '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(3, 'Kecamatan Pancoran', 'JKS-03', '2026-11-07 21:46:27', '2026-11-07 21:46:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ratings`
--

CREATE TABLE `ratings` (
  `id` int UNSIGNED NOT NULL,
  `report_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `score` tinyint UNSIGNED NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ratings`
--

INSERT INTO `ratings` (`id`, `report_id`, `user_id`, `score`, `review_text`, `created_at`) VALUES
(1, 2, 5, 5, 'Respons petugas sangat cepat, jalan sekarang sudah mulus kembali. Terima kasih Dinas PU!', '2026-11-08 04:46:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `district_id` int UNSIGNED NOT NULL,
  `ticket_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `ai_validation` tinyint(1) NOT NULL DEFAULT '0',
  `ai_confidence` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('submitted','verified','in_progress','resolved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `category_id`, `district_id`, `ticket_code`, `description`, `photo_url`, `latitude`, `longitude`, `ai_validation`, `ai_confidence`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, 'RVX-2026-0001', 'Jalan Berlubang Lebar di Depan Stasiun Tanjung Barat — Lubang cukup dalam di lajur kiri arah Depok, sering tergenang air saat hujan.', 'reports/sample-pothole-1.jpg', -6.30720000, 106.83980000, 1, 98.40, 'in_progress', '2026-11-07 21:46:28', '2026-11-07 21:46:28'),
(2, 5, 2, 2, 'RVX-2026-0002', 'Aspal Amblas di Tikungan Jl. Raya Ragunan No. 14 — Permukaan aspal turun sekitar 8 cm sepanjang 2 meter.', 'reports/sample-subsidence-2.jpg', -6.28950000, 106.84020000, 1, 94.10, 'resolved', '2026-11-07 21:46:28', '2026-11-07 21:46:28'),
(3, 5, 3, 3, 'RVX-2026-0003', 'Retak Permukaan Jalan & Trotoar Rusak di Jl. Raya Pasar Minggu KM 7 Kalibata.', 'reports/sample-cracks-3.jpg', -6.25910000, 106.85110000, 1, 91.50, 'submitted', '2026-11-07 21:46:28', '2026-11-07 21:46:28'),
(4, 5, 2, 2, 'RVX-2026-0004', 'bang jalanan rusak akiibat kontener lewat', 'reports/FnEAeKhAoHt1mb7K7A8E5gfNWpC2FvRVDOPis3fZ.png', -6.30720000, 106.83980000, 1, 94.98, 'submitted', '2026-11-07 22:23:43', '2026-11-07 22:23:43'),
(5, 5, 3, 3, 'RVX-2026-0005', 'jalnan rusak akibgat genangan hujan bang', 'reports/5qZOznl2LRT0kGEi9ZeLrX44r53mq0hPey1hsFns.jpg', -6.61031690, 106.79886975, 1, 90.74, 'submitted', '2026-11-07 22:25:15', '2026-11-07 22:25:15'),
(6, 5, 1, 3, 'RVX-2026-0006', 'testfwefew', 'reports/Ngpz1kjskm7qTcggeHl0lrvpFlZpCyTfVUE8LHkq.jpg', -6.30720000, 106.83980000, 1, 91.95, 'submitted', '2026-11-07 22:47:10', '2026-11-07 22:47:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report_categories`
--

CREATE TABLE `report_categories` (
  `id` int UNSIGNED NOT NULL,
  `category_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity_level` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `report_categories`
--

INSERT INTO `report_categories` (`id`, `category_name`, `severity_level`, `created_at`, `updated_at`) VALUES
(1, 'Jalan Berlubang Dalam (Pothole)', 5, '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(2, 'Permukaan Jalan Amblas / Bergelombang', 4, '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(3, 'Retak Buaya & Trotoar Rusak', 3, '2026-11-07 21:46:27', '2026-11-07 21:46:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `district_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('warga','petugas','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warga',
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `district_id`, `name`, `email`, `phone_number`, `password`, `role`, `profile_photo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator Dinas PU', 'admin@rovix.id', '081210002000', '$2y$12$keb7/vD0GndX0nyvjD.YV.MWLSCK6gj4N48qm9e3CWUarjr52XP.q', 'admin', NULL, '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(2, 1, 'Budi Santoso (Petugas Regu Teknis 01)', 'petugas@rovix.id', '081299887766', '$2y$12$Natur3Z4nS/OH7HMki502u6p96CKVs/alzc1S7VRpozD4njGvuhWu', 'petugas', NULL, '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(3, 2, 'Andi Wijaya (Petugas Regu Teknis 02)', 'petugas2@rovix.id', '081299887777', '$2y$12$ud2T4zRpfDmonR1Mq8Z5Q.eHTFj/ewx151sVdFxGOWmz3v4jtvpjS', 'petugas', NULL, '2026-11-07 21:46:27', '2026-11-07 21:46:27'),
(4, 3, 'Hendra Kusuma (Petugas Regu Teknis 03)', 'petugas3@rovix.id', '081299887788', '$2y$12$rw6I8lrla1WVfuv0n5oz5.trrnlSxExtbMq0Lyodkzf.FEt9c1qzm', 'petugas', NULL, '2026-11-07 21:46:28', '2026-11-07 21:46:28'),
(5, 1, 'Rizky Pratama (Warga Pelapor)', 'warga@rovix.id', '081355443322', '$2y$12$7azkLhQuaGh50nNNOqdgqeGOHiMQDKZLUm85h9MKpCTP0A8yaM3ti', 'warga', NULL, '2026-11-07 21:46:28', '2026-11-07 21:46:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `work_orders`
--

CREATE TABLE `work_orders` (
  `id` int UNSIGNED NOT NULL,
  `report_id` int UNSIGNED NOT NULL,
  `officer_id` int UNSIGNED NOT NULL,
  `spk_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_sla` date NOT NULL,
  `work_status` enum('assigned','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `after_photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repair_notes` text COLLATE utf8mb4_unicode_ci,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `work_orders`
--

INSERT INTO `work_orders` (`id`, `report_id`, `officer_id`, `spk_number`, `target_sla`, `work_status`, `after_photo_url`, `repair_notes`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'SPK-PU-2026-001', '2026-11-10', 'in_progress', NULL, 'Segera lakukan penambalan cold-mix aspal karena jalur padat komuter.', NULL, '2026-11-07 21:46:28', '2026-11-07 21:46:28'),
(2, 2, 2, 'SPK-PU-2026-002', '2026-11-07', 'completed', 'work_orders/sample-repaired-2.jpg', 'Perbaikan struktur dasar dan pengaspalan ulang menggunakan Hotmix Asphalt 400 Kg.', '2026-11-08 04:46:28', '2026-11-07 21:46:28', '2026-11-07 21:46:28');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_report_id_index` (`report_id`),
  ADD KEY `audit_logs_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `districts_name_index` (`name`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ratings_report_id_unique` (`report_id`),
  ADD KEY `ratings_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reports_ticket_code_unique` (`ticket_code`),
  ADD KEY `reports_user_id_index` (`user_id`),
  ADD KEY `reports_category_id_index` (`category_id`),
  ADD KEY `reports_district_id_index` (`district_id`),
  ADD KEY `reports_latitude_index` (`latitude`),
  ADD KEY `reports_status_index` (`status`);

--
-- Indeks untuk tabel `report_categories`
--
ALTER TABLE `report_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_district_id_index` (`district_id`),
  ADD KEY `users_role_index` (`role`);

--
-- Indeks untuk tabel `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_orders_report_id_unique` (`report_id`),
  ADD UNIQUE KEY `work_orders_spk_number_unique` (`spk_number`),
  ADD KEY `work_orders_officer_id_index` (`officer_id`),
  ADD KEY `work_orders_work_status_index` (`work_status`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `report_categories`
--
ALTER TABLE `report_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `work_orders`
--
ALTER TABLE `work_orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `report_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `work_orders`
--
ALTER TABLE `work_orders`
  ADD CONSTRAINT `work_orders_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `work_orders_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
