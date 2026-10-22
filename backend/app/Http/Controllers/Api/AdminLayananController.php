<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLayananRequest;
use App\Http\Requests\UpdateLayananRequest;
use App\Http\Resources\LayananResource;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminLayananController extends Controller
{
    /**
     * Display a listing of all services (including inactive ones) with search and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Layanan::query();

        if ($request->has('search')) {
            $search = trim((string) $request->query('search', ''));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(nama) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(deskripsi) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $layanan = $query->orderBy('id', 'asc')->paginate($perPage);

        return LayananResource::collection($layanan);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreLayananRequest $request): JsonResponse
    {
        $nama = $request->input('nama');
        $slug = Layanan::generateUniqueSlug($nama);

        $layanan = Layanan::create([
            'slug' => $slug,
            'nama' => $nama,
            'deskripsi' => $request->input('deskripsi'),
            'persyaratan' => $request->input('persyaratan'),
            'estimasi_hari' => $request->input('estimasi_hari'),
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        return (new LayananResource($layanan))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified service.
     */
    public function show(int|string $id): JsonResponse|LayananResource
    {
        $layanan = Layanan::find($id);

        if (! $layanan) {
            return response()->json([
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        return new LayananResource($layanan);
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateLayananRequest $request, int|string $id): JsonResponse|LayananResource
    {
        $layanan = Layanan::find($id);

        if (! $layanan) {
            return response()->json([
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        if ($request->has('nama') && $request->input('nama') !== $layanan->nama) {
            $layanan->slug = Layanan::generateUniqueSlug($request->input('nama'), $layanan->id);
            $layanan->nama = $request->input('nama');
        }

        if ($request->has('deskripsi')) {
            $layanan->deskripsi = $request->input('deskripsi');
        }

        if ($request->has('persyaratan')) {
            $layanan->persyaratan = $request->input('persyaratan');
        }

        if ($request->has('estimasi_hari')) {
            $layanan->estimasi_hari = $request->input('estimasi_hari');
        }

        if ($request->has('aktif')) {
            $layanan->aktif = $request->boolean('aktif');
        }

        $layanan->save();

        return new LayananResource($layanan);
    }

    /**
     * Remove the specified service.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $layanan = Layanan::find($id);

        if (! $layanan) {
            return response()->json([
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        if ($layanan->pengajuan()->exists()) {
            return response()->json([
                'message' => 'Layanan tidak dapat dihapus karena sudah memiliki data pengajuan terkait. Disarankan untuk menonaktifkan layanan (ubah aktif menjadi false).',
            ], 422);
        }

        $layanan->delete();

        return response()->json([
            'message' => 'Layanan berhasil dihapus.',
        ], 200);
    }
}
