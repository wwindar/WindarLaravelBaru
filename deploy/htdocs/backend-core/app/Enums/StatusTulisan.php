<?php

namespace App\Enums;

enum StatusTulisan: string
{
    case Draf = 'draf';
    case Menunggu = 'menunggu';
    case Terbit = 'terbit';
    case PerluRevisi = 'perlu_revisi';
    case Ditolak = 'ditolak';

    /** Label dalam Bahasa Indonesia. */
    public function label(): string
    {
        return match ($this) {
            self::Draf => 'Draf',
            self::Menunggu => 'Menunggu Persetujuan',
            self::Terbit => 'Terbit',
            self::PerluRevisi => 'Perlu Revisi',
            self::Ditolak => 'Ditolak',
        };
    }

    /** Apakah penulis boleh mengedit tulisan dengan status ini? */
    public function bisaDiedit(): bool
    {
        return match ($this) {
            self::Draf, self::PerluRevisi => true,
            default => false,
        };
    }
}
