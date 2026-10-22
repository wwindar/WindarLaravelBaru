<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LayananResource;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LayananController extends Controller
{
    /**
     * Display a listing of active services.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Layanan::where('aktif', true);

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

        $layanan = $query->orderBy('id', 'asc')->get();

        return LayananResource::collection($layanan)->additional([
            'meta' => [
                'total' => $layanan->count(),
            ],
        ]);
    }

    /**
     * Display the specified service by slug.
     */
    public function show(string $slug): LayananResource|JsonResponse
    {
        $layanan = Layanan::where('slug', $slug)->first();

        if (! $layanan) {
            return response()->json([
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        return new LayananResource($layanan);
    }
}
