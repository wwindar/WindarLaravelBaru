<?php

namespace App\Enums;

enum JenisTulisan: string
{
    case Puisi = 'puisi';
    case Cerpen = 'cerpen';
    case Artikel = 'artikel';
    case Esai = 'esai';

    /** Label dalam Bahasa Indonesia. */
    public function label(): string
    {
        return match ($this) {
            self::Puisi => 'Puisi',
            self::Cerpen => 'Cerpen',
            self::Artikel => 'Artikel',
            self::Esai => 'Esai',
        };
    }
}
