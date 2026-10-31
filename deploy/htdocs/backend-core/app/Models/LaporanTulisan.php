<?php

namespace App\Models;

use App\Enums\StatusLaporan;
use Database\Factories\LaporanTulisanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tulisan_id', 'nama_pelapor', 'email_pelapor', 'alasan', 'status'])]
class LaporanTulisan extends Model
{
    /** @use HasFactory<LaporanTulisanFactory> */
    use HasFactory;

    protected $table = 'laporan_tulisan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusLaporan::class,
        ];
    }

    /** Tulisan yang dilaporkan. */
    public function tulisan(): BelongsTo
    {
        return $this->belongsTo(Tulisan::class);
    }
}
