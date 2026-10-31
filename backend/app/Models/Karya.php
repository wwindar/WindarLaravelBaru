<?php

namespace App\Models;

use App\Enums\StatusKarya;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Karya extends Model
{
    use HasFactory;

    protected $table = 'karya';

    protected $fillable = [
        'slug',
        'nomor_kiriman',
        'judul',
        'jenis',
        'isi',
        'nama_penulis',
        'email',
        'status',
        'catatan_redaksi',
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

    public static function generateNomorKiriman(int $id, ?CarbonInterface $createdAt = null): string
    {
        $year = $createdAt ? $createdAt->format('Y') : date('Y');

        return sprintf('AKS-%s-%05d', $year, $id);
    }

    public static function generateUniqueSlug(string $judul, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($judul);
        if ($baseSlug === '') {
            $baseSlug = 'karya';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::created(function (Karya $karya): void {
            if (empty($karya->nomor_kiriman)) {
                $karya->nomor_kiriman = self::generateNomorKiriman($karya->id, $karya->created_at);
                $karya->saveQuietly();
            }
        });
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatKarya::class, 'karya_id')->orderBy('id', 'desc');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof StatusKarya
            ? $this->status->label()
            : (StatusKarya::tryFrom((string) $this->status)?->label() ?? (string) $this->status);
    }
}
