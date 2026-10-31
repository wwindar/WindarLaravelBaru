<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusKarya;
use App\Http\Controllers\Controller;
use App\Http\Requests\KirimKaryaRequest;
use App\Http\Requests\RevisiKaryaRequest;
use App\Http\Resources\KaryaResource;
use App\Models\Karya;
use App\Models\RiwayatKarya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KirimanController extends Controller
{
    /**
     * Submit a new literary work for editorial review.
     */
    public function store(KirimKaryaRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = StatusKarya::DIKIRIM;
        $validated['slug'] = Karya::generateUniqueSlug($validated['judul']);

        $karya = DB::transaction(function () use ($validated): Karya {
            $karya = Karya::create($validated);

            if (empty($karya->nomor_kiriman)) {
                $karya->nomor_kiriman = Karya::generateNomorKiriman($karya->id, $karya->created_at);
                $karya->saveQuietly();
            }

            RiwayatKarya::create([
                'karya_id' => $karya->id,
                'status' => StatusKarya::DIKIRIM,
                'catatan' => 'Karya berhasil dikirimkan ke redaksi dan menunggu peninjauan.',
            ]);

            return $karya;
        });

        $karya->load('riwayat');

        return (new KaryaResource($karya))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Track a submitted literary work by nomor_kiriman and matching author email.
     */
    public function show(Request $request, string $nomor_kiriman): JsonResponse|KaryaResource
    {
        $email = trim((string) $request->query('email', ''));

        if ($email === '') {
            return response()->json([
                'message' => 'Data kiriman karya tidak ditemukan.',
            ], 404);
        }

        $karya = Karya::where('nomor_kiriman', $nomor_kiriman)
            ->where('email', $email)
            ->with('riwayat')
            ->first();

        if (! $karya) {
            return response()->json([
                'message' => 'Data kiriman karya tidak ditemukan.',
            ], 404);
        }

        return new KaryaResource($karya);
    }

    /**
     * Revise a literary work by the author (only when status is perlu_revisi).
     */
    public function update(RevisiKaryaRequest $request, string $nomor_kiriman): JsonResponse|KaryaResource
    {
        $email = trim((string) $request->input('email', ''));

        $karya = Karya::where('nomor_kiriman', $nomor_kiriman)
            ->where('email', $email)
            ->with('riwayat')
            ->first();

        if (! $karya) {
            return response()->json([
                'message' => 'Data kiriman karya tidak ditemukan.',
            ], 404);
        }

        if ($karya->status !== StatusKarya::PERLU_REVISI) {
            return response()->json([
                'message' => 'Karya hanya dapat direvisi jika berstatus Perlu Revisi.',
            ], 422);
        }

        DB::transaction(function () use ($karya, $request): void {
            if ($request->filled('judul')) {
                $karya->judul = (string) $request->input('judul');
                $karya->slug = Karya::generateUniqueSlug($karya->judul, $karya->id);
            }

            if ($request->filled('isi')) {
                $karya->isi = (string) $request->input('isi');
            }

            $karya->status = StatusKarya::DIKIRIM;
            $karya->catatan_redaksi = null;
            $karya->save();

            RiwayatKarya::create([
                'karya_id' => $karya->id,
                'status' => StatusKarya::DIKIRIM,
                'catatan' => 'Penulis telah memperbarui dan mengirimkan revisi naskah karya.',
            ]);
        });

        $karya->load('riwayat');

        return new KaryaResource($karya);
    }

    /**
     * Get list of works submitted by the logged in student/writer.
     */
    public function karyaSaya(Request $request): JsonResponse
    {
        $user = $request->user();
        $karya = Karya::with('riwayat')
            ->where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => KaryaResource::collection($karya),
        ]);
    }
}
