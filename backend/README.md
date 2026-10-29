# Aksara Kampus - Backend REST API

**Aksara Kampus** adalah portal komunitas sastra kampus yang menyediakan media penerbitan artikel (berita & blog sastra), publikasi karya sastra mahasiswa (puisi, cerpen, esai), mekanisme pengiriman dan kurasi naskah oleh dewan redaksi, serta kalender agenda kegiatan sastra.

> **Catatan:** Seluruh data pada proyek ini adalah **data simulasi** dan seluruh teks naskah contoh (puisi, cerpen, esai, berita, dan blog) adalah **karya orisinal**.

---

## 1. Nilai Token & Konfigurasi Redaksi

* **ADMIN_API_TOKEN:** `redaksi-rahasia-123`
* **Header Autentikasi Admin:** `X-Admin-Token: redaksi-rahasia-123`
* **Base URL:** `http://localhost:8001/api`

---

## 2. Cara Menjalankan Backend

Buka PowerShell di subfolder `backend`:

```powershell
# 1. Pasang dependensi Composer (bila pertama kali)
composer install

# 2. Salin environment
copy .env.example .env

# 3. Generate Application Key
php artisan key:generate

# 4. Jalankan migrasi dan seeder data orisinal
php artisan migrate:fresh --seed

# 5. Jalankan unit & feature tests
php artisan test

# 6. Jalankan server lokal pada port 8001
php artisan serve --port=8001
```

---

## 3. Daftar Status Karya Sastra

Status karya menggunakan PHP Backed Enum (`App\Enums\StatusKarya`):

| Kode Status | Label Status | Sifat | Keterangan |
| :--- | :--- | :---: | :--- |
| `dikirim` | **Dikirim** | Terbuka | Karya baru dikirim oleh penulis dan menunggu telaah redaksi |
| `ditinjau` | **Sedang Ditinjau** | Terbuka | Naskah sedang dalam tahap kurasi oleh kurator redaksi |
| `perlu_revisi`| **Perlu Revisi** | Terbuka | Penulis diminta memperbaiki naskah sesuai catatan redaksi |
| `terbit` | **Terbit** | **Final** | Karya telah disetujui dan tampil pada katalog publik |
| `ditolak` | **Ditolak** | **Final** | Naskah belum memenuhi standar kurasi redaksi |

*(Status `terbit` dan `ditolak` bersifat final dan tidak dapat diubah lagi).*

---

## 4. Tabel Endpoint & Contoh Pemanggilan (PowerShell Invoke-RestMethod)

### A. Endpoint Publik Mahasiswa & Pembaca

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/kategori` | Daftar seluruh kategori artikel & rubrik sastra |
| `GET` | `/api/artikel` | Daftar artikel terbit (filter: `?jenis=`, `?kategori={slug}`, `?search=`, pagination) |
| `GET` | `/api/artikel/{slug}` | Detail artikel terbit (404 jika draft / tidak ada) |
| `GET` | `/api/karya` | Daftar karya sastra terbit (filter: `?jenis=`, `?search=`, pagination) |
| `GET` | `/api/karya/{slug}` | Detail karya sastra terbit |
| `POST` | `/api/kiriman` | Mengirim karya sastra baru oleh penulis (isi min 50 karakter) |
| `GET` | `/api/kiriman/{nomor}?email=...` | Melacak status kiriman karya (email wajib cocok) |
| `PUT` | `/api/kiriman/{nomor}` | Merevisi naskah karya (khusus status `perlu_revisi`) |
| `GET` | `/api/agenda` | Daftar agenda sastra (filter: `?jenis=`, `?search=`, `?waktu=mendatang\|lampau`) |
| `GET` | `/api/agenda/{slug}` | Detail agenda sastra |

#### Contoh Pemanggilan PowerShell (Publik):

```powershell
# Mengambil daftar artikel terbit
Invoke-RestMethod -Uri "http://localhost:8001/api/artikel?search=sastra" -Method Get

# Mengirimkan karya sastra baru
$body = @{
    judul = "Sajak Hujan di Sudut Kampus"
    jenis = "puisi"
    isi = "Di antara aroma tanah basah dan desir angin sore, kita menenun kata yang tak pernah lekang oleh waktu dan jarak."
    nama_penulis = "Ahmad Syarif"
    email = "ahmad.syarif@student.univ.ac.id"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/kiriman" -Method Post -Body $body -ContentType "application/json"

# Melacak kiriman karya
Invoke-RestMethod -Uri "http://localhost:8001/api/kiriman/AKS-2026-00001?email=sekar.ayu@student.univ.ac.id" -Method Get

# Merevisi naskah (status perlu_revisi)
$revisiBody = @{
    email = "aditya.n@student.univ.ac.id"
    judul = "Melangkah Menembus Kabut Pagi (Revisi)"
    isi = "Langkah kaki Bayu bergegas menembus kabut tebal yang menyelimuti lereng bukit kampus pagi itu dengan tekad yang bulat dan naskah yang telah diperbaiki secara utuh."
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/kiriman/AKS-2026-00005" -Method Put -Body $revisiBody -ContentType "application/json"
```

---

### B. Endpoint Khusus Redaksi (`/api/admin/*`)

Wajib menyertakan Header `X-Admin-Token: redaksi-rahasia-123`.

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/admin/artikel` | Daftar artikel redaksi (filter: `?status=draft\|terbit`, `?search=`) |
| `POST` | `/api/admin/artikel` | Menerbitkan atau membuat draf artikel baru |
| `GET` | `/api/admin/artikel/{id}` | Detail artikel redaksi |
| `PUT` | `/api/admin/artikel/{id}` | Memperbarui artikel (jika diubah ke terbit, otomatis terisi waktu terbit) |
| `DELETE` | `/api/admin/artikel/{id}` | Menghapus artikel |
| `GET` | `/api/admin/kategori` | Daftar kategori |
| `POST` | `/api/admin/kategori` | Tambah kategori |
| `DELETE` | `/api/admin/kategori/{id}` | Hapus kategori (ditolak 422 jika masih dipakai artikel) |
| `GET` | `/api/admin/karya` | Daftar naskah karya yang masuk (filter: `?status=`, `?search=`) |
| `GET` | `/api/admin/karya/{id}` | Detail naskah karya beserta histori telaah redaksi |
| `PATCH` | `/api/admin/karya/{id}/status` | Mengubah status karya (catatan wajib jika `perlu_revisi` / `ditolak`) |
| `DELETE` | `/api/admin/karya/{id}` | Hapus karya (hanya diperbolehkan untuk status `dikirim` atau `ditolak`) |
| `CRUD` | `/api/admin/agenda` | Kelola agenda kegiatan sastra |

#### Contoh Pemanggilan PowerShell (Redaksi):

```powershell
$headers = @{
    "X-Admin-Token" = "redaksi-rahasia-123"
}

# Melihat daftar naskah kiriman
Invoke-RestMethod -Uri "http://localhost:8001/api/admin/karya?status=dikirim" -Headers $headers -Method Get

# Mengubah status karya menjadi ditinjau
$patchBody = @{
    status = "ditinjau"
    catatan = "Naskah telah masuk antrean kurasi tim sastra."
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/api/admin/karya/6/status" -Headers $headers -Method Patch -Body $patchBody -ContentType "application/json"
```
