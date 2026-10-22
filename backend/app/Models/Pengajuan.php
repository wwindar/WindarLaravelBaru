<?php

namespace App\Models;

use App\Enums\StatusPengajuan;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';

    protected $fillable = [
        'nomor_pelacakan',
        'layanan_id',
        'nama_pemohon',
        'nim',
        'email',
        'program_studi',
        'keperluan',
        'dokumen',
        'status',
        'catatan_petugas',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dokumen' => 'array',
            'status' => StatusPengajuan::class,
        ];
    }

    public static function generateNomorPelacakan(int $id, ?CarbonInterface $createdAt = null): string
    {
        $year = $createdAt ? $createdAt->format('Y') : date('Y');

        return sprintf('PLM-%s-%05d', $year, $id);
    }

    protected static function booted(): void
    {
        static::created(function (Pengajuan $pengajuan): void {
            if (empty($pengajuan->nomor_pelacakan)) {
                $pengajuan->nomor_pelacakan = self::generateNomorPelacakan($pengajuan->id, $pengajuan->created_at);
                $pengajuan->saveQuietly();
            }
        });
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class, 'pengajuan_id')->orderBy('id', 'desc');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->riwayat();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof StatusPengajuan
            ? $this->status->label()
            : (StatusPengajuan::tryFrom((string) $this->status)?->label() ?? (string) $this->status);
    }
}
