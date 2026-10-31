# Panduan Deployment Aksara Kampus ke InfinityFree

Panduan langkah demi langkah untuk mengunggah proyek **Aksara Kampus** (Frontend Nuxt 4 + Backend Laravel) ke hosting gratis **InfinityFree** dalam arsitektur **Satu Domain (Single Domain)**.

---

## Ringkasan Arsitektur Satu Domain

Di InfinityFree, seluruh file publik disajikan dari folder `htdocs/`. Paket deploy ini mengintegrasikan:
1. **Frontend Nuxt (SPA Static):** Berkas HTML, JS, dan CSS statis diletakkan langsung di root `htdocs/`.
2. **Backend Laravel:** Diletakkan di subfolder `htdocs/backend-core/` yang **diblokir dari akses langsung browser** lewat `.htaccess`.
3. **Endpoint API:** Setiap permintaan ke `https://domain-anda.infinityfreeapp.com/api/*` dialihkan oleh `.htaccess` secara internal ke `htdocs/backend-core/public/index.php`.
4. **Keamanan:** File `.env`, folder `storage`, dan `vendor` terlindungi dan tidak bisa diunduh oleh pengunjung.

---

## Langkah 1: Buat Database MySQL di Control Panel InfinityFree

1. Masuk ke **Client Area InfinityFree** (`app.infinityfree.com`) dan buka akun hosting Anda.
2. Klik tombol **Control Panel** (VistaPanel / cPanel).
3. Cari menu **MySQL Databases**:
   * Masukkan nama database baru (misal: `aksara`).
   * Klik **Create Database**.
4. Catat informasi koneksi database yang tertera di halaman tersebut:
   * **MySQL Database Name:** Contoh: `if0_38000000_aksara`
   * **MySQL Hostname:** Contoh: `sql123.infinityfree.com` *(PENTING: Gunakan hostname ini, BUKAN localhost!)*
   * **MySQL Username:** Contoh: `if0_38000000`
   * **MySQL Password:** Password akun hosting InfinityFree Anda.

---

## Langkah 2: Impor Skema & Data Awal via phpMyAdmin

1. Di Control Panel InfinityFree, klik menu **phpMyAdmin**.
2. Klik tombol **Connect** di sebelah nama database yang baru saja dibuat.
3. Setelah masuk ke phpMyAdmin:
   * Klik tab **Import** pada menu atas.
   * Klik **Choose File** lalu pilih file `backend/database/infinityfree/schema.sql`.
   * Klik tombol **Import** (atau Go) di bagian bawah. Tunggu sampai muncul notifikasi hijau sukses.
4. Ulangi proses impor untuk data awal:
   * Klik tab **Import** kembali.
   * Pilih file `backend/database/infinityfree/seed.sql`.
   * Klik tombol **Import**.
5. Database kini telah terisi tabel `users`, `kategori`, `tulisan`, `riwayat_status_tulisan`, `laporan_tulisan`, `notifikasi`, `agenda`, dan `personal_access_tokens`.

---

## Langkah 3: Jalankan Script Build di Komputer Lokal

Buka PowerShell di komputer Anda pada folder proyek:

```powershell
# Jalankan script build otomatis
.\deploy\build-infinityfree.ps1
```

Script ini akan otomatis:
1. Melakukan `npx nuxt generate` dengan `NUXT_PUBLIC_API_BASE=/api`.
2. Menyalin berkas frontend Nuxt ke folder `deploy/htdocs`.
3. Menyalin backend Laravel ke `deploy/htdocs/backend-core`.
4. Menjalankan `composer install --no-dev --optimize-autoloader` agar dependensi PHP siap produksi.
5. Menyiapkan `.htaccess` ganda (routing `/api` dan SPA fallback) serta perlindungan folder.

---

## Langkah 4: Sesuaikan File `.env` Produksi

Buka file `deploy/htdocs/backend-core/.env` di editor teks, lalu perbarui baris berikut sesuai data dari Langkah 1:

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
DB_PASSWORD=password_akun_hosting_anda

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file

KAMPUS_EMAIL_DOMAINS="umpku.ac.id,student.umpku.ac.id,itspku.ac.id,students.itspku.ac.id"
```

---

## Langkah 5: Unggah File Menggunakan FileZilla (FTP)

1. Buka aplikasi **FileZilla** di komputer Anda.
2. Masukkan data FTP dari Client Area InfinityFree:
   * **Host:** `ftpupload.net`
   * **Username:** Username akun hosting Anda (misal `if0_38000000`).
   * **Password:** Password akun hosting Anda.
   * **Port:** `21`
   * Klik **Quickconnect**.
3. Di panel sebelah kanan (Remote Site):
   * Masuk ke folder `htdocs/`.
   * Hapus file bawaan default InfinityFree (`index2.html` atau sejenisnya) jika ada.
4. Di panel sebelah kiri (Local Site):
   * Buka folder `projek-windar/deploy/htdocs/`.
   * Pilih seluruh isi di dalamnya (`.htaccess`, `index.html`, `_nuxt/`, `backend-core/`, dll.).
   * Klik kanan lalu pilih **Upload**.
5. Tunggu hingga seluruh transfer berkas selesai.
6. Pastikan permission folder `backend-core/storage/` dan `backend-core/bootstrap/cache/` bernilai `755` atau `775` (writeable).

---

## Langkah 6: Pengujian Sistem di Browser

1. Buka domain website Anda di browser: `https://nama-domain-anda.infinityfreeapp.com`
2. **Uji Halaman Publik:**
   * Pastikan beranda memuat karya sastra terbaru dari database.
   * Coba fitur pencarian dan filter di menu **Karya Sastra**.
   * Buka salah satu karya dan uji tombol **Bagikan** serta modal **Laporkan tulisan**.
3. **Uji Akun Dosen / Editor:**
   * Buka halaman `/login`.
   * Masuk dengan akun: `dosen@umpku.ac.id` / `password`.
   * Pastikan dasbor antrean memuat naskah yang menunggu telaah.
   * Coba klik tombol **Setujui** atau **Minta Revisi**.
4. **Uji Akun Mahasiswa:**
   * Logout lalu buka halaman `/register`.
   * Coba daftar dengan email kampus (misal `@students.itspku.ac.id`).
   * Masuk ke dasbor **Tulisan Saya**, buat karya baru, simpan draf, dan ajukan untuk publikasi.

---

## Tips & Troubleshooting InfinityFree

* **Error 500 saat akses API:** Periksa versi PHP di control panel (*Select PHP Version*), pastikan memilih **PHP 8.2** atau **PHP 8.3**.
* **Database Connection Refused:** Pastikan `DB_HOST` menggunakan hostname MySQL dari panel InfinityFree (contoh: `sql300.infinityfree.com`), bukan `127.0.0.1` atau `localhost`.
* **Refresh Halaman 404 pada SPA:** Pastikan file `.htaccess` di root `htdocs/` terunggah dengan benar karena FileZilla terkadang menyembunyikan file berawalan titik (`dotfiles`). Aktifkan opsi *Server -> Force showing hidden files* di FileZilla.

