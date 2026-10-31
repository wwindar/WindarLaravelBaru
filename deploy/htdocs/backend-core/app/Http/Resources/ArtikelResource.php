<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtikelResource extends JsonResource
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
            'ringkasan' => $this->ringkasan,
            'isi' => $this->isi,
            'jenis' => $this->jenis,
            'kategori_id' => $this->kategori_id,
            'kategori' => $this->whenLoaded('kategori', function () {
                return [
                    'id' => $this->kategori->id,
                    'slug' => $this->kategori->slug,
                    'nama' => $this->kategori->nama,
                ];
            }),
            'penulis' => $this->penulis,
            'status' => $this->status,
            'status_label' => $this->status === 'terbit' ? 'Terbit' : 'Draft',
            'diterbitkan_pada' => $this->diterbitkan_pada?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
