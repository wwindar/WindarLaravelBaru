<?php

namespace App\Http\Resources;

use App\Models\Tulisan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Tulisan */
class TulisanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'judul' => $this->judul,
            'jenis' => $this->jenis->value,
            'jenis_label' => $this->jenis->label(),
            'ringkasan' => $this->ringkasan,
            'isi' => $this->whenNotNull($this->when(
                $request->routeIs('*.show') || $request->query('with_isi'),
                $this->isi,
            )),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'catatan_editor' => $this->whenNotNull($this->catatan_editor),
            'diterbitkan_pada' => $this->diterbitkan_pada?->toIso8601String(),
            'penulis' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
            ],
            'kategori' => $this->whenLoaded('kategori', fn () => $this->kategori ? [
                'id' => $this->kategori->id,
                'slug' => $this->kategori->slug,
                'nama' => $this->kategori->nama,
            ] : null),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
