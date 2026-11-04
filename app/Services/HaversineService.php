<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Collection;

/**
 * Class HaversineService
 * Sesuai Bab 3.1 (FR-04), Use Case 4.1.4, & Bab 4.4 Class Diagram Proposal ROVIX:
 * Menghitung jarak koordinat geografis menggunakan formula Haversine untuk mendeteksi
 * indikasi duplikasi laporan kerusakan jalan dalam radius berdekatan (< 30 meter).
 */
class HaversineService
{
    public const DEFAULT_RADIUS_METERS = 30.0;

    /**
     * Hitung jarak (dalam meter) antara dua titik koordinat GPS menggunakan rumus Haversine
     */
    public function distanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusMeters = 6371000.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusMeters * $c, 2);
    }

    /**
     * Cari laporan aktif yang berada dalam radius < 30 meter dari titik koordinat baru
     *
     * @return Collection<int, Report>
     */
    public function findNearbyActiveReports(
        float $latitude,
        float $longitude,
        float $radiusMeters = self::DEFAULT_RADIUS_METERS,
        ?int $excludeReportId = null
    ): Collection {
        $activeReports = Report::query()
            ->whereIn('status', ['submitted', 'verified', 'in_progress'])
            ->when($excludeReportId, fn ($q) => $q->where('id', '!=', $excludeReportId))
            ->get();

        return $activeReports->filter(function (Report $report) use ($latitude, $longitude, $radiusMeters) {
            $distance = $this->distanceInMeters(
                $latitude,
                $longitude,
                (float) $report->latitude,
                (float) $report->longitude
            );
            $report->setAttribute('distance_meters', $distance);

            return $distance <= $radiusMeters;
        })->sortBy('distance_meters')->values();
    }
}
