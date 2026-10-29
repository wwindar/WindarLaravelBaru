<?php

namespace App\Http\Resources;

use App\Enums\StatusKarya;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KaryaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'nomor_kiriman' => $this->nomor_kiriman,
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'isi' => $this->isi,
            'nama_penulis' => $this->nama_penulis,
            'email' => $this->email,
            'status' => $this->status instanceof StatusKarya ? $this->status->value : (string) $this->status,
            'status_label' => $this->status_label,
            'catatan_redaksi' => $this->catatan_redaksi,
            'riwayat' => RiwayatKaryaResource::collection($this->whenLoaded('riwayat')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

