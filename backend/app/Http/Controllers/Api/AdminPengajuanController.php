<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPengajuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStatusPengajuanRequest;
use App\Http\Resources\PengajuanResource;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AdminPengajuanController extends Controller
{
    /**
     * Display a paginated listing of applications with filter & search.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Pengajuan::with(['layanan', 'riwayat'])->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $status = trim((string) $request->query('status', ''));
            if ($status !== '') {
                $query->where('status', $status);
            }
        }

        if ($request->has('search')) {
            $search = trim((string) $request->query('search', ''));
            if ($search !== '') {
                $searchLower = mb_strtolower($search, 'UTF-8');
                $query->where(function ($q) use ($searchLower): void {
                    $q->whereRaw('LOWER(nama_pemohon) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(nim) LIKE ?', ['%'.$searchLower.'%'])
                        ->orWhereRaw('LOWER(nomor_pelacakan) LIKE ?', ['%'.$searchLower.'%']);
                });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $pengajuan = $query->paginate($perPage);

        return PengajuanResource::collection($pengajuan);
    }

    /**
     * Display the specified application detail with history.
     */
    public function show(int|string $id): JsonResponse|PengajuanResource
    {
        $pengajuan = Pengajuan::with(['layanan', 'riwayat'])->find($id);

        if (! $pengajuan) {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        return new PengajuanResource($pengajuan);
    }

    /**
     * Update application status and log history.
     */
    public function updateStatus(UpdateStatusPengajuanRequest $request, int|string $id): JsonResponse|PengajuanResource
    {
        $pengajuan = Pengajuan::find($id);

        if (! $pengajuan) {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $currentStatus = $pengajuan->status instanceof StatusPengajuan
            ? $pengajuan->status
            : StatusPengajuan::tryFrom((string) $pengajuan->status);

        if ($currentStatus && $currentStatus->isFinal()) {
            return response()->json([
                'message' => 'Pengajuan dengan status selesai atau ditolak tidak dapat diubah lagi.',
            ], 422);
        }

        $newStatus = StatusPengajuan::from($request->input('status'));
        $catatan = $request->input('catatan');

        DB::transaction(function () use ($pengajuan, $newStatus, $catatan): void {
            $pengajuan->status = $newStatus;
            if ($catatan !== null) {
                $pengajuan->catatan_petugas = $catatan;
            }
            $pengajuan->save();

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => $newStatus,
                'catatan' => $catatan,
            ]);
        });

        $pengajuan->load(['layanan', 'riwayat']);

        return new PengajuanResource($pengajuan);
    }

    /**
     * Remove the specified application (only allowed for diajukan or ditolak).
     */
    public function destroy(int|string $id): JsonResponse
    {
        $pengajuan = Pengajuan::find($id);

        if (! $pengajuan) {
            return response()->json([
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $allowedStatuses = [
            StatusPengajuan::DIAJUKAN,
            StatusPengajuan::DITOLAK,
        ];

        if (! in_array($pengajuan->status, $allowedStatuses, true)) {
            return response()->json([
                'message' => 'Pengajuan hanya dapat dihapus apabila berstatus Diajukan atau Ditolak.',
            ], 422);
        }

        DB::transaction(function () use ($pengajuan): void {
            $pengajuan->riwayat()->delete();
            $pengajuan->delete();
        });

        return response()->json([
            'message' => 'Pengajuan berhasil dihapus.',
        ], 200);
    }
}
