<?php

namespace App\Http\Resources;

use App\Models\LaporanTulisan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LaporanTulisan */
class LaporanTulisanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tulisan_id' => $this->tulisan_id,
            'tulisan_judul' => $this->whenLoaded('tulisan', fn () => $this->tulisan->judul),
            'nama_pelapor' => $this->nama_pelapor,
            'email_pelapor' => $this->email_pelapor,
            'alasan' => $this->alasan,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
