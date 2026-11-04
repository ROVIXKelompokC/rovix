<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class OfficerWorkloadService
 * Layanan pembagian beban kerja petugas lapangan secara adil (Fair Workload Balancing)
 * dan pencegahan bentrok/redundansi jadwal penugasan SPK.
 */
class OfficerWorkloadService
{
    /**
     * Ambil daftar seluruh petugas lapangan beserta beban kerja aktif, status jadwal,
     * dan urutan prioritas penugasan yang paling adil.
     *
     * @return Collection<int, User>
     */
    public function getRankedOfficers(?int $districtId = null, ?string $targetSlaDate = null): Collection
    {
        $date = $targetSlaDate ? Carbon::parse($targetSlaDate)->toDateString() : Carbon::today()->addDays(3)->toDateString();

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
            // 1. Utamakan petugas yang TIDAK bentrok jadwal pada tanggal target
            ['has_schedule_conflict', 'asc'],
            // 2. Utamakan petugas dengan jumlah SPK aktif paling sedikit (beban kerja seimbang)
            ['active_spk_count', 'asc'],
            // 3. Utamakan petugas yang berada di zona kecamatan yang sama dengan laporan
            ['is_same_district', 'desc'],
            // 4. Utamakan petugas dengan total riwayat SPK selesai paling sedikit agar merata
            ['completed_spk_count', 'asc'],
            ['id', 'asc'],
        ])->values();
    }

    /**
     * Pilih 1 petugas paling ideal secara otomatis berdasarkan beban kerja terendah
     * dan bebas bentrok jadwal.
     */
    public function selectFairOfficer(?int $districtId = null, ?string $targetSlaDate = null): ?User
    {
        $ranked = $this->getRankedOfficers($districtId, $targetSlaDate);

        // Prioritaskan yang tidak bentrok jadwal; jika semua penuh di tanggal tsb, ambil yang beban aktifnya terendah
        return $ranked->firstWhere('has_schedule_conflict', false) ?? $ranked->first();
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
}
