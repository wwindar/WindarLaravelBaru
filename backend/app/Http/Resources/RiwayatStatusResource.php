<?php

namespace App\Http\Resources;

use App\Enums\StatusPengajuan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatStatusResource extends JsonResource
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
            'pengajuan_id' => $this->pengajuan_id,
            'status' => $statusCode,
            'status_label' => $statusLabel,
            'catatan' => $this->catatan,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
