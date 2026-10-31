<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelResource;
use App\Models\Artikel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArtikelController extends Controller
{
    /**
     * Display a listing of published articles with filters and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Artikel::with('kategori')
            ->where('status', 'terbit');

        // Filter by jenis (berita|blog)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        // Filter by category slug
        if ($request->filled('kategori')) {
            $kategoriSlug = trim((string) $request->query('kategori'));
            $query->whereHas('kategori', function ($q) use ($kategoriSlug): void {
                $q->where('slug', $kategoriSlug);
            });
        }

        // Search in judul and ringkasan (case-insensitive & trimmed)
        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(ringkasan) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        // Order by latest published date
        $query->orderByRaw('COALESCE(diterbitkan_pada, created_at) DESC');

        $perPage = (int) $request->query('per_page', 9);
        if ($perPage < 1) {
            $perPage = 9;
        }

        $artikel = $query->paginate($perPage);

        return ArtikelResource::collection($artikel);
    }

    /**
     * Display the specified published article by slug.
     */
    public function show(string $slug): ArtikelResource|JsonResponse
    {
        $artikel = Artikel::with('kategori')
            ->where('slug', $slug)
            ->first();

        if (! $artikel || $artikel->status !== 'terbit') {
            return response()->json([
                'message' => 'Artikel tidak ditemukan atau belum diterbitkan.',
            ], 404);
        }

        return new ArtikelResource($artikel);
    }
}
