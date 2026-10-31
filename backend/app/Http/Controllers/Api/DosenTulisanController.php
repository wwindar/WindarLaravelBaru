<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusTulisan;
use App\Http\Controllers\Controller;
use App\Http\Resources\LaporanTulisanResource;
use App\Http\Resources\TulisanResource;
use App\Models\LaporanTulisan;
use App\Models\Notifikasi;
use App\Models\Tulisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DosenTulisanController extends Controller
{
    /**
     * Daftar tulisan yang menunggu persetujuan (antrean).
     */
    public function antrean(): AnonymousResourceCollection
    {
        $tulisan = Tulisan::with(['user', 'kategori'])
            ->where('status', StatusTulisan::Menunggu)
            ->oldest()
            ->paginate(20);

        return TulisanResource::collection($tulisan);
    }

    /**
     * Setujui tulisan — ubah status menjadi terbit.
     */
    public function setujui(int $id): JsonResponse
    {
        $tulisan = Tulisan::with('user')->findOrFail($id);

        if ($tulisan->status !== StatusTulisan::Menunggu) {
            return response()->json([
                'message' => 'Hanya tulisan berstatus Menunggu yang bisa disetujui.',
            ], 422);
        }

        $tulisan->update([
            'status' => StatusTulisan::Terbit,
            'diterbitkan_pada' => now(),
            'catatan_editor' => null,
        ]);

        $tulisan->riwayatStatus()->create([
            'status' => StatusTulisan::Terbit,
            'catatan' => 'Disetujui oleh dosen/editor.',
        ]);

        $this->kirimNotifikasi(
            userId: $tulisan->user_id,
            judul: 'Tulisan Anda Disetujui!',
            pesan: "Selamat! Tulisan \"{$tulisan->judul}\" telah disetujui dan kini sudah terbit.",
        );

        return response()->json([
            'message' => 'Tulisan berhasil disetujui dan diterbitkan.',
            'data' => new TulisanResource($tulisan->load('kategori')),
        ]);
    }

    /**
     * Tolak tulisan — catatan wajib diisi.
     */
    public function tolak(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'catatan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $tulisan = Tulisan::with('user')->findOrFail($id);

        if ($tulisan->status !== StatusTulisan::Menunggu) {
            return response()->json([
                'message' => 'Hanya tulisan berstatus Menunggu yang bisa ditolak.',
            ], 422);
        }

        $tulisan->update([
            'status' => StatusTulisan::Ditolak,
            'catatan_editor' => $validated['catatan'],
        ]);

        $tulisan->riwayatStatus()->create([
            'status' => StatusTulisan::Ditolak,
            'catatan' => $validated['catatan'],
        ]);

        $this->kirimNotifikasi(
            userId: $tulisan->user_id,
            judul: 'Tulisan Anda Ditolak',
            pesan: "Tulisan \"{$tulisan->judul}\" ditolak. Catatan editor: {$validated['catatan']}",
        );

        return response()->json([
            'message' => 'Tulisan berhasil ditolak.',
            'data' => new TulisanResource($tulisan->load('kategori')),
        ]);
    }

    /**
     * Minta revisi — catatan wajib diisi.
     */
    public function revisi(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'catatan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi.',
        ]);

        $tulisan = Tulisan::with('user')->findOrFail($id);

        if ($tulisan->status !== StatusTulisan::Menunggu) {
            return response()->json([
                'message' => 'Hanya tulisan berstatus Menunggu yang bisa diminta revisi.',
            ], 422);
        }

        $tulisan->update([
            'status' => StatusTulisan::PerluRevisi,
            'catatan_editor' => $validated['catatan'],
        ]);

        $tulisan->riwayatStatus()->create([
            'status' => StatusTulisan::PerluRevisi,
            'catatan' => $validated['catatan'],
        ]);

        $this->kirimNotifikasi(
            userId: $tulisan->user_id,
            judul: 'Tulisan Anda Perlu Direvisi',
            pesan: "Tulisan \"{$tulisan->judul}\" perlu direvisi. Catatan editor: {$validated['catatan']}",
        );

        return response()->json([
            'message' => 'Tulisan dikembalikan ke penulis untuk direvisi.',
            'data' => new TulisanResource($tulisan->load('kategori')),
        ]);
    }

    /**
     * Daftar laporan tulisan yang masuk.
     */
    public function laporan(Request $request): AnonymousResourceCollection
    {
        $laporan = LaporanTulisan::with('tulisan')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return LaporanTulisanResource::collection($laporan);
    }

    /**
     * Update status laporan (ditinjau / selesai).
     */
    public function updateStatusLaporan(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:menunggu,ditinjau,selesai'],
        ], [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.in' => 'Status laporan tidak valid.',
        ]);

        $laporan = LaporanTulisan::findOrFail($id);
        $laporan->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui.',
            'data' => new LaporanTulisanResource($laporan->load('tulisan')),
        ]);
    }

    /** Simpan notifikasi in-app untuk user. */
    private function kirimNotifikasi(int $userId, string $judul, string $pesan): void
    {
        Notifikasi::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'dibaca' => false,
        ]);
    }
}
