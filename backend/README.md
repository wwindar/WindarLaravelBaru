# Portal Layanan Mahasiswa - Backend REST API

Backend REST API untuk studi kasus **Portal Layanan Mahasiswa** (Mata Kuliah *Pengembangan UI Modern*). Sistem ini menyediakan layanan bagi mahasiswa untuk menelusuri jenis layanan administrasi kampus, mengajukan permohonan secara mandiri, memperoleh nomor pelacakan unik, serta memantau progres status dan catatan perbaikan dari petugas. Petugas/admin dapat memverifikasi berkas dan memperbarui status pengajuan.

> [!NOTE]
> **Catatan Simulasi Data**: Seluruh data mahasiswa, NIM, nomor pelacakan, dan dokumen pada repositori ini adalah **data simulasi fiktif** untuk keperluan pembelajaran, tanpa menggunakan data pribadi nyata.

---

## 1. Spesifikasi Teknis & Lingkungan
- **Framework**: Laravel 12 (PHP 8.4)
- **Database**: SQLite (`database/database.sqlite`)
- **Port Default**: `8001`
- **Base URL API**: `http://localhost:8001/api`
- **Admin API Token Contoh**: `petugas-rahasia-123` (disertakan pada header `X-Admin-Token`)

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

Server akan aktif dan siap menerima request pada: `http://localhost:8001/api`.

---

## 3. Daftar Status Pengajuan (Enum)

Pengajuan dikelola menggunakan PHP Backed Enum dengan kode dan label tampilan sebagai berikut:

| Kode Status (`status`) | Label Indonesia (`status_label`) | Catatan Petugas | Sifat Status |
| :--- | :--- | :--- | :--- |
| `diajukan` | Diajukan | Opsional | Status awal setelah permohonan dikirim |
| `diperiksa` | Sedang Diperiksa | Opsional | Berkas dalam tahap verifikasi staf |
| `perlu_perbaikan` | Perlu Perbaikan | **Wajib Diisi** | Ada berkas/syarat yang perlu diperbaiki |
| `disetujui` | Disetujui | Opsional | Dokumen siap diterbitkan/ditandatangani |
| `selesai` | Selesai | Opsional | **Final** (tidak dapat diubah lagi) |
| `ditolak` | Ditolak | **Wajib Diisi** | **Final** (tidak dapat diubah lagi) |

Setiap perubahan status otomatis mencatatkan riwayat pada tabel `riwayat_status`.

---

## 4. Daftar Endpoint API

### A. Endpoint Publik

#### 1. `GET /api/layanan`
Menampilkan daftar layanan akademik aktif. Mendukung parameter pencarian `?search=` (tidak sensitif huruf besar/kecil dan mengabaikan spasi tepi).

- **Query Parameters**:
  - `search` (opsional): kata kunci nama atau deskripsi layanan.
- **Contoh Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "slug": "surat-aktif-kuliah",
      "nama": "Surat Aktif Kuliah",
      "deskripsi": "Surat keterangan resmi yang menyatakan bahwa mahasiswa berstatus aktif mengikuti perkuliahan pada semester berjalan...",
      "persyaratan": [
        "Kartu Tanda Mahasiswa (KTM)",
        "Bukti Pembayaran UKT Semester Terakhir",
        "Kartu Rencana Studi (KRS)"
      ],
      "estimasi_hari": 1,
      "aktif": true,
      "created_at": "2026-10-22T07:30:16+00:00",
      "updated_at": "2026-10-22T07:30:16+00:00"
    }
  ],
  "meta": {
    "total": 6
  }
}
```

#### 2. `GET /api/layanan/{slug}`
Menampilkan rincian layanan beserta persyaratan dokumen yang wajib dilampirkan.

- **Contoh URL**: `/api/layanan/surat-aktif-kuliah`
- **Response 404 jika slug tidak ada**:
```json
{
  "message": "Layanan tidak ditemukan."
}
```

#### 3. `POST /api/pengajuan`
Membuat permohonan layanan baru. Sistem memvalidasi apakah layanan aktif dan memeriksa kelengkapan semua dokumen persyaratan wajib.

- **Request Body**:
```json
{
  "layanan_id": 1,
  "nama_pemohon": "Ahmad Rizky Pratama",
  "nim": "220101001",
  "email": "ahmad.rizky@student.univ.ac.id",
  "program_studi": "Teknik Informatika",
  "keperluan": "Pengajuan tunjangan anak untuk instansi dinas orang tua.",
  "dokumen": [
    "Kartu Tanda Mahasiswa (KTM)",
    "Bukti Pembayaran UKT Semester Terakhir",
    "Kartu Rencana Studi (KRS)"
  ]
}
```
- **Response Sukses (201 Created)**:
```json
{
  "data": {
    "id": 9,
    "nomor_pelacakan": "PLM-2026-00009",
    "layanan_id": 1,
    "nama_layanan": "Surat Aktif Kuliah",
    "nama_pemohon": "Ahmad Rizky Pratama",
    "nim": "220101001",
    "email": "ahmad.rizky@student.univ.ac.id",
    "program_studi": "Teknik Informatika",
    "keperluan": "Pengajuan tunjangan anak untuk instansi dinas orang tua.",
    "dokumen": [
      "Kartu Tanda Mahasiswa (KTM)",
      "Bukti Pembayaran UKT Semester Terakhir",
      "Kartu Rencana Studi (KRS)"
    ],
    "status": "diajukan",
    "status_label": "Diajukan",
    "catatan_petugas": null,
    "riwayat": [
      {
        "id": 18,
        "pengajuan_id": 9,
        "status": "diajukan",
        "status_label": "Diajukan",
        "catatan": "Pengajuan berhasil dibuat oleh pemohon.",
        "created_at": "2026-10-22T07:35:00+00:00",
        "updated_at": "2026-10-22T07:35:00+00:00"
      }
    ],
    "created_at": "2026-10-22T07:35:00+00:00",
    "updated_at": "2026-10-22T07:35:00+00:00"
  }
}
```
- **Response Error Dokumen Kurang (422 Unprocessable Content)**:
```json
{
  "message": "Validasi gagal. Silakan periksa kembali data yang Anda kirimkan.",
  "errors": {
    "dokumen": [
      "Dokumen persyaratan belum lengkap. Dokumen yang belum dilampirkan: Kartu Rencana Studi (KRS)."
    ]
  }
}
```

#### 4. `GET /api/pengajuan/{nomor_pelacakan}?nim=...`
Lacak status pengajuan permohonan. Parameter query `nim` wajib disertakan dan harus cocok dengan nomor pelacakan untuk menjaga privasi data.

- **Contoh URL**: `/api/pengajuan/PLM-2026-00003?nim=210201042`
- **Contoh Response (200 OK)**:
```json
{
  "data": {
    "id": 3,
    "nomor_pelacakan": "PLM-2026-00003",
    "layanan_id": 3,
    "nama_layanan": "Legalisasi Dokumen",
    "nama_pemohon": "Budi Santoso",
    "nim": "210201042",
    "email": "budi.santoso@student.univ.ac.id",
    "program_studi": "Teknik Elektro",
    "keperluan": "Kelengkapan berkas administrasi rekrutmen BUMN.",
    "dokumen": [
      "Scan Ijazah / Transkrip Asli",
      "Bukti Pembayaran Retribusi Legalisasi",
      "KTM / Identitas Diri"
    ],
    "status": "perlu_perbaikan",
    "status_label": "Perlu Perbaikan",
    "catatan_petugas": "Scan Ijazah Asli terpotong pada bagian nomor seri ijazah dan cap universitas. Harap unggah ulang dokumen scan yang utuh dan jelas.",
    "riwayat": [
      {
        "id": 6,
        "pengajuan_id": 3,
        "status": "perlu_perbaikan",
        "status_label": "Perlu Perbaikan",
        "catatan": "Scan Ijazah Asli terpotong pada bagian nomor seri ijazah dan cap universitas. Harap unggah ulang dokumen scan yang utuh dan jelas.",
        "created_at": "2026-10-22T07:30:16+00:00",
        "updated_at": "2026-10-22T07:30:16+00:00"
      },
      {
        "id": 5,
        "pengajuan_id": 3,
        "status": "diperiksa",
        "status_label": "Sedang Diperiksa",
        "catatan": "Pemeriksaan berkas legalisir.",
        "created_at": "2026-10-22T07:30:16+00:00",
        "updated_at": "2026-10-22T07:30:16+00:00"
      },
      {
        "id": 4,
        "pengajuan_id": 3,
        "status": "diajukan",
        "status_label": "Diajukan",
        "catatan": "Pengajuan berhasil dibuat oleh pemohon.",
        "created_at": "2026-10-22T07:30:16+00:00",
        "updated_at": "2026-10-22T07:30:16+00:00"
      }
    ],
    "created_at": "2026-10-22T07:30:16+00:00",
    "updated_at": "2026-10-22T07:30:16+00:00"
  }
}
```
- **Response Jika NIM / Nomor Salah (404 Not Found)**:
```json
{
  "message": "Data pengajuan tidak ditemukan."
}
```

---

### B. Endpoint Petugas / Admin (Dilindungi Header `X-Admin-Token`)

Semua endpoint `/api/admin/*` mewajibkan header:
`X-Admin-Token: petugas-rahasia-123`

Jika header tidak disertakan atau salah, server mengembalikan **401 Unauthorized (JSON)**:
```json
{
  "message": "Token admin tidak valid atau tidak disertakan."
}
```

#### 1. `GET /api/admin/pengajuan`
Daftar seluruh pengajuan mahasiswa dengan paginasi, filter status (`?status=`), dan pencarian (`?search=`).

- **Query Parameters**:
  - `status` (opsional): filter kode status (`diajukan`, `diperiksa`, `perlu_perbaikan`, `disetujui`, `selesai`, `ditolak`).
  - `search` (opsional): cari berdasarkan nama pemohon, NIM, atau nomor pelacakan.
  - `page` (opsional): nomor halaman (default: 1).
  - `per_page` (opsional): jumlah item per halaman (default: 10).

#### 2. `GET /api/admin/pengajuan/{id}`
Melihat detail pengajuan berdasarkan ID beserta riwayat status lengkap dari yang terbaru.

#### 3. `PATCH /api/admin/pengajuan/{id}/status`
Memperbarui status pengajuan dan mencatat riwayat perubahan.

- **Request Body**:
```json
{
  "status": "perlu_perbaikan",
  "catatan": "Scan KTM terpotong, silakan unggah ulang."
}
```
- **Aturan**:
  - Field `catatan` **wajib diisi** apabila status diubah menjadi `perlu_perbaikan` atau `ditolak`.
  - Status `selesai` dan `ditolak` bersifat **final**. Mengubah status pengajuan yang sudah final mengembalikan **422 Unprocessable Content**:
  ```json
  {
    "message": "Pengajuan dengan status selesai atau ditolak tidak dapat diubah lagi."
  }
  ```

---

## 5. Contoh Pengujian dengan PowerShell (`Invoke-RestMethod`)

Pastikan server telah berjalan di terminal lain (`php artisan serve --port=8001`).

### A. Mendapatkan Daftar Layanan
```powershell
Invoke-RestMethod -Uri "http://localhost:8001/api/layanan" -Method GET | ConvertTo-Json -Depth 5
```

### B. Mencari Layanan
```powershell
Invoke-RestMethod -Uri "http://localhost:8001/api/layanan?search=penelitian" -Method GET | ConvertTo-Json -Depth 5
```

### C. Mengirim Pengajuan Baru
```powershell
$body = @{
    layanan_id = 1
    nama_pemohon = "Ahmad Rizky Pratama"
    nim = "220101001"
    email = "ahmad.rizky@student.univ.ac.id"
    program_studi = "Teknik Informatika"
    keperluan = "Pengajuan tunjangan anak dinas orang tua"
    dokumen = @(
        "Kartu Tanda Mahasiswa (KTM)",
        "Bukti Pembayaran UKT Semester Terakhir",
        "Kartu Rencana Studi (KRS)"
    )
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "http://localhost:8001/api/pengajuan" `
    -Method POST `
    -Headers @{ "Content-Type" = "application/json"; "Accept" = "application/json" } `
    -Body $body

$response | ConvertTo-Json -Depth 5
```

### D. Melacak Pengajuan dengan NIM
```powershell
$nomor = $response.data.nomor_pelacakan
Invoke-RestMethod -Uri "http://localhost:8001/api/pengajuan/$nomor`?nim=220101001" -Method GET | ConvertTo-Json -Depth 5
```

### E. Admin Mengubah Status Pengajuan
```powershell
$statusBody = @{
    status = "diperiksa"
    catatan = "Berkas sedang diperiksa oleh staf administrasi akademik."
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/admin/pengajuan/1/status" `
    -Method PATCH `
    -Headers @{
        "X-Admin-Token" = "petugas-rahasia-123"
        "Content-Type" = "application/json"
        "Accept" = "application/json"
    } `
    -Body $statusBody | ConvertTo-Json -Depth 5
```

---

## 6. Menjalankan Automated Tests

Seluruh skenario pengujian unit & feature test dapat dijalankan dengan:

```powershell
php artisan test
```
