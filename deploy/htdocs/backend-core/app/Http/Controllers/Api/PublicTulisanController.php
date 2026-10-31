<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusTulisan;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanTulisanRequest;
use App\Http\Resources\TulisanResource;
use App\Models\LaporanTulisan;
use App\Models\Tulisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicTulisanController extends Controller
{
    /**
     * Daftar tulisan terbit dengan filter dan pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Tulisan::with(['user', 'kategori'])
            ->where('status', StatusTulisan::Terbit)
            ->orderBy('diterbitkan_pada', 'desc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request): void {
                $q->where('judul', 'like', '%'.$request->search.'%')
                    ->orWhere('isi', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('kategori')) {
            $query->whereHas('kategori', fn ($q) => $q->where('slug', $request->kategori));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('penulis')) {
            $query->whereHas('user', fn ($q) => $q->where('username', $request->penulis));
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('diterbitkan_pada', [$request->dari, $request->sampai]);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        return TulisanResource::collection($query->paginate($perPage));
    }

    /**
     * Detail satu tulisan terbit berdasarkan slug.
     */
    public function show(string $slug): TulisanResource
    {
        $tulisan = Tulisan::with(['user', 'kategori'])
            ->where('slug', $slug)
            ->where('status', StatusTulisan::Terbit)
            ->firstOrFail();

        return new TulisanResource($tulisan);
    }

    /**
     * Terima laporan pelanggaran / plagiarisme dari publik.
     */
    public function lapor(LaporanTulisanRequest $request, int $id): JsonResponse
    {
        $tulisan = Tulisan::where('id', $id)
            ->where('status', StatusTulisan::Terbit)
            ->firstOrFail();

        LaporanTulisan::create([
            'tulisan_id' => $tulisan->id,
            'nama_pelapor' => $request->nama_pelapor,
            'email_pelapor' => $request->email_pelapor,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim. Terima kasih atas kontribusi Anda.',
        ], 201);
    }
}
