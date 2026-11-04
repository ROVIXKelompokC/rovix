<?php

namespace App\Enums;

/**
 * Enum ReportStatus
 * Sesuai Bab 4.4 Class Diagram Proposal ROVIX:
 * Daftar status tiket laporan kerusakan jalan yang valid.
 */
enum ReportStatus: string
{
    case SUBMITTED = 'submitted';
    case VERIFIED = 'verified';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::IN_PROGRESS => 'Sedang Diperbaiki',
            self::RESOLVED => 'Selesai Diperbaiki',
            self::REJECTED => 'Ditolak / Spam',
        };
    }
}
