<?php

namespace App\Models;

use App\Enums\StatusKarya;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKarya extends Model
{
    use HasFactory;

    protected $table = 'riwayat_karya';

    protected $fillable = [
        'karya_id',
        'status',
        'catatan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusKarya::class,
        ];
    }

    public function karya(): BelongsTo
    {
        return $this->belongsTo(Karya::class, 'karya_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof StatusKarya
            ? $this->status->label()
            : (StatusKarya::tryFrom((string) $this->status)?->label() ?? (string) $this->status);
    }
}

