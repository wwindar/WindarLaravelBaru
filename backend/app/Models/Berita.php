<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'judul', 'slug', 'ringkasan', 'foto', 'isi', 'status', 'diterbitkan_pada'])]
class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected function casts(): array
    {
        return [
            'diterbitkan_pada' => 'datetime',
        ];
    }

    /** Admin/dosen yang menulis berita. */
    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
