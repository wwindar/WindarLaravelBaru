-- =============================================================================
-- Aksara Kampus - Database Schema MySQL (InfinityFree / phpMyAdmin)
-- Kompatibel dengan MySQL 5.7+ / MariaDB 10.3+ / MySQL 8.0+
-- Dibuat untuk hosting InfinityFree yang tidak memiliki akses terminal/SSH
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `notifikasi`;
DROP TABLE IF EXISTS `laporan_tulisan`;
DROP TABLE IF EXISTS `riwayat_status_tulisan`;
DROP TABLE IF EXISTS `tulisan`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `agenda`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `users`;

-- -----------------------------------------------------------------------------
-- 1. Tabel users (Akun Mahasiswa dan Dosen)
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `role` VARCHAR(30) NOT NULL DEFAULT 'mahasiswa',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Tabel password_reset_tokens (Bawaan Laravel)
-- -----------------------------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Tabel sessions (Bawaan Laravel jika SESSION_DRIVER=database)
-- -----------------------------------------------------------------------------
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Tabel cache & cache_locks (Bawaan Laravel jika CACHE_STORE=database)
-- -----------------------------------------------------------------------------
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Tabel personal_access_tokens (Laravel Sanctum - Wajib untuk Auth Token)
-- -----------------------------------------------------------------------------
CREATE TABLE `personal_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` TEXT NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `abilities` TEXT DEFAULT NULL,
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. Tabel kategori (Rubrik Kategori Tulisan)
-- -----------------------------------------------------------------------------
CREATE TABLE `kategori` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategori_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. Tabel tulisan (Karya Puisi, Cerpen, Artikel, Esai)
-- -----------------------------------------------------------------------------
CREATE TABLE `tulisan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `kategori_id` BIGINT UNSIGNED DEFAULT NULL,
  `jenis` VARCHAR(20) NOT NULL, -- puisi|cerpen|artikel|esai
  `slug` VARCHAR(255) NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `isi` LONGTEXT NOT NULL,
  `ringkasan` TEXT DEFAULT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'draf', -- draf|menunggu|terbit|perlu_revisi|ditolak
  `catatan_editor` TEXT DEFAULT NULL,
  `diterbitkan_pada` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tulisan_slug_unique` (`slug`),
  KEY `tulisan_user_id_foreign` (`user_id`),
  KEY `tulisan_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `fk_tulisan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tulisan_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. Tabel riwayat_status_tulisan (Tracking Perubahan Status dan Catatan)
-- -----------------------------------------------------------------------------
CREATE TABLE `riwayat_status_tulisan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tulisan_id` BIGINT UNSIGNED NOT NULL,
  `status` VARCHAR(30) NOT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_status_tulisan_tulisan_id_foreign` (`tulisan_id`),
  CONSTRAINT `fk_riwayat_tulisan` FOREIGN KEY (`tulisan_id`) REFERENCES `tulisan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. Tabel laporan_tulisan (Laporan Pelanggaran / Plagiarisme Publik)
-- -----------------------------------------------------------------------------
CREATE TABLE `laporan_tulisan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tulisan_id` BIGINT UNSIGNED NOT NULL,
  `nama_pelapor` VARCHAR(100) NOT NULL,
  `email_pelapor` VARCHAR(150) NOT NULL,
  `alasan` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'menunggu', -- menunggu|ditinjau|selesai
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laporan_tulisan_tulisan_id_foreign` (`tulisan_id`),
  CONSTRAINT `fk_laporan_tulisan` FOREIGN KEY (`tulisan_id`) REFERENCES `tulisan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. Tabel notifikasi (In-App Notification untuk Penulis / User)
-- -----------------------------------------------------------------------------
CREATE TABLE `notifikasi` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `pesan` TEXT NOT NULL,
  `dibaca` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_user_id_foreign` (`user_id`),
  CONSTRAINT `fk_notifikasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. Tabel agenda (Agenda & Kegiatan Sastra Kampus)
-- -----------------------------------------------------------------------------
CREATE TABLE `agenda` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `jenis` ENUM('lomba', 'bedah_buku', 'open_mic', 'workshop') NOT NULL,
  `deskripsi` LONGTEXT NOT NULL,
  `lokasi` VARCHAR(255) NOT NULL,
  `mulai_pada` DATETIME NOT NULL,
  `selesai_pada` DATETIME NOT NULL,
  `kuota` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agenda_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

