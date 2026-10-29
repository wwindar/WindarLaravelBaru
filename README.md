# Portal Layanan Mahasiswa (E-Services Kampus)

Proyek Portal Layanan Administrasi & Surat Mahasiswa untuk mata kuliah **Pengembangan UI Modern (Vue, Nuxt, dan Next.js)** - Prodi S1 Informatika, Universitas Muhammadiyah PKU Surakarta.

---

## Arsitektur Sistem

Aplikasi dibangun dengan arsitektur terpisah (*Decoupled / Headless*):

```text
projek-windar/
├── backend/            # REST API Laravel (Sanctum Auth, Database, Business Logic)
├── API_DOCUMENTATION.md # Dokumentasi lengkap seluruh endpoint REST API
└── README.md           # Panduan menjalankan proyek
```

---

## Fitur Utama Backend

1. **Autentikasi Administrator (Laravel Sanctum):**
   * Endpoint Login, Profile, dan Logout.
   * Hanya akun berlabel `admin` yang diizinkan mengakses panel belakang.
   * Mahasiswa/pengguna biasa tidak diizinkan masuk ke rute admin (403 Forbidden).

2. **Katalog & Pencarian Layanan:**
   * 6 Layanan Surat Resmi (Surat Aktif Kuliah, Surat Pengantar Penelitian, Legalisasi Dokumen, Surat Rekomendasi Beasiswa, Surat Bebas Sanksi, Pengajuan Cuti).
   * Pencarian fleksibel (*case-insensitive* dan *trimmed*).

3. **Alur Pengajuan & Pelacakan Mandiri (Guest Tracking):**
   * Mahasiswa tidak perlu registrasi akun, cukup mengajukan permohonan dan menerima **Nomor Pelacakan unik** (`PLM-YYYY-XXXXX`).
   * Pelacakan aman menggunakan **Nomor Pelacakan + NIM**.
   * Validasi checklist dokumen persyaratan wajib.
   * Mahasiswa dapat memperbaiki berkas jika status dinyatakan `perlu_perbaikan` oleh petugas.

4. **Panel Admin Petugas Kampus:**
   * Filter status pengajuan dan pencarian pemohon.
   * Update status (*Sedang Diperiksa*, *Perlu Perbaikan*, *Disetujui*, *Selesai*, *Ditolak*) disertai catatan petugas.
   * CRUD jenis layanan kampus.
   * Statistik dan metrik pengajuan.

---

## Panduan Menjalankan Backend

1. Masuk ke folder backend:
   ```bash
   cd backend
   ```

2. Salin environment dan jalankan migrasi database + seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   Server REST API akan berjalan di: `http://127.0.0.1:8000`

4. Jalankan pengujian otomatis (Unit & Feature Test):
   ```bash
   php artisan test
   ```

---

## Akun Login Administrator

Gunakan akun berikut untuk menguji login admin:
* **Email:** `admin@kampus.ac.id`
* **Password:** `password`
* **Role:** `admin`

---

## Dokumentasi API Lengkap

Silakan baca [API_DOCUMENTATION.md](file:///c:/Users/USER/Herd/projek-windar/API_DOCUMENTATION.md) untuk detail endpoint, schema JSON, dan contoh pengujian.
