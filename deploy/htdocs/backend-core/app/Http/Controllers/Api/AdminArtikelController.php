<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreArtikelRequest;
use App\Http\Requests\AdminUpdateArtikelRequest;
use App\Http\Resources\ArtikelResource;
use App\Models\Artikel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminArtikelController extends Controller
{
    /**
     * Display a listing of articles for editorial staff.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Artikel::with('kategori')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(penulis) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $artikel = $query->paginate($perPage);

        return ArtikelResource::collection($artikel);
    }

    /**
     * Store a newly created article.
     */
    public function store(AdminStoreArtikelRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Artikel::generateUniqueSlug($data['judul']);

        if ($data['status'] === 'terbit' && empty($data['diterbitkan_pada'])) {
            $data['diterbitkan_pada'] = Carbon::now();
        }

        $artikel = Artikel::create($data);
        $artikel->load('kategori');

        return (new ArtikelResource($artikel))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified article.
     */
    public function show(int|string $id): ArtikelResource|JsonResponse
    {
        $artikel = Artikel::with('kategori')->find($id);

        if (! $artikel) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan.',
            ], 404);
        }

        return new ArtikelResource($artikel);
    }

    /**
     * Update the specified article.
     */
    public function update(AdminUpdateArtikelRequest $request, int|string $id): ArtikelResource|JsonResponse
    {
        $artikel = Artikel::find($id);

        if (! $artikel) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan.',
            ], 404);
        }

        $data = $request->validated();

        if ($request->filled('judul') && $request->input('judul') !== $artikel->judul) {
            $data['slug'] = Artikel::generateUniqueSlug($request->input('judul'), $artikel->id);
        }

        $newStatus = $request->input('status', $artikel->status);
        if ($newStatus === 'terbit' && empty($artikel->diterbitkan_pada) && empty($data['diterbitkan_pada'])) {
            $data['diterbitkan_pada'] = Carbon::now();
        }

        $artikel->update($data);
        $artikel->load('kategori');

        return new ArtikelResource($artikel);
    }

    /**
     * Remove the specified article.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $artikel = Artikel::find($id);

        if (! $artikel) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan.',
            ], 404);
        }

        $artikel->delete();

        return response()->json([
            'message' => 'Artikel berhasil dihapus.',
        ], 200);
    }
}
