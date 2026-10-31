<?php

namespace App\Enums;

enum StatusKarya: string
{
    case DIKIRIM = 'dikirim';
    case DITINJAU = 'ditinjau';
    case PERLU_REVISI = 'perlu_revisi';
    case TERBIT = 'terbit';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::DIKIRIM => 'Dikirim',
            self::DITINJAU => 'Sedang Ditinjau',
            self::PERLU_REVISI => 'Perlu Revisi',
            self::TERBIT => 'Terbit',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::TERBIT, self::DITOLAK], true);
    }
}
