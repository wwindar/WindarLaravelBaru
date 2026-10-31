<?php

namespace App\Models;

use App\Enums\StatusTulisan;
use Database\Factories\RiwayatStatusTulisanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tulisan_id', 'status', 'catatan'])]
class RiwayatStatusTulisan extends Model
{
    /** @use HasFactory<RiwayatStatusTulisanFactory> */
    use HasFactory;

    protected $table = 'riwayat_status_tulisan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusTulisan::class,
        ];
    }

    /** Tulisan yang bersangkutan. */
    public function tulisan(): BelongsTo
    {
        return $this->belongsTo(Tulisan::class);
    }
}
