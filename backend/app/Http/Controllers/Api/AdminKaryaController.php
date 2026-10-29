<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusKarya;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateStatusKaryaRequest;
use App\Http\Resources\KaryaResource;
use App\Models\Karya;
use App\Models\RiwayatKarya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AdminKaryaController extends Controller
{
    /**
     * Display a listing of submissions for editorial staff.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Karya::with('riwayat')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(judul) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(nama_penulis) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(nomor_kiriman) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $karya = $query->paginate($perPage);

        return KaryaResource::collection($karya);
    }

    /**
     * Display the specified submission detail with history.
     */
    public function show(int|string $id): KaryaResource|JsonResponse
    {
        $karya = Karya::with('riwayat')->find($id);

        if (! $karya) {
            return response()->json([
                'message' => 'Karya sastra tidak ditemukan.',
            ], 404);
        }

        return new KaryaResource($karya);
    }

    /**
     * Update the status of a submission and log history.
     */
    public function updateStatus(AdminUpdateStatusKaryaRequest $request, int|string $id): KaryaResource|JsonResponse
    {
        $karya = Karya::find($id);

        if (! $karya) {
            return response()->json([
                'message' => 'Karya sastra tidak ditemukan.',
            ], 404);
        }

        $currentStatus = $karya->status instanceof StatusKarya
            ? $karya->status
            : StatusKarya::tryFrom((string) $karya->status);

        if ($currentStatus && $currentStatus->isFinal()) {
            return response()->json([
                'message' => 'Karya dengan status terbit atau ditolak bersifat final dan tidak dapat diubah lagi.',
            ], 422);
        }

        $newStatus = StatusKarya::from($request->input('status'));
        $catatan = $request->input('catatan');

        DB::transaction(function () use ($karya, $newStatus, $catatan): void {
            $karya->status = $newStatus;
            if ($catatan !== null) {
                $karya->catatan_redaksi = $catatan;
            }
            $karya->save();

            RiwayatKarya::create([
                'karya_id' => $karya->id,
                'status' => $newStatus,
                'catatan' => $catatan,
            ]);
        });

        $karya->load('riwayat');

        return new KaryaResource($karya);
    }

    /**
     * Remove the specified submission (only allowed if status is dikirim or ditolak).
     */
    public function destroy(int|string $id): JsonResponse
    {
        $karya = Karya::find($id);

        if (! $karya) {
            return response()->json([
                'message' => 'Karya sastra tidak ditemukan.',
            ], 404);
        }

        $allowedStatuses = [
            StatusKarya::DIKIRIM,
            StatusKarya::DITOLAK,
        ];

        if (! in_array($karya->status, $allowedStatuses, true)) {
            return response()->json([
                'message' => 'Karya hanya dapat dihapus jika berstatus Dikirim atau Ditolak.',
            ], 422);
        }

        DB::transaction(function () use ($karya): void {
            $karya->riwayat()->delete();
            $karya->delete();
        });

        return response()->json([
            'message' => 'Karya berhasil dihapus.',
        ], 200);
    }
}

