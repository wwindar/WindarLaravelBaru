<?php

namespace App\Enums;

enum StatusLaporan: string
{
    case Menunggu = 'menunggu';
    case Ditinjau = 'ditinjau';
    case Selesai = 'selesai';

    /** Label dalam Bahasa Indonesia. */
    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Ditinjau => 'Sedang Ditinjau',
            self::Selesai => 'Selesai',
        };
    }
}
