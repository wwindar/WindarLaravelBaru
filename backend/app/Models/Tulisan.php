<?php

namespace App\Models;

use App\Enums\JenisTulisan;
use App\Enums\StatusTulisan;
use Database\Factories\TulisanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'kategori_id',
    'jenis',
    'slug',
    'judul',
    'isi',
    'ringkasan',
    'status',
    'catatan_editor',
    'diterbitkan_pada',
])]
class Tulisan extends Model
{
    /** @use HasFactory<TulisanFactory> */
    use HasFactory;

    protected $table = 'tulisan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusTulisan::class,
            'jenis' => JenisTulisan::class,
            'diterbitkan_pada' => 'datetime',
        ];
    }

    /** Penulis tulisan. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Kategori tulisan. */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /** Riwayat perubahan status. */
    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusTulisan::class);
    }

    /** Laporan yang masuk untuk tulisan ini. */
    public function laporan(): HasMany
    {
        return $this->hasMany(LaporanTulisan::class);
    }
}
