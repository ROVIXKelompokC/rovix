<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class OfficerWorkloadService
 * Layanan pembagian beban kerja petugas lapangan secara otomatis & merata
 * serta pencegahan jadwal penugasan SPK yang bertabrakan atau redundan.
 */
class OfficerWorkloadService
{
    /**
     * Ambil daftar seluruh petugas lapangan beserta beban kerja aktif, status jadwal,
     * dan urutan prioritas penugasan otomatis yang paling merata.
     *
     * @return Collection<int, User>
     */
    public function getRankedOfficers(?int $districtId = null, ?string $targetSlaDate = null): Collection
    {
        $date = $targetSlaDate ? Carbon::parse($targetSlaDate)->toDateString() : Carbon::today()->addDays(2)->toDateString();

        $officers = User::query()
            ->where('role', 'petugas')
            ->with(['district', 'workOrders' => function ($q) {
                $q->whereIn('work_status', ['assigned', 'in_progress'])->with('report');
            }])
            ->withCount([
                'workOrders as active_spk_count' => fn ($q) => $q->whereIn('work_status', ['assigned', 'in_progress']),
                'workOrders as completed_spk_count' => fn ($q) => $q->where('work_status', 'completed'),
            ])
            ->get();

        return $officers->map(function (User $officer) use ($districtId, $date) {
            // Cek apakah petugas sudah memiliki jadwal SPK aktif pada tanggal target SLA yang sama
            $conflictingSpk = $officer->workOrders->first(function (WorkOrder $wo) use ($date) {
                return $wo->target_sla && $wo->target_sla->toDateString() === $date;
            });

            $officer->setAttribute('has_schedule_conflict', $conflictingSpk !== null);
            $officer->setAttribute('conflict_spk_number', $conflictingSpk?->spk_number);
            $officer->setAttribute('is_same_district', $districtId !== null && (int) $officer->district_id === (int) $districtId);

            return $officer;
        })->sortBy([
            // 1. Utamakan petugas yang jadwalnya tersedia pada tanggal target
            ['has_schedule_conflict', 'asc'],
            // 2. Utamakan petugas dengan jumlah SPK aktif paling sedikit (beban kerja seimbang)
            ['active_spk_count', 'asc'],
            // 3. Utamakan petugas yang berada di wilayah kecamatan yang sama dengan laporan
            ['is_same_district', 'desc'],
            // 4. Utamakan petugas dengan total riwayat SPK selesai paling sedikit agar merata
            ['completed_spk_count', 'asc'],
            ['id', 'asc'],
        ])->values();
    }

    /**
     * Pilih 1 petugas paling ideal secara otomatis berdasarkan beban kerja terendah
     * dan ketersediaan jadwal.
     */
    public function selectFairOfficer(?int $districtId = null, ?string $targetSlaDate = null): ?User
    {
        $ranked = $this->getRankedOfficers($districtId, $targetSlaDate);

        return $ranked->firstWhere('has_schedule_conflict', false) ?? $ranked->first();
    }

    /**
     * Cari tanggal target SLA terdekat yang tidak bertabrakan dengan jadwal aktif petugas
     */
    public function resolveNonConflictingSlaDate(User $officer, ?string $preferredDate = null): string
    {
        $candidate = $preferredDate ? Carbon::parse($preferredDate) : Carbon::today()->addDays(2);

        $occupiedDates = WorkOrder::query()
            ->where('officer_id', $officer->id)
            ->whereIn('work_status', ['assigned', 'in_progress'])
            ->pluck('target_sla')
            ->filter()
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();

        while (in_array($candidate->toDateString(), $occupiedDates, true)) {
            $candidate->addDay();
        }

        return $candidate->toDateString();
    }

    /**
     * Pastikan laporan tidak memiliki SPK ganda/redundan yang masih aktif
     */
    public function hasRedundantActiveWorkOrder(int $reportId): bool
    {
        return WorkOrder::query()
            ->where('report_id', $reportId)
            ->whereIn('work_status', ['assigned', 'in_progress'])
            ->exists();
    }

    /**
     * Distribusi otomatis: terbitkan & tugaskan SPK secara otomatis untuk laporan berstatus
     * 'verified' yang belum memiliki SPK, kepada petugas dengan beban kerja paling ringan.
     */
    public function autoDispatchVerifiedReports(): int
    {
        $dispatchedCount = 0;

        $verifiedReportsWithoutSpk = Report::query()
            ->where('status', 'verified')
            ->whereDoesntHave('workOrder')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($verifiedReportsWithoutSpk as $report) {
            $officer = $this->selectFairOfficer($report->district_id);
            if (! $officer) {
                continue;
            }

            $targetSla = $this->resolveNonConflictingSlaDate($officer);
            $nextNumber = WorkOrder::count() + 1;

            WorkOrder::create([
                'report_id' => $report->id,
                'officer_id' => $officer->id,
                'spk_number' => sprintf('SPK-PU-%s-%03d', now()->format('Y'), $nextNumber),
                'target_sla' => $targetSla,
                'work_status' => 'assigned',
                'repair_notes' => 'Penugasan otomatis berdasarkan distribusi beban kerja wilayah ' . ($report->district->name ?? 'Dinas PU') . '.',
            ]);

            $report->update(['status' => 'in_progress']);
            $dispatchedCount++;
        }

        return $dispatchedCount;
    }
}
