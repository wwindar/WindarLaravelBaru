# Aksara Kampus - Backend REST API

Aksara Kampus adalah portal sastra dan artikel kampus berbasis **Laravel** (PHP 8.4) yang memfasilitasi publikasi karya sastra (puisi, cerpen, artikel, esai), moderasi kurasi naskah oleh dewan dosen/editor, serta pembaca publik.

---

## 1. Tiga Peran Pengguna

1. **Pengunjung Publik (Tanpa Login):**
   * Membaca tulisan yang telah terbit.
   * Melakukan pencarian (search) dan filter berdasarkan kategori, nama penulis, dan tanggal.
   * Melaporkan tulisan jika ada indikasi plagiarisme atau pelanggaran.
   * Melihat kalender agenda kegiatan sastra kampus.

2. **Mahasiswa (Penulis):**
   * Registrasi khusus dengan email resmi kampus (`@umpku.ac.id`, `@student.umpku.ac.id`, `@itspku.ac.id`, `@students.itspku.ac.id`).
   * Mengelola tulisan sendiri: membuat draf, mengedit (hanya saat berstatus Draf atau Perlu Revisi), dan menghapus draf.
   * Mengajukan tulisan ke dosen/editor untuk proses telaah persetujuan.
   * Memantau status tulisan: **Draf**, **Menunggu Persetujuan**, **Terbit**, **Perlu Revisi**, **Ditolak**.
   * Menerima notifikasi in-app saat tulisan disetujui, ditolak, atau diminta revisi.

3. **Dosen / Editor:**
   * Akun dibuat melalui database seeder (tidak melalui form registrasi).
   * Membaca antrean naskah yang menunggu persetujuan.
   * Menyetujui naskah dalam satu klik (langsung terbit otomatis dan mengirim notifikasi ke mahasiswa).
   * Meminta revisi atau menolak naskah (catatan editor wajib diisi dan langsung dikirimkan ke mahasiswa).
   * Membaca dan meninjau laporan tulisan yang masuk dari pengunjung publik.

---

## 2. Akun Contoh (Seed Accounts)

Seluruh password akun awal adalah: `password`

| Peran | Nama | Email | Username | Password |
| :--- | :--- | :--- | :--- | :--- |
| **Dosen / Editor** | Dr. Budi Santoso | `dosen@umpku.ac.id` | `dosen.budi` | `password` |
| **Dosen / Editor** | Prof. Sari Dewi | `dosen@itspku.ac.id` | `dosen.sari` | `password` |
| **Mahasiswa** | Andi Saputra | `andi@students.itspku.ac.id` | `andisaputra` | `password` |
| **Mahasiswa** | Putri Rahayu | `putri@student.umpku.ac.id` | `putrirahayu` | `password` |

---

## 3. Cara Menjalankan Lokal

Buka terminal di folder `backend`:

```powershell
# 1. Pasang dependensi PHP
composer install

# 2. Siapkan file environment
copy .env.example .env

# 3. Generate App Key
php artisan key:generate

# 4. Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# 5. Jalankan Pest Tests (memastikan seluruh fitur berfungsi)
php vendor/pestphp/pest/bin/pest

# 6. Jalankan server lokal
php artisan serve --port=8000
```

Backend aktif di: `http://127.0.0.1:8000` (atau port yang ditentukan).

---

## 4. Daftar Lengkap Endpoint API

Semua respons API menggunakan standar JSON Eloquent Resource yang dibungkus dengan properti `"data"`. Setiap objek berstatus memuat `status` dan `status_label` berbahasa Indonesia.

### A. Autentikasi (`/api/auth/*`)

| Method | Endpoint | Auth | Deskripsi |
| :--- | :--- | :---: | :--- |
| `POST` | `/api/auth/register` | Publik | Registrasi akun mahasiswa baru (domain email kampus resmi) |
| `POST` | `/api/auth/login` | Publik | Login (mengembalikan Sanctum Bearer Token & data user) |
| `GET` | `/api/auth/me` | Bearer Token | Mengambil profil user yang sedang login |
| `POST` | `/api/auth/logout` | Bearer Token | Mencabut token yang sedang aktif |

### B. Publik — Tanpa Login (`/api/*`)

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/kategori` | Daftar rubrik / kategori tulisan |
| `GET` | `/api/agenda` | Daftar agenda kegiatan sastra |
| `GET` | `/api/agenda/{slug}` | Detail kegiatan sastra |
| `GET` | `/api/tulisan` | Daftar tulisan terbit (filter: `search`, `kategori`, `jenis`, `penulis`, `dari`, `sampai`, pagination) |
| `GET` | `/api/tulisan/{slug}` | Detail isi tulisan terbit |
| `POST` | `/api/tulisan/{id}/lapor` | Mengirim laporan pelanggaran / plagiarisme (`nama_pelapor`, `email_pelapor`, `alasan`) |

### C. Mahasiswa — Penulis Naskah (`/api/mahasiswa/*`)
*Wajib Header:* `Authorization: Bearer <token_mahasiswa>`

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/mahasiswa/tulisan` | Daftar seluruh tulisan milik mahasiswa yang sedang login |
| `POST` | `/api/mahasiswa/tulisan` | Membuat tulisan baru (tersimpan sebagai status Draf) |
| `GET` | `/api/mahasiswa/tulisan/{id}` | Detail tulisan milik sendiri beserta histori status |
| `PUT` | `/api/mahasiswa/tulisan/{id}` | Mengedit tulisan (hanya diizinkan jika status Draf atau Perlu Revisi) |
| `DELETE` | `/api/mahasiswa/tulisan/{id}` | Menghapus tulisan (hanya saat status Draf) |
| `POST` | `/api/mahasiswa/tulisan/{id}/ajukan` | Mengajukan tulisan ke dosen/editor (status menjadi Menunggu Persetujuan) |

### D. In-App Notifikasi (`/api/notifikasi/*`)
*Wajib Header:* `Authorization: Bearer <token>`

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/notifikasi` | Daftar notifikasi yang masuk |
| `PATCH` | `/api/notifikasi/{id}/baca` | Menandai satu notifikasi telah dibaca |
| `POST` | `/api/notifikasi/baca-semua` | Menandai seluruh notifikasi telah dibaca |

### E. Dosen / Editor — Kurasi & Moderasi (`/api/dosen/*`)
*Wajib Header:* `Authorization: Bearer <token_dosen>`

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/dosen/antrean` | Membaca antrean naskah yang menunggu persetujuan (`status = 'menunggu'`) |
| `POST` | `/api/dosen/tulisan/{id}/setujui` | Menyetujui naskah dalam satu klik (langsung terbit dan kirim notifikasi ke penulis) |
| `POST` | `/api/dosen/tulisan/{id}/tolak` | Menolak naskah (catatan editor wajib diisi) |
| `POST` | `/api/dosen/tulisan/{id}/revisi` | Mengembalikan naskah untuk direvisi oleh penulis (catatan editor wajib diisi) |
| `GET` | `/api/dosen/laporan` | Daftar laporan tulisan yang masuk dari publik |
| `PATCH` | `/api/dosen/laporan/{id}/status` | Memperbarui status laporan (`menunggu`, `ditinjau`, `selesai`) |

---

## 5. Deploy ke InfinityFree

InfinityFree merupakan shared hosting gratis dengan batasan operasional: **tanpa akses SSH terminal**, **tanpa symlink (`storage:link`)**, **tanpa cron/scheduler**, **antrean queue sinkron**, serta **tanpa upload file multimedia (berbasis teks saja)**.

### Langkah-langkah Deployment:

1. **Import Skema & Data Database ke MySQL phpMyAdmin:**
   * Buka control panel InfinityFree -> phpMyAdmin.
   * Buat database MySQL baru.
   * Masuk ke tab **Import**:
     * Impor file `database/infinityfree/schema.sql` (membuat tabel MySQL lengkap termasuk tabel bawaan Laravel dan Sanctum).
     * Impor file `database/infinityfree/seed.sql` (mengisi data kategori, akun dosen, dan sampel tulisan).

2. **Pengaturan `.env` di Server InfinityFree:**
   ```env
   APP_NAME="Aksara Kampus"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-domain-anda.infinityfreeapp.com

   DB_CONNECTION=mysql
   DB_HOST=sqlxxx.infinityfree.com
   DB_PORT=3306
   DB_DATABASE=if0_xxxxxxxx_aksara
   DB_USERNAME=if0_xxxxxxxx
   DB_PASSWORD=password_db_anda

   # Kepatuhan InfinityFree
   QUEUE_CONNECTION=sync
   SESSION_DRIVER=file
   CACHE_STORE=file

   KAMPUS_EMAIL_DOMAINS="umpku.ac.id,student.umpku.ac.id,itspku.ac.id,students.itspku.ac.id"
   CORS_ALLOWED_ORIGINS="https://frontend-anda.vercel.app,http://localhost:3000"
   ```

3. **Struktur Folder di File Manager (`htdocs`):**
   * Letakkan folder `public/` Laravel di dalam direktori `htdocs/`.
   * Letakkan seluruh folder aplikasi lainnya (`app/`, `bootstrap/`, `config/`, `vendor/`, dll.) di folder root di luar `htdocs/` (atau ubah path pada `htdocs/index.php` ke folder core Laravel).
   * Pastikan folder `storage/` dan `bootstrap/cache/` memiliki izin tulis (writeable, chmod 755/775).
