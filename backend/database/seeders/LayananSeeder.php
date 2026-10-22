<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layananList = [
            [
                'slug' => 'surat-aktif-kuliah',
                'nama' => 'Surat Aktif Kuliah',
                'deskripsi' => 'Surat keterangan resmi yang menyatakan bahwa mahasiswa berstatus aktif mengikuti perkuliahan pada semester berjalan, digunakan untuk tunjangan anak, BPJS, atau keperluan kedinasan.',
                'persyaratan' => [
                    'Kartu Tanda Mahasiswa (KTM)',
                    'Bukti Pembayaran UKT Semester Terakhir',
                    'Kartu Rencana Studi (KRS)',
                ],
                'estimasi_hari' => 1,
                'aktif' => true,
            ],
            [
                'slug' => 'surat-pengantar-penelitian',
                'nama' => 'Surat Pengantar Penelitian',
                'deskripsi' => 'Surat permohonan izin riset atau penelitian skripsi/tugas akhir kepada instansi, perusahaan, atau laboratorium tujuan.',
                'persyaratan' => [
                    'Proposal Penelitian Singkat',
                    'Lembar Persetujuan Dosen Pembimbing',
                    'KRS Semester Berjalan',
                ],
                'estimasi_hari' => 3,
                'aktif' => true,
            ],
            [
                'slug' => 'legalisasi-dokumen',
                'nama' => 'Legalisasi Dokumen',
                'deskripsi' => 'Pengesahan dan legalisir salinan ijazah, transkrip akademik, atau surat keterangan kelulusan resmi universitas.',
                'persyaratan' => [
                    'Scan Ijazah / Transkrip Asli',
                    'Bukti Pembayaran Retribusi Legalisasi',
                    'KTM / Identitas Diri',
                ],
                'estimasi_hari' => 2,
                'aktif' => true,
            ],
            [
                'slug' => 'surat-rekomendasi-beasiswa',
                'nama' => 'Surat Rekomendasi Beasiswa',
                'deskripsi' => 'Surat rekomendasi resmi dari pimpinan fakultas atau universitas untuk keperluan pendaftaran beasiswa internal maupun eksternal.',
                'persyaratan' => [
                    'Transkrip Nilai Kumulatif',
                    'Surat Pernyataan Bebas Beasiswa Lain',
                    'Curriculum Vitae (CV)',
                    'KTM',
                ],
                'estimasi_hari' => 3,
                'aktif' => true,
            ],
            [
                'slug' => 'surat-keterangan-kelakuan-baik',
                'nama' => 'Surat Keterangan Kelakuan Baik',
                'deskripsi' => 'Surat keterangan resmi yang menerangkan bahwa mahasiswa tidak sedang dalam masa sanksi akademik atau pelanggaran tata tertib kampus.',
                'persyaratan' => [
                    'KTM Aktif',
                    'Surat Pernyataan Bebas Sanksi dan Narkoba',
                    'KRS Semester Berjalan',
                ],
                'estimasi_hari' => 2,
                'aktif' => true,
            ],
            [
                'slug' => 'pengajuan-cuti-akademik',
                'nama' => 'Pengajuan Cuti Akademik',
                'deskripsi' => 'Layanan permohonan penghentian studi sementara bagi mahasiswa aktif yang memenuhi syarat akademik.',
                'persyaratan' => [
                    'Formulir Pengajuan Cuti',
                    'Surat Persetujuan Orang Tua / Wali',
                    'Surat Bebas Pustaka Perpustakaan',
                    'Transkrip Nilai Terakhir',
                ],
                'estimasi_hari' => 5,
                'aktif' => true,
            ],
        ];

        foreach ($layananList as $item) {
            Layanan::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
