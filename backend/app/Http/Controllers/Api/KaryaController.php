<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusKarya;
use App\Http\Controllers\Controller;
use App\Http\Resources\KaryaResource;
use App\Models\Karya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KaryaController extends Controller
{
    /**
     * Display a listing of published literary works.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Karya::where('status', StatusKarya::TERBIT);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(nama_penulis) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $query->orderBy('created_at', 'desc');

        $perPage = (int) $request->query('per_page', 9);
        if ($perPage < 1) {
            $perPage = 9;
        }

        $karya = $query->paginate($perPage);

        return KaryaResource::collection($karya);
    }

    /**
     * Display the specified published literary work by slug.
     */
    public function show(string $slug): KaryaResource|JsonResponse
    {
        $karya = Karya::where('slug', $slug)
            ->where('status', StatusKarya::TERBIT)
            ->first();

        if (! $karya) {
            return response()->json([
                'message' => 'Karya sastra tidak ditemukan atau belum diterbitkan.',
            ], 404);
        }

        return new KaryaResource($karya);
    }
}

