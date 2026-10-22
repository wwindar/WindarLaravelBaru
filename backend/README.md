# Portal Layanan Mahasiswa - Backend REST API (CRUD Penuh)

Backend REST API untuk studi kasus **Portal Layanan Mahasiswa** (Mata Kuliah *Pengembangan UI Modern*). Sistem ini menyediakan layanan komprehensif bagi mahasiswa untuk menelusuri layanan administrasi kampus, mengajukan permohonan mandiri, memperoleh nomor pelacakan otomatis (`PLM-YYYY-XXXXX`), memantau riwayat status & catatan perbaikan, serta melakukan perbaikan berkas. Petugas/admin memiliki hak akses CRUD penuh untuk mengelola master layanan dan memproses/menghapus pengajuan permohonan.

> [!NOTE]
> **Catatan Simulasi Data**: Seluruh data mahasiswa, NIM, nomor pelacakan, dan dokumen pada sistem ini adalah **data simulasi fiktif** untuk keperluan pembelajaran, tanpa menggunakan data pribadi nyata.

---

## 1. Spesifikasi Teknis & Lingkungan
- **Framework**: Laravel 12 (PHP 8.4)
- **Database**: SQLite (`database/database.sqlite`)
- **Port Default**: `8001`
- **Base URL API**: `http://localhost:8001/api`
- **Admin API Token Contoh**: `petugas-rahasia-123` (wajib dikirimkan melalui header `X-Admin-Token` untuk endpoint admin)

---

## 2. Cara Menjalankan Backend

Jalankan perintah berikut di PowerShell dari direktori `backend/`:

```powershell
# 1. Masuk ke folder backend
cd backend

# 2. Instalasi dependensi composer (jika belum)
composer install

# 3. Salin file environment jika belum ada
if (-not (Test-Path .env)) { Copy-Item .env.example .env }

# 4. Generate application key (jika belum)
php artisan key:generate

# 5. Jalankan migrasi dan seeding data simulasi
php artisan migrate:fresh --seed

# 6. Jalankan development server pada port 8001
php artisan serve --port=8001
```

Server aktif dan siap melayani request pada: `http://localhost:8001/api`.

---

## 3. Daftar Status Pengajuan (Enum)

Pengajuan dikelola menggunakan PHP Backed Enum:

| Kode Status (`status`) | Label Indonesia (`status_label`) | Catatan Petugas | Sifat Status / Aksi Diizinkan |
| :--- | :--- | :--- | :--- |
| `diajukan` | Diajukan | Opsional | Status awal pengajuan; dapat dihapus oleh admin |
| `diperiksa` | Sedang Diperiksa | Opsional | Berkas dalam verifikasi staf administrasi |
| `perlu_perbaikan` | Perlu Perbaikan | **Wajib Diisi** | Mahasiswa dapat memperbaiki berkas via `PUT /api/pengajuan/{nomor}` |
| `disetujui` | Disetujui | Opsional | Pengajuan disetujui, dalam proses penerbitan |
| `selesai` | Selesai | Opsional | **Final** (tidak dapat diubah statusnya lagi) |
| `ditolak` | Ditolak | **Wajib Diisi** | **Final**; dapat dihapus oleh admin |

---

## 4. Tabel Rangkuman Endpoint API (CRUD Penuh)

| Modul | Method | Endpoint | Hak Akses | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| **Layanan (Publik)** | `GET` | `/api/layanan` | Publik | Daftar layanan aktif (dukung `?search=`) |
| | `GET` | `/api/layanan/{slug}` | Publik | Detail layanan dan persyaratan dokumen |
| **Pengajuan (Publik)** | `POST` | `/api/pengajuan` | Publik | Buat pengajuan baru (auto generate nomor) |
| | `GET` | `/api/pengajuan/{nomor}?nim=...` | Publik | Lacak status pengajuan (wajib cocok NIM) |
| | `PUT` | `/api/pengajuan/{nomor}` | Publik | Mahasiswa memperbaiki berkas (status `perlu_perbaikan`) |
| **Layanan (Admin)** | `GET` | `/api/admin/layanan` | Petugas (`X-Admin-Token`) | Semua layanan (termasuk nonaktif), pagination & search |
| | `POST` | `/api/admin/layanan` | Petugas (`X-Admin-Token`) | Buat layanan baru (slug otomatis unik) |
| | `GET` | `/api/admin/layanan/{id}` | Petugas (`X-Admin-Token`) | Detail layanan untuk admin |
| | `PUT/PATCH` | `/api/admin/layanan/{id}` | Petugas (`X-Admin-Token`) | Ubah data layanan (nama, syarat, estimasi, aktif) |
| | `DELETE` | `/api/admin/layanan/{id}` | Petugas (`X-Admin-Token`) | Hapus layanan (tolak 422 bila ada relasi pengajuan) |
| **Pengajuan (Admin)** | `GET` | `/api/admin/pengajuan` | Petugas (`X-Admin-Token`) | Daftar pengajuan dengan filter status & pencarian |
| | `GET` | `/api/admin/pengajuan/{id}` | Petugas (`X-Admin-Token`) | Detail pengajuan beserta log riwayat |
| | `PATCH` | `/api/admin/pengajuan/{id}/status` | Petugas (`X-Admin-Token`) | Ubah status & tambah riwayat |
| | `DELETE` | `/api/admin/pengajuan/{id}` | Petugas (`X-Admin-Token`) | Hapus pengajuan (khusus status `diajukan`/`ditolak`) |

---

## 5. Rincian Endpoint & Contoh Request/Response

### A. Endpoint Mahasiswa / Publik

#### 1. `GET /api/layanan`
Menampilkan daftar layanan akademik yang berstatus aktif.
- **Query Parameter**: `?search=...` (abaikan huruf besar/kecil dan spasi tepi).
- **Format Respons**:
```json
{
  "data": [
    {
      "id": 1,
      "slug": "surat-aktif-kuliah",
      "nama": "Surat Aktif Kuliah",
      "deskripsi": "Surat keterangan resmi mahasiswa aktif...",
      "persyaratan": [
        "Kartu Tanda Mahasiswa (KTM)",
        "Bukti Pembayaran UKT Semester Terakhir",
        "Kartu Rencana Studi (KRS)"
      ],
      "estimasi_hari": 1,
      "aktif": true
    }
  ],
  "meta": { "total": 6 }
}
```

#### 2. `GET /api/layanan/{slug}`
Menampilkan detail lengkap layanan. Bila slug tidak ditemukan, mengembalikan HTTP 404: `{"message":"Layanan tidak ditemukan."}`.

#### 3. `POST /api/pengajuan`
Membuat permohonan baru. Memvalidasi bahwa seluruh dokumen persyaratan wajib disertakan.
- **Body**: `layanan_id`, `nama_pemohon`, `nim`, `email`, `program_studi`, `keperluan`, `dokumen[]`.
- **Respons (201 Created)**: Objek pengajuan dengan nomor pelacakan otomatis (format `PLM-2026-XXXXX`), status `diajukan`, dan riwayat status awal.

#### 4. `GET /api/pengajuan/{nomor_pelacakan}?nim=...`
Lacak status pengajuan. NIM wajib cocok dengan data pengajuan; jika nomor atau NIM salah, mengembalikan HTTP 404: `{"message":"Data pengajuan tidak ditemukan."}`.

#### 5. `PUT /api/pengajuan/{nomor_pelacakan}`
Memperbaiki berkas pengajuan oleh mahasiswa saat status pengajuan adalah `perlu_perbaikan`.
- **Body**: `nim`, `email`, `program_studi`, `keperluan`, `dokumen[]`.
- **Aturan**:
  - NIM wajib cocok dengan pemilik pengajuan (jika tidak cocok: 404 Not Found).
  - Hanya dapat dijalankan jika status pengajuan `perlu_perbaikan` (status lain: 422 Unprocessable Content).
  - Seluruh dokumen persyaratan wajib disertakan (jika kurang: 422 menyebutkan dokumen yang belum dilampirkan).
  - Setelah berhasil, status pengajuan otomatis kembali ke `diajukan`, catatan perbaikan dibersihkan, dan ditambahkan catatan riwayat baru.

---

### B. Endpoint Petugas / Admin (Header `X-Admin-Token: petugas-rahasia-123`)

#### 1. `GET /api/admin/layanan`
Menampilkan semua master layanan (termasuk yang tidak aktif) dengan pagination dan pencarian `?search=`.

#### 2. `POST /api/admin/layanan`
Membuat master layanan baru.
- **Body**:
```json
{
  "nama": "Surat Bebas Pustaka",
  "deskripsi": "Surat keterangan bebas pinjaman koleksi perpustakaan universitas.",
  "persyaratan": [
    "Kartu Tanda Mahasiswa (KTM)",
    "Bukti Pengembalian Buku / Bebas Denda"
  ],
  "estimasi_hari": 1,
  "aktif": true
}
```
- **Respons (201 Created)**: Slug dibuat otomatis dan dipastikan unik (contoh: `surat-bebas-pustaka`).

#### 3. `GET /api/admin/layanan/{id}`
Melihat detail master layanan berdasarkan ID.

#### 4. `PUT/PATCH /api/admin/layanan/{id}`
Memperbarui informasi master layanan (nama, deskripsi, persyaratan, estimasi hari, aktif). Jika nama diubah, slug otomatis disesuaikan secara unik.

#### 5. `DELETE /api/admin/layanan/{id}`
Menghapus master layanan.
- **Aturan**: Jika layanan sudah pernah digunakan oleh pengajuan mahasiswa, penghapusan ditolak dengan HTTP 422:
```json
{
  "message": "Layanan tidak dapat dihapus karena sudah memiliki data pengajuan terkait. Disarankan untuk menonaktifkan layanan (ubah aktif menjadi false)."
}
```

#### 6. `GET /api/admin/pengajuan`
Daftar seluruh pengajuan mahasiswa dengan pagination, filter status (`?status=diajukan`), dan pencarian (`?search=Budi`).

#### 7. `GET /api/admin/pengajuan/{id}`
Detail pengajuan beserta seluruh catatan riwayat status dari yang paling baru.

#### 8. `PATCH /api/admin/pengajuan/{id}/status`
Mengubah status pengajuan oleh petugas.
- **Body**: `status`, `catatan`.
- **Aturan**:
  - `catatan` wajib diisi apabila status diubah ke `perlu_perbaikan` atau `ditolak`.
  - Status `selesai` dan `ditolak` bersifat **final** (ubah lagi = 422).

#### 9. `DELETE /api/admin/pengajuan/{id}`
Menghapus pengajuan permohonan.
- **Aturan**: Hanya diizinkan untuk pengajuan yang berstatus `diajukan` atau `ditolak`. Pengajuan dengan status lain ditolak dengan HTTP 422:
```json
{
  "message": "Pengajuan hanya dapat dihapus apabila berstatus Diajukan atau Ditolak."
}
```
Riwayat status terkait akan ikut terhapus secara otomatis (*cascade*).

---

## 6. Contoh Pengujian PowerShell (`Invoke-RestMethod`)

Pastikan development server aktif (`php artisan serve --port=8001`).

### A. Admin: Buat Layanan Baru
```powershell
$layananBody = @{
    nama = "Surat Bebas Pustaka"
    deskripsi = "Surat keterangan bebas pinjaman buku perpustakaan."
    persyaratan = @("KTM", "Bukti Bebas Denda")
    estimasi_hari = 1
    aktif = $true
} | ConvertTo-Json

$layananBaru = Invoke-RestMethod -Uri "http://localhost:8001/api/admin/layanan" `
    -Method POST `
    -Headers @{ "X-Admin-Token" = "petugas-rahasia-123"; "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $layananBody

$layananBaru | ConvertTo-Json -Depth 5
```

### B. Admin: Ubah Layanan
```powershell
$updateLayananBody = @{
    nama = "Surat Bebas Perpustakaan Pusat"
    estimasi_hari = 2
} | ConvertTo-Json

$layananId = $layananBaru.data.id
Invoke-RestMethod -Uri "http://localhost:8001/api/admin/layanan/$layananId" `
    -Method PUT `
    -Headers @{ "X-Admin-Token" = "petugas-rahasia-123"; "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $updateLayananBody | ConvertTo-Json -Depth 5
```

### C. Mahasiswa: Ajukan Permohonan
```powershell
$pengajuanBody = @{
    layanan_id = $layananId
    nama_pemohon = "Citra Kirana"
    nim = "220108888"
    email = "citra@student.univ.ac.id"
    program_studi = "Sistem Informasi"
    keperluan = "Syarat wisuda kelulusan"
    dokumen = @("KTM", "Bukti Bebas Denda")
} | ConvertTo-Json

$pengajuanBaru = Invoke-RestMethod -Uri "http://localhost:8001/api/pengajuan" `
    -Method POST `
    -Headers @{ "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $pengajuanBody

$pengajuanBaru | ConvertTo-Json -Depth 5
```

### D. Petugas: Minta Perbaikan Berkas
```powershell
$mintaPerbaikanBody = @{
    status = "perlu_perbaikan"
    catatan = "Scan Bukti Bebas Denda buram dan tidak terbaca stempelnya."
} | ConvertTo-Json

$pengajuanId = $pengajuanBaru.data.id
Invoke-RestMethod -Uri "http://localhost:8001/api/admin/pengajuan/$pengajuanId/status" `
    -Method PATCH `
    -Headers @{ "X-Admin-Token" = "petugas-rahasia-123"; "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $mintaPerbaikanBody | ConvertTo-Json -Depth 5
```

### E. Mahasiswa: Kirim Perbaikan Pengajuan
```powershell
$nomorPelacakan = $pengajuanBaru.data.nomor_pelacakan
$perbaikanBody = @{
    nim = "220108888"
    email = "citra.update@student.univ.ac.id"
    program_studi = "Sistem Informasi"
    keperluan = "Syarat wisuda kelulusan (unggah ulang berkas)"
    dokumen = @("KTM", "Bukti Bebas Denda")
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/pengajuan/$nomorPelacakan" `
    -Method PUT `
    -Headers @{ "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $perbaikanBody | ConvertTo-Json -Depth 5
```

### F. Admin: Hapus Pengajuan (Status Diajukan / Ditolak)
```powershell
Invoke-RestMethod -Uri "http://localhost:8001/api/admin/pengajuan/$pengajuanId" `
    -Method DELETE `
    -Headers @{ "X-Admin-Token" = "petugas-rahasia-123"; "Accept" = "application/json" } | ConvertTo-Json
```

### G. Admin: Hapus Layanan
```powershell
Invoke-RestMethod -Uri "http://localhost:8001/api/admin/layanan/$layananId" `
    -Method DELETE `
    -Headers @{ "X-Admin-Token" = "petugas-rahasia-123"; "Accept" = "application/json" } | ConvertTo-Json
```

---

## 7. Menjalankan Seluruh Pengujian (Testing)

Jalankan perintah berikut untuk mengeksekusi 34 feature tests:

```powershell
php artisan test
```
