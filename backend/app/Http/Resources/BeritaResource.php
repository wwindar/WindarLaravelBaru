<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeritaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'slug' => $this->slug,
            'ringkasan' => $this->ringkasan,
            'foto_url' => $this->foto ? asset('storage/' . $this->foto) : null,
            'isi' => $this->isi,
            'status' => $this->status,
            'diterbitkan_pada' => $this->diterbitkan_pada?->toIso8601String(),
            'diterbitkan_pada_formatted' => $this->diterbitkan_pada?->translatedFormat('d F Y') ?? $this->created_at?->translatedFormat('d F Y'),
            'penulis' => [
                'id' => $this->penulis?->id,
                'name' => $this->penulis?->name ?? 'Redaksi Aksara Kampus',
                'role' => $this->penulis?->role ?? 'admin',
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

