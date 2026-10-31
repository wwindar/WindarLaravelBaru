-- =============================================================================
-- Aksara Kampus - Database Seed Data (InfinityFree / phpMyAdmin)
-- Data awal untuk kategori, akun dosen, mahasiswa, tulisan sampel, dan agenda.
-- Password untuk semua akun contoh di bawah adalah: password
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. Kategori
-- -----------------------------------------------------------------------------
INSERT INTO `kategori` (`id`, `slug`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'sastra-indonesia', 'Sastra Indonesia', NOW(), NOW()),
(2, 'fiksi', 'Fiksi', NOW(), NOW()),
(3, 'nonfiksi', 'Non-fiksi', NOW(), NOW()),
(4, 'budaya', 'Budaya & Seni', NOW(), NOW()),
(5, 'ilmiah', 'Ilmiah Populer', NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 2. Akun Dosen & Mahasiswa
-- Password semua akun: password ($2y$12$diqhfp2wnBI0tvicR2RuXOgbS.4Wt/PIOTw7HsMlXSZMlDbDSyls6)
-- -----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Budi Santoso', 'dosen.budi', 'dosen@umpku.ac.id', 'dosen', '$2y$12$diqhfp2wnBI0tvicR2RuXOgbS.4Wt/PIOTw7HsMlXSZMlDbDSyls6', NOW(), NOW()),
(2, 'Prof. Sari Dewi', 'dosen.sari', 'dosen@itspku.ac.id', 'dosen', '$2y$12$diqhfp2wnBI0tvicR2RuXOgbS.4Wt/PIOTw7HsMlXSZMlDbDSyls6', NOW(), NOW()),
(3, 'Andi Saputra', 'andisaputra', 'andi@students.itspku.ac.id', 'mahasiswa', '$2y$12$diqhfp2wnBI0tvicR2RuXOgbS.4Wt/PIOTw7HsMlXSZMlDbDSyls6', NOW(), NOW()),
(4, 'Putri Rahayu', 'putrirahayu', 'putri@student.umpku.ac.id', 'mahasiswa', '$2y$12$diqhfp2wnBI0tvicR2RuXOgbS.4Wt/PIOTw7HsMlXSZMlDbDSyls6', NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 3. Sampel Tulisan
-- -----------------------------------------------------------------------------
INSERT INTO `tulisan` (`id`, `user_id`, `kategori_id`, `jenis`, `slug`, `judul`, `isi`, `ringkasan`, `status`, `catatan_editor`, `diterbitkan_pada`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'puisi', 'rindu-di-ujung-pena', 'Rindu di Ujung Pena', 'Wahai kertas putih\nTerima curahan rinduku\nYang tak bertepi\n\nTintaku mengalir deras\nSebagaimana air mata\nYang tak terbendung\n\nDi ujung pena ini\nAku titipkan rinduku\nKepada angin malam.', 'Puisi tentang kerinduan yang dituangkan lewat tulisan.', 'terbit', NULL, NOW(), NOW(), NOW()),
(2, 3, 2, 'cerpen', 'surat-untuk-ibu', 'Surat untuk Ibu', 'Di sudut kamar kos yang sempit, Dimas menuliskan kata demi kata untuk ibunya di kampung. Jari-jarinya yang lelah setelah seharian bekerja paruh waktu tidak menyurutkan semangatnya. Ia ingin ibunya tahu bahwa ia baik-baik saja, meski kebenaran itu tidak seluruhnya ia ceritakan.', 'Cerpen tentang seorang anak rantau yang menyembunyikan kesulitannya dari ibunya.', 'terbit', NULL, NOW(), NOW(), NOW()),
(3, 4, 1, 'esai', 'membaca-budaya-dari-lembaran-puisi', 'Membaca Budaya dari Lembaran Puisi', 'Puisi bukan sekadar rangkaian kata indah. Ia adalah cermin budaya yang merekam denyut kehidupan masyarakat di setiap zamannya. Dalam lembaran-lembaran puisi Chairil Anwar, kita dapat membaca kegelisahan generasi muda Indonesia pasca kemerdekaan.', 'Esai tentang puisi sebagai cermin budaya bangsa.', 'menunggu', NULL, NULL, NOW(), NOW()),
(4, 4, 2, 'cerpen', 'di-balik-jendela-perpustakaan', 'Di Balik Jendela Perpustakaan', 'Anggun selalu datang ke perpustakaan pukul tiga sore. Bukan karena ia kutu buku, tetapi karena dari balik jendela perpustakaan itu ia bisa melihat lapangan basket tempat Reza berlatih.', 'Cerpen roman tentang seorang mahasiswi dan pengamatan rahasianya.', 'draf', NULL, NULL, NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 4. Sampel Agenda Kegiatan Sastra
-- -----------------------------------------------------------------------------
INSERT INTO `agenda` (`id`, `slug`, `judul`, `jenis`, `deskripsi`, `lokasi`, `mulai_pada`, `selesai_pada`, `kuota`, `created_at`, `updated_at`) VALUES
(1, 'lomba-puisi-nasional-2026', 'Lomba Puisi Nasional Aksara 2026', 'lomba', 'Ajang bergengsi tahunan untuk penulis puisi seluruh Indonesia.', 'Aula Utama Kampus UMPKU', DATE_ADD(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 33 DAY), 100, NOW(), NOW()),
(2, 'bedah-buku-sastra-modern', 'Bedah Buku: Sastra Modern Indonesia', 'bedah_buku', 'Diskusi mendalam tentang perkembangan sastra modern di Indonesia.', 'Ruang Seminar Gedung B', DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY), 50, NOW(), NOW());

