<?php

namespace App\Models;

use Database\Factories\NotifikasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'judul', 'pesan', 'dibaca'])]
class Notifikasi extends Model
{
    /** @use HasFactory<NotifikasiFactory> */
    use HasFactory;

    protected $table = 'notifikasi';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
        ];
    }

    /** Pemilik notifikasi. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
