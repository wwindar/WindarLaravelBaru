<?php

namespace App\Http\Resources;

use App\Enums\StatusPengajuan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusCode = $this->status instanceof StatusPengajuan
            ? $this->status->value
            : (string) $this->status;

        $statusLabel = $this->status instanceof StatusPengajuan
            ? $this->status->label()
            : (StatusPengajuan::tryFrom($statusCode)?->label() ?? $statusCode);

        return [
            'id' => $this->id,
            'nomor_pelacakan' => $this->nomor_pelacakan,
            'layanan_id' => $this->layanan_id,
            'nama_layanan' => $this->layanan?->nama,
            'layanan' => $this->whenLoaded('layanan', fn () => new LayananResource($this->layanan)),
            'nama_pemohon' => $this->nama_pemohon,
            'nim' => $this->nim,
            'email' => $this->email,
            'program_studi' => $this->program_studi,
            'keperluan' => $this->keperluan,
            'dokumen' => $this->dokumen ?? [],
            'status' => $statusCode,
            'status_label' => $statusLabel,
            'catatan_petugas' => $this->catatan_petugas,
            'riwayat' => RiwayatStatusResource::collection($this->whenLoaded('riwayat')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
