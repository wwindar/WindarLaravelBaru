<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreKategoriRequest;
use App\Http\Requests\AdminUpdateKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Artikel;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminKategoriController extends Controller
{
    /**
     * Display a listing of categories for editorial staff.
     */
    public function index(): AnonymousResourceCollection
    {
        $kategori = Kategori::withCount('artikel')->orderBy('nama', 'asc')->get();

        return KategoriResource::collection($kategori);
    }

    /**
     * Store a newly created category.
     */
    public function store(AdminStoreKategoriRequest $request): JsonResponse
    {
        $nama = $request->input('nama');
        $slug = Kategori::generateUniqueSlug($nama);

        $kategori = Kategori::create([
            'nama' => $nama,
            'slug' => $slug,
        ]);

        return (new KategoriResource($kategori))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified category.
     */
    public function show(int|string $id): KategoriResource|JsonResponse
    {
        $kategori = Kategori::find($id);

        if (! $kategori) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        return new KategoriResource($kategori);
    }

    /**
     * Update the specified category.
     */
    public function update(AdminUpdateKategoriRequest $request, int|string $id): KategoriResource|JsonResponse
    {
        $kategori = Kategori::find($id);

        if (! $kategori) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $nama = $request->input('nama');
        $data = ['nama' => $nama];

        if ($nama !== $kategori->nama) {
            $data['slug'] = Kategori::generateUniqueSlug($nama, $kategori->id);
        }

        $kategori->update($data);

        return new KategoriResource($kategori);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $kategori = Kategori::find($id);

        if (! $kategori) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        if (Artikel::where('kategori_id', $kategori->id)->exists()) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel.',
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.',
        ], 200);
    }
}
