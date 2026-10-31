<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
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
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'deskripsi' => $this->deskripsi,
            'lokasi' => $this->lokasi,
            'mulai_pada' => $this->mulai_pada?->toIso8601String(),
            'selesai_pada' => $this->selesai_pada?->toIso8601String(),
            'kuota' => $this->kuota,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
