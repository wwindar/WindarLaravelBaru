<?php

namespace App\Enums;

enum StatusPengajuan: string
{
    case DIAJUKAN = 'diajukan';
    case DIPERIKSA = 'diperiksa';
    case PERLU_PERBAIKAN = 'perlu_perbaikan';
    case DISETUJUI = 'disetujui';
    case SELESAI = 'selesai';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DIPERIKSA => 'Sedang Diperiksa',
            self::PERLU_PERBAIKAN => 'Perlu Perbaikan',
            self::DISETUJUI => 'Disetujui',
            self::SELESAI => 'Selesai',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SELESAI, self::DITOLAK], true);
    }
}
