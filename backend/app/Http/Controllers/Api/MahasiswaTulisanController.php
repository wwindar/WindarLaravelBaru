<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusTulisan;
use App\Http\Controllers\Controller;
use App\Http\Requests\TulisanRequest;
use App\Http\Resources\TulisanResource;
use App\Models\Tulisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class MahasiswaTulisanController extends Controller
{
    /**
     * Daftar tulisan milik mahasiswa yang sedang login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $tulisan = $request->user()
            ->tulisan()
            ->with(['kategori'])
            ->latest()
            ->paginate(15);

        return TulisanResource::collection($tulisan);
    }

    /**
     * Buat tulisan baru (default status: draf).
     */
    public function store(TulisanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tulisan = $request->user()->tulisan()->create([
            'kategori_id' => $validated['kategori_id'] ?? null,
            'jenis' => $validated['jenis'],
            'slug' => $this->generateSlug($validated['judul']),
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'ringkasan' => $validated['ringkasan'] ?? null,
            'status' => StatusTulisan::Draf,
        ]);

        $tulisan->load(['user', 'kategori']);

        return response()->json([
            'message' => 'Tulisan berhasil disimpan sebagai draf.',
            'data' => new TulisanResource($tulisan),
        ], 201);
    }

    /**
     * Detail satu tulisan milik mahasiswa.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $tulisan = $request->user()
            ->tulisan()
            ->with(['user', 'kategori', 'riwayatStatus'])
            ->findOrFail($id);

        return response()->json(['data' => new TulisanResource($tulisan)]);
    }

    /**
     * Update tulisan — hanya diizinkan saat status draf atau perlu_revisi.
     */
    public function update(TulisanRequest $request, int $id): JsonResponse
    {
        $tulisan = $request->user()->tulisan()->findOrFail($id);

        if (! $tulisan->status->bisaDiedit()) {
            return response()->json([
                'message' => 'Tulisan hanya bisa diedit saat berstatus Draf atau Perlu Revisi.',
            ], 422);
        }

        $validated = $request->validated();

        $tulisan->update([
            'kategori_id' => $validated['kategori_id'] ?? $tulisan->kategori_id,
            'jenis' => $validated['jenis'],
            'judul' => $validated['judul'],
            'slug' => $this->generateSlug($validated['judul'], $tulisan->id),
            'isi' => $validated['isi'],
            'ringkasan' => $validated['ringkasan'] ?? $tulisan->ringkasan,
        ]);

        $tulisan->load(['user', 'kategori']);

        return response()->json([
            'message' => 'Tulisan berhasil diperbarui.',
            'data' => new TulisanResource($tulisan),
        ]);
    }

    /**
     * Hapus tulisan — hanya bisa dihapus saat status draf.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tulisan = $request->user()->tulisan()->findOrFail($id);

        if ($tulisan->status !== StatusTulisan::Draf) {
            return response()->json([
                'message' => 'Tulisan hanya bisa dihapus saat berstatus Draf.',
            ], 422);
        }

        $tulisan->delete();

        return response()->json(['message' => 'Tulisan berhasil dihapus.']);
    }

    /**
     * Ajukan tulisan ke dosen untuk disetujui.
     */
    public function ajukan(Request $request, int $id): JsonResponse
    {
        $tulisan = $request->user()->tulisan()->findOrFail($id);

        if (! in_array($tulisan->status, [StatusTulisan::Draf, StatusTulisan::PerluRevisi], strict: true)) {
            return response()->json([
                'message' => 'Hanya tulisan berstatus Draf atau Perlu Revisi yang bisa diajukan.',
            ], 422);
        }

        $tulisan->update(['status' => StatusTulisan::Menunggu, 'catatan_editor' => null]);

        $tulisan->riwayatStatus()->create([
            'status' => StatusTulisan::Menunggu,
            'catatan' => 'Diajukan oleh penulis untuk persetujuan.',
        ]);

        $tulisan->load(['user', 'kategori']);

        return response()->json([
            'message' => 'Tulisan berhasil diajukan dan sedang menunggu persetujuan dosen.',
            'data' => new TulisanResource($tulisan),
        ]);
    }

    /** Generate slug unik dari judul. */
    private function generateSlug(string $judul, ?int $exceptId = null): string
    {
        $base = Str::slug($judul);
        $slug = $base;
        $counter = 1;

        while (Tulisan::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
