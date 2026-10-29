<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreAgendaRequest;
use App\Http\Requests\AdminUpdateAgendaRequest;
use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminAgendaController extends Controller
{
    /**
     * Display a listing of agenda events.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Agenda::query()->orderBy('mulai_pada', 'desc');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(lokasi) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $agenda = $query->paginate($perPage);

        return AgendaResource::collection($agenda);
    }

    /**
     * Store a newly created agenda.
     */
    public function store(AdminStoreAgendaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Agenda::generateUniqueSlug($data['judul']);

        $agenda = Agenda::create($data);

        return (new AgendaResource($agenda))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified agenda.
     */
    public function show(int|string $id): AgendaResource|JsonResponse
    {
        $agenda = Agenda::find($id);

        if (! $agenda) {
            return response()->json([
                'message' => 'Agenda tidak ditemukan.',
            ], 404);
        }

        return new AgendaResource($agenda);
    }

    /**
     * Update the specified agenda.
     */
    public function update(AdminUpdateAgendaRequest $request, int|string $id): AgendaResource|JsonResponse
    {
        $agenda = Agenda::find($id);

        if (! $agenda) {
            return response()->json([
                'message' => 'Agenda tidak ditemukan.',
            ], 404);
        }

        $data = $request->validated();

        if ($request->filled('judul') && $request->input('judul') !== $agenda->judul) {
            $data['slug'] = Agenda::generateUniqueSlug($request->input('judul'), $agenda->id);
        }

        $agenda->update($data);

        return new AgendaResource($agenda);
    }

    /**
     * Remove the specified agenda.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $agenda = Agenda::find($id);

        if (! $agenda) {
            return response()->json([
                'message' => 'Agenda tidak ditemukan.',
            ], 404);
        }

        $agenda->delete();

        return response()->json([
            'message' => 'Agenda berhasil dihapus.',
        ], 200);
    }
}

