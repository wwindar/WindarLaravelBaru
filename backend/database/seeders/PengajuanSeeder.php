<?php

namespace Database\Seeders;

use App\Enums\StatusPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Database\Seeder;

class PengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layananMap = Layanan::pluck('id', 'slug')->all();

        $pengajuanSimulasi = [
            [
                'layanan_slug' => 'surat-aktif-kuliah',
                'nama_pemohon' => 'Ahmad Rizky Pratama',
                'nim' => '220101001',
                'email' => 'ahmad.rizky@student.univ.ac.id',
                'program_studi' => 'Teknik Informatika',
                'keperluan' => 'Pengajuan tunjangan anak instansi dinas orang tua.',
                'dokumen' => [
                    'Kartu Tanda Mahasiswa (KTM)',
                    'Bukti Pembayaran UKT Semester Terakhir',
                    'Kartu Rencana Studi (KRS)',
                ],
                'status' => StatusPengajuan::DIAJUKAN,
                'catatan_petugas' => null,
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'surat-pengantar-penelitian',
                'nama_pemohon' => 'Siti Nurhaliza',
                'nim' => '220102015',
                'email' => 'siti.nurhaliza@student.univ.ac.id',
                'program_studi' => 'Sistem Informasi',
                'keperluan' => 'Permohonan izin pengambilan data penelitian skripsi di Diskominfo.',
                'dokumen' => [
                    'Proposal Penelitian Singkat',
                    'Lembar Persetujuan Dosen Pembimbing',
                    'KRS Semester Berjalan',
                ],
                'status' => StatusPengajuan::DIPERIKSA,
                'catatan_petugas' => 'Berkas pengajuan sedang diperiksa oleh staf administrasi akademik.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Berkas pengajuan sedang diperiksa oleh staf administrasi akademik.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'legalisasi-dokumen',
                'nama_pemohon' => 'Budi Santoso',
                'nim' => '210201042',
                'email' => 'budi.santoso@student.univ.ac.id',
                'program_studi' => 'Teknik Elektro',
                'keperluan' => 'Kelengkapan berkas administrasi rekrutmen BUMN.',
                'dokumen' => [
                    'Scan Ijazah / Transkrip Asli',
                    'Bukti Pembayaran Retribusi Legalisasi',
                    'KTM / Identitas Diri',
                ],
                'status' => StatusPengajuan::PERLU_PERBAIKAN,
                'catatan_petugas' => 'Scan Ijazah Asli terpotong pada bagian nomor seri ijazah dan cap universitas. Harap unggah ulang dokumen scan yang utuh dan jelas.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Pemeriksaan berkas legalisir.',
                    ],
                    [
                        'status' => StatusPengajuan::PERLU_PERBAIKAN,
                        'catatan' => 'Scan Ijazah Asli terpotong pada bagian nomor seri ijazah dan cap universitas. Harap unggah ulang dokumen scan yang utuh dan jelas.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'surat-rekomendasi-beasiswa',
                'nama_pemohon' => 'Dewi Anggraini',
                'nim' => '230301019',
                'email' => 'dewi.anggraini@student.univ.ac.id',
                'program_studi' => 'Manajemen',
                'keperluan' => 'Pendaftaran program Beasiswa Prestasi Unggulan 2026.',
                'dokumen' => [
                    'Transkrip Nilai Kumulatif',
                    'Surat Pernyataan Bebas Beasiswa Lain',
                    'Curriculum Vitae (CV)',
                    'KTM',
                ],
                'status' => StatusPengajuan::DISETUJUI,
                'catatan_petugas' => 'Pengajuan disetujui, surat sedang dalam proses penandatanganan Dekan.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Verifikasi syarat indeks prestasi kumulatif memenuhi ketentuan.',
                    ],
                    [
                        'status' => StatusPengajuan::DISETUJUI,
                        'catatan' => 'Pengajuan disetujui, surat sedang dalam proses penandatanganan Dekan.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'surat-aktif-kuliah',
                'nama_pemohon' => 'Fajar Prasetyo',
                'nim' => '200101088',
                'email' => 'fajar.prasetyo@student.univ.ac.id',
                'program_studi' => 'Teknik Informatika',
                'keperluan' => 'Perpanjangan BPJS Kesehatan keluarga tahun berjalan.',
                'dokumen' => [
                    'Kartu Tanda Mahasiswa (KTM)',
                    'Bukti Pembayaran UKT Semester Terakhir',
                    'Kartu Rencana Studi (KRS)',
                ],
                'status' => StatusPengajuan::SELESAI,
                'catatan_petugas' => 'Surat aktif kuliah telah diterbitkan dan dapat diunduh atau diambil di loket akademik.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Pemeriksaan status pembayaran UKT dan KRS aktif.',
                    ],
                    [
                        'status' => StatusPengajuan::DISETUJUI,
                        'catatan' => 'Surat aktif kuliah disetujui dan ditandatangani secara digital.',
                    ],
                    [
                        'status' => StatusPengajuan::SELESAI,
                        'catatan' => 'Surat aktif kuliah telah diterbitkan dan dapat diunduh atau diambil di loket akademik.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'surat-keterangan-kelakuan-baik',
                'nama_pemohon' => 'Rina Kartika',
                'nim' => '210401033',
                'email' => 'rina.kartika@student.univ.ac.id',
                'program_studi' => 'Akuntansi',
                'keperluan' => 'Persyaratan seleksi program magang internasional.',
                'dokumen' => [
                    'KTM Aktif',
                    'Surat Pernyataan Bebas Sanksi dan Narkoba',
                    'KRS Semester Berjalan',
                ],
                'status' => StatusPengajuan::DITOLAK,
                'catatan_petugas' => 'Terdapat catatan pelanggaran disiplin akademik semester lalu yang belum diselesaikan melalui Komisi Disiplin Mahasiswa.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Pengecekan riwayat catatan pelanggaran mahasiswa.',
                    ],
                    [
                        'status' => StatusPengajuan::DITOLAK,
                        'catatan' => 'Terdapat catatan pelanggaran disiplin akademik semester lalu yang belum diselesaikan melalui Komisi Disiplin Mahasiswa.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'pengajuan-cuti-akademik',
                'nama_pemohon' => 'Gilang Ramadhan',
                'nim' => '220501007',
                'email' => 'gilang.ramadhan@student.univ.ac.id',
                'program_studi' => 'Ilmu Komunikasi',
                'keperluan' => 'Cuti akademik 1 semester untuk menjalani pengobatan rawat jalan intensif.',
                'dokumen' => [
                    'Formulir Pengajuan Cuti',
                    'Surat Persetujuan Orang Tua / Wali',
                    'Surat Bebas Pustaka Perpustakaan',
                    'Transkrip Nilai Terakhir',
                ],
                'status' => StatusPengajuan::DIPERIKSA,
                'catatan_petugas' => 'Menunggu verifikasi rekomendasi dokter dari klinik universitas.',
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                    [
                        'status' => StatusPengajuan::DIPERIKSA,
                        'catatan' => 'Menunggu verifikasi rekomendasi dokter dari klinik universitas.',
                    ],
                ],
            ],
            [
                'layanan_slug' => 'surat-pengantar-penelitian',
                'nama_pemohon' => 'Hendra Wijaya',
                'nim' => '230101112',
                'email' => 'hendra.wijaya@student.univ.ac.id',
                'program_studi' => 'Teknik Sipil',
                'keperluan' => 'Permohonan uji sampel agregat di Balai Pengujian Material Provinsi.',
                'dokumen' => [
                    'Proposal Penelitian Singkat',
                    'Lembar Persetujuan Dosen Pembimbing',
                    'KRS Semester Berjalan',
                ],
                'status' => StatusPengajuan::DIAJUKAN,
                'catatan_petugas' => null,
                'riwayat' => [
                    [
                        'status' => StatusPengajuan::DIAJUKAN,
                        'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
                    ],
                ],
            ],
        ];

        foreach ($pengajuanSimulasi as $data) {
            $layananId = $layananMap[$data['layanan_slug']] ?? Layanan::first()->id;

            $pengajuan = Pengajuan::create([
                'layanan_id' => $layananId,
                'nama_pemohon' => $data['nama_pemohon'],
                'nim' => $data['nim'],
                'email' => $data['email'],
                'program_studi' => $data['program_studi'],
                'keperluan' => $data['keperluan'],
                'dokumen' => $data['dokumen'],
                'status' => $data['status'],
                'catatan_petugas' => $data['catatan_petugas'],
            ]);

            if (empty($pengajuan->nomor_pelacakan)) {
                $pengajuan->nomor_pelacakan = Pengajuan::generateNomorPelacakan($pengajuan->id, $pengajuan->created_at);
                $pengajuan->saveQuietly();
            }

            foreach ($data['riwayat'] as $riwayatItem) {
                RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'status' => $riwayatItem['status'],
                    'catatan' => $riwayatItem['catatan'],
                ]);
            }
        }
    }
}
