<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AgendaController extends Controller
{
    /**
     * Display a listing of literary events/agendas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Agenda::query();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(lokasi) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(deskripsi) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        if ($request->filled('waktu')) {
            $waktu = $request->query('waktu');
            $now = Carbon::now();

            if ($waktu === 'mendatang') {
                $query->where('selesai_pada', '>=', $now)
                    ->orderBy('mulai_pada', 'asc');
            } elseif ($waktu === 'lampau') {
                $query->where('selesai_pada', '<', $now)
                    ->orderBy('mulai_pada', 'desc');
            } else {
                $query->orderBy('mulai_pada', 'asc');
            }
        } else {
            $query->orderBy('mulai_pada', 'asc');
        }

        $perPage = (int) $request->query('per_page', 9);
        if ($perPage < 1) {
            $perPage = 9;
        }

        $agenda = $query->paginate($perPage);

        return AgendaResource::collection($agenda);
    }

    /**
     * Display the specified agenda by slug.
     */
    public function show(string $slug): AgendaResource|JsonResponse
    {
        $agenda = Agenda::where('slug', $slug)->first();

        if (! $agenda) {
            return response()->json([
                'message' => 'Agenda kegiatan sastra tidak ditemukan.',
            ], 404);
        }

        return new AgendaResource($agenda);
    }
}

