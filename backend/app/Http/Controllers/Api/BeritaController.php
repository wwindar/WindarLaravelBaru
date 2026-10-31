<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BeritaResource;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BeritaController extends Controller
{
    /**
     * Daftar berita terbit untuk publik.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Berita::with('penulis')
            ->where('status', 'terbit')
            ->orderBy('diterbitkan_pada', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 10), 30);

        return BeritaResource::collection($query->paginate($perPage));
    }

    /**
     * Detail satu berita terbit.
     */
    public function show(string $slug): JsonResponse|BeritaResource
    {
        $berita = Berita::with('penulis')
            ->where('slug', $slug)
            ->where('status', 'terbit')
            ->first();

        if (! $berita) {
            return response()->json([
                'message' => 'Berita atau pengumuman tidak ditemukan.',
            ], 404);
        }

        return new BeritaResource($berita);
    }
}

