# Dokumentasi REST API - Portal Layanan Mahasiswa

Dokumentasi lengkap REST API backend Laravel untuk **Portal Layanan Mahasiswa**.

- **Base URL:** `http://127.0.0.1:8000/api`
- **Format Data:** `application/json`
- **Autentikasi Admin:** Laravel Sanctum (Bearer Token) atau `X-Admin-Token` header

---

## 1. Akun Default Administrator

Gunakan akun ini untuk login ke panel admin backend:

| Keterangan | Nilai |
| :--- | :--- |
| **Email** | `admin@kampus.ac.id` |
| **Kata Sandi** | `password` |
| **Peran (Role)** | `admin` |
| **Fallback Token (Header)** | `petugas-rahasia-123` |

---

## 2. Autentikasi Admin (`/api/auth`)

### A. Login Admin
Memvalidasi kredensial administrator dan menerbitkan token Sanctum. Mahasiswa / akun non-admin ditolak (403 Forbidden).

- **Method:** `POST`
- **Endpoint:** `/api/auth/login`
- **Request Body:**
  ```json
  {
    "email": "admin@kampus.ac.id",
    "password": "password"
  }
  ```
- **Response Success (200 OK):**
  ```json
  {
    "message": "Login berhasil.",
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Admin Akademik",
      "email": "admin@kampus.ac.id",
      "role": "admin"
    }
  }
  ```
- **Response Error (422 Unprocessable):**
  ```json
  {
    "message": "Email atau kata sandi yang Anda masukkan salah.",
    "errors": {
      "email": ["Kredensial tidak sesuai dengan data kami."]
    }
  }
  ```
- **Response Error Non-Admin (403 Forbidden):**
  ```json
  {
    "message": "Akses ditolak. Hanya administrator yang diizinkan masuk ke panel backend."
  }
  ```

---

### B. Profil Admin (Current User)
Mengecek status dan profil admin yang sedang login.

- **Method:** `GET`
- **Endpoint:** `/api/auth/me`
- **Headers:** `Authorization: Bearer <TOKEN>`
- **Response Success (200 OK):**
  ```json
  {
    "user": {
      "id": 1,
      "name": "Admin Akademik",
      "email": "admin@kampus.ac.id",
      "role": "admin"
    }
  }
  ```

---

### C. Logout Admin
Mencabut token sesi yang sedang digunakan.

- **Method:** `POST`
- **Endpoint:** `/api/auth/logout`
- **Headers:** `Authorization: Bearer <TOKEN>`
- **Response Success (200 OK):**
  ```json
  {
    "message": "Logout berhasil."
  }
  ```

---

## 3. Endpoint Publik Mahasiswa (`/api/*`)

Mahasiswa **tidak perlu login akun**, cukup menggunakan **Nomor Pelacakan + NIM** untuk mengakses riwayat permohonan.

### A. Statistik Ringkasan Publik & Dashboard
Menampilkan angka ringkasan layanan, total pengajuan, pengajuan selesai, dan 5 pengajuan terkini.

- **Method:** `GET`
- **Endpoint:** `/api/statistik`
- **Response Success (200 OK):**
  ```json
  {
    "data": {
      "total_layanan_aktif": 6,
      "total_pengajuan": 8,
      "total_pengajuan_selesai": 1,
      "status_ringkasan": [
        { "status": "diajukan", "label": "Diajukan", "total": 2 },
        { "status": "diperiksa", "label": "Sedang Diperiksa", "total": 2 },
        { "status": "perlu_perbaikan", "label": "Perlu Perbaikan", "total": 1 },
        { "status": "disetujui", "label": "Disetujui", "total": 1 },
        { "status": "selesai", "label": "Selesai", "total": 1 },
        { "status": "ditolak", "label": "Ditolak", "total": 1 }
      ],
      "pengajuan_terbaru": [...]
    }
  }
  ```

---

### B. Daftar Layanan Aktif (Katalog & Pencarian)
Mengambil seluruh layanan aktif. Mendukung pencarian teks bebas (kebal huruf besar/kecil dan spasi).

- **Method:** `GET`
- **Endpoint:** `/api/layanan`
- **Query Parameter (Opsional):** `?search=aktif`
- **Response Success (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "slug": "surat-aktif-kuliah",
        "nama": "Surat Aktif Kuliah",
        "deskripsi": "Surat keterangan resmi yang menyatakan bahwa mahasiswa berstatus aktif...",
        "persyaratan": [
          "Kartu Tanda Mahasiswa (KTM)",
          "Bukti Pembayaran UKT Semester Terakhir",
          "Kartu Rencana Studi (KRS)"
        ],
        "estimasi_hari": 1,
        "aktif": true
      }
    ],
    "meta": {
      "total": 1
    }
  }
  ```

---

### C. Detail Layanan
Menampilkan detail satu layanan beserta persyaratan dokumen berdasarkan slug.

- **Method:** `GET`
- **Endpoint:** `/api/layanan/{slug}` (Contoh: `/api/layanan/surat-aktif-kuliah`)
- **Response Success (200 OK):**
  ```json
  {
    "data": {
      "id": 1,
      "slug": "surat-aktif-kuliah",
      "nama": "Surat Aktif Kuliah",
      "deskripsi": "...",
      "persyaratan": [...],
      "estimasi_hari": 1,
      "aktif": true
    }
  }
  ```

---

### D. Mengajukan Permohonan Surat Baru
Membuat pengajuan surat baru. Validasi akan memeriksa kelengkapan checklist dokumen persyaratan layanan yang dipilih.

- **Method:** `POST`
- **Endpoint:** `/api/pengajuan`
- **Request Body:**
  ```json
  {
    "layanan_id": 1,
    "nama_pemohon": "Budi Rahardjo",
    "nim": "220101999",
    "email": "budi@student.univ.ac.id",
    "program_studi": "Teknik Informatika",
    "keperluan": "Keperluan tunjangan instansi dinas orang tua.",
    "dokumen": [
      "Kartu Tanda Mahasiswa (KTM)",
      "Bukti Pembayaran UKT Semester Terakhir",
      "Kartu Rencana Studi (KRS)"
    ]
  }
  ```
- **Response Success (201 Created):**
  ```json
  {
    "data": {
      "id": 9,
      "nomor_pelacakan": "PLM-2026-00009",
      "nama_layanan": "Surat Aktif Kuliah",
      "layanan_slug": "surat-aktif-kuliah",
      "nama_pemohon": "Budi Rahardjo",
      "nim": "220101999",
      "email": "budi@student.univ.ac.id",
      "program_studi": "Teknik Informatika",
      "keperluan": "Keperluan tunjangan instansi dinas orang tua.",
      "dokumen": [...],
      "status": "diajukan",
      "status_label": "Diajukan",
      "catatan_petugas": null,
      "created_at": "2026-10-29T13:40:00+07:00",
      "riwayat": [
        {
          "id": 20,
          "status": "diajukan",
          "status_label": "Diajukan",
          "catatan": "Pengajuan berhasil dibuat oleh pemohon.",
          "created_at": "2026-10-29T13:40:00+07:00"
        }
      ]
    }
  }
  ```

---

### E. Melacak Permohonan (Tracking)
Melacak proses surat berdasarkan nomor pelacakan dan verifikasi NIM.

- **Method:** `GET`
- **Endpoint:** `/api/pengajuan/{nomor_pelacakan}?nim={nim}`
- **Contoh:** `/api/pengajuan/PLM-2026-00001?nim=220101001`
- **Response Success (200 OK):**
  ```json
  {
    "data": {
      "nomor_pelacakan": "PLM-2026-00001",
      "nama_layanan": "Surat Aktif Kuliah",
      "status": "diajukan",
      "status_label": "Diajukan",
      "catatan_petugas": null,
      "riwayat": [...]
    }
  }
  ```
- **Response Error (404 Not Found):** Jika nomor atau NIM tidak cocok.
  ```json
  {
    "message": "Data pengajuan tidak ditemukan."
  }
  ```

---

### F. Memperbaiki Permohonan (Khusus Status `perlu_perbaikan`)
Hanya dapat diperbarui oleh pemohon apabila statusnya `perlu_perbaikan`. Setelah perbaikan dikirim, status otomatis kembali ke `diajukan`.

- **Method:** `PUT`
- **Endpoint:** `/api/pengajuan/{nomor_pelacakan}`
- **Request Body:**
  ```json
  {
    "nim": "210201042",
    "email": "budi.santoso@student.univ.ac.id",
    "program_studi": "Teknik Elektro",
    "keperluan": "Keperluan update berkas terbaru",
    "dokumen": [
      "Scan Ijazah / Transkrip Asli",
      "Bukti Pembayaran Retribusi Legalisasi",
      "KTM / Identitas Diri"
    ]
  }
  ```
- **Response Success (200 OK):** Pengajuan terbarui dan status kembali `diajukan`.

---

## 4. Endpoint Khusus Petugas / Admin (`/api/admin/*`)

> **Keamanan:** Wajib menyertakan Header `Authorization: Bearer <TOKEN>` hasil login admin (atau fallback header `X-Admin-Token: petugas-rahasia-123`).

### A. Daftar Semua Pengajuan (Dengan Filter & Cari)
- **Method:** `GET`
- **Endpoint:** `/api/admin/pengajuan?status=diperiksa&search=Ahmad&per_page=10`

### B. Detail Pengajuan
- **Method:** `GET`
- **Endpoint:** `/api/admin/pengajuan/{id}`

### C. Update Status Pengajuan (ACC / Perlu Perbaikan / Tolak)
- **Method:** `PATCH`
- **Endpoint:** `/api/admin/pengajuan/{id}/status`
- **Request Body:**
  ```json
  {
    "status": "perlu_perbaikan",
    "catatan": "Scan KTM terpotong, harap unggah ulang yang jelas."
  }
  ```
  *(Catatan: `catatan` wajib diisi jika status `perlu_perbaikan` atau `ditolak`). Status `selesai` dan `ditolak` bersifat final dan tidak bisa diubah lagi.*

### D. Hapus Pengajuan
Hanya permohonan berstatus `diajukan` atau `ditolak` yang dapat dihapus.
- **Method:** `DELETE`
- **Endpoint:** `/api/admin/pengajuan/{id}`

### E. CRUD Layanan Kampus
- `GET /api/admin/layanan`: Daftar semua layanan (termasuk yang nonaktif).
- `POST /api/admin/layanan`: Tambah layanan baru (slug otomatis digenerate unik).
- `GET /api/admin/layanan/{id}`: Detail layanan.
- `PUT/PATCH /api/admin/layanan/{id}`: Ubah nama/syarat/status aktif.
- `DELETE /api/admin/layanan/{id}`: Hapus layanan (hanya jika belum memiliki riwayat pengajuan).

---

## 5. 4 Status UI (Untuk Integrasi Frontend Slide 22)

Frontend disarankan mengimplementasikan 4 penanganan respon berikut:
1. **Memuat (*Loading*):** Saat permintaan API dikirimkan.
2. **Berhasil (*Success*):** Menampilkan array `data`.
3. **Kosong (*Empty*):** Ketika `data: []` (misal hasil pencarian nihil).
4. **Gagal (*Error*):** Ketika HTTP Status `404`, `422`, atau `500` menampilkan `message` ramah pengguna.

