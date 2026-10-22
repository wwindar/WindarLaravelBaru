<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengajuanRequest;
use App\Http\Requests\UpdatePerbaikanPengajuanRequest;
use App\Http\Resources\PengajuanResource;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Store a newly created application.
     */
    public function store(StorePengajuanRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = StatusPengajuan::DIAJUKAN;

        $pengajuan = DB::transaction(function () use ($validated): Pengajuan {
            $pengajuan = Pengajuan::create($validated);

            if (empty($pengajuan->nomor_pelacakan)) {
                $pengajuan->nomor_pelacakan = Pengajuan::generateNomorPelacakan($pengajuan->id, $pengajuan->created_at);
                $pengajuan->saveQuietly();
            }

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => StatusPengajuan::DIAJUKAN,
                'catatan' => 'Pengajuan berhasil dibuat oleh pemohon.',
            ]);

            return $pengajuan;
        });

        $pengajuan->load(['layanan', 'riwayat']);

        return (new PengajuanResource($pengajuan))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Track application by tracking number and matching NIM.
     */
    public function show(Request $request, string $nomor_pelacakan): JsonResponse|PengajuanResource
    {
        $nim = trim((string) $request->query('nim', ''));

        if ($nim === '') {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $pengajuan = Pengajuan::where('nomor_pelacakan', $nomor_pelacakan)
            ->where('nim', $nim)
            ->with(['layanan', 'riwayat'])
            ->first();

        if (! $pengajuan) {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        return new PengajuanResource($pengajuan);
    }

    /**
     * Revise an application by student (only when status is perlu_perbaikan).
     */
    public function update(UpdatePerbaikanPengajuanRequest $request, string $nomor_pelacakan): JsonResponse|PengajuanResource
    {
        $nim = trim((string) $request->input('nim', ''));

        $pengajuan = Pengajuan::where('nomor_pelacakan', $nomor_pelacakan)
            ->where('nim', $nim)
            ->with(['layanan', 'riwayat'])
            ->first();

        if (! $pengajuan) {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        if ($pengajuan->status !== StatusPengajuan::PERLU_PERBAIKAN) {
            return response()->json([
                'message' => 'Pengajuan hanya dapat diperbaiki jika berstatus Perlu Perbaikan.',
            ], 422);
        }

        // Validate that all required documents from the service are present
        $persyaratanWajib = $pengajuan->layanan?->persyaratan ?? [];
        $dokumenSubmitted = array_map(fn ($doc) => trim((string) $doc), (array) $request->input('dokumen', []));

        $missing = [];
        foreach ($persyaratanWajib as $syarat) {
            $found = false;
            foreach ($dokumenSubmitted as $doc) {
                if (strcasecmp(trim($doc), trim($syarat)) === 0) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                $missing[] = $syarat;
            }
        }

        if (! empty($missing)) {
            return response()->json([
                'message' => 'Validasi perbaikan pengajuan gagal. Silakan periksa kembali data yang Anda kirimkan.',
                'errors' => [
                    'dokumen' => [
                        'Dokumen persyaratan belum lengkap. Dokumen yang belum dilampirkan: '.implode(', ', $missing).'.',
                    ],
                ],
            ], 422);
        }

        DB::transaction(function () use ($pengajuan, $request): void {
            $pengajuan->keperluan = $request->input('keperluan');
            $pengajuan->email = $request->input('email');
            $pengajuan->program_studi = $request->input('program_studi');
            $pengajuan->dokumen = $request->input('dokumen');
            $pengajuan->status = StatusPengajuan::DIAJUKAN;
            $pengajuan->catatan_petugas = null;
            $pengajuan->save();

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => StatusPengajuan::DIAJUKAN,
                'catatan' => 'Perbaikan data dan kelengkapan dokumen telah dikirimkan oleh pemohon.',
            ]);
        });

        $pengajuan->load(['layanan', 'riwayat']);

        return new PengajuanResource($pengajuan);
    }
}
