<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotifikasiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi user yang sedang login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifikasi = $request->user()
            ->notifikasi()
            ->latest()
            ->paginate(20);

        return NotifikasiResource::collection($notifikasi);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function baca(Request $request, int $id): JsonResponse
    {
        $notifikasi = $request->user()->notifikasi()->findOrFail($id);
        $notifikasi->update(['dibaca' => true]);

        return response()->json([
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'data' => new NotifikasiResource($notifikasi),
        ]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function bacaSemua(Request $request): JsonResponse
    {
        $request->user()->notifikasi()->where('dibaca', false)->update(['dibaca' => true]);

        return response()->json([
            'message' => 'Semua notifikasi telah ditandai sudah dibaca.',
        ]);
    }
}
