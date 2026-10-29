<?php

namespace App\Http\Resources;

use App\Enums\StatusKarya;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatKaryaResource extends JsonResource
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
            'karya_id' => $this->karya_id,
            'status' => $this->status instanceof StatusKarya ? $this->status->value : (string) $this->status,
            'status_label' => $this->status_label,
            'catatan' => $this->catatan,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
