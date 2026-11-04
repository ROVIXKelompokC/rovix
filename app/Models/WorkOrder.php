<?php

namespace App\Models;

use App\Services\OfficerWorkloadService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'officer_id',
        'spk_number',
        'target_sla',
        'work_status',
        'after_photo_url',
        'repair_notes',
        'resolved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (WorkOrder $workOrder) {
            $workloadService = app(OfficerWorkloadService::class);
            $report = $workOrder->report_id ? Report::find($workOrder->report_id) : null;

            // Jika officer_id belum ditentukan, pilih otomatis petugas dengan beban kerja terendah
            if (empty($workOrder->officer_id)) {
                $officer = $workloadService->selectFairOfficer(
                    $report?->district_id,
                    $workOrder->target_sla ? (string) $workOrder->target_sla : null
                );

                if ($officer) {
                    $workOrder->officer_id = $officer->id;
                }
            }

            // Pastikan jadwal target_sla tidak bertabrakan dengan jadwal aktif petugas terpilih
            if (! empty($workOrder->officer_id)) {
                $officer = User::find($workOrder->officer_id);
                if ($officer) {
                    $workOrder->target_sla = $workloadService->resolveNonConflictingSlaDate(
                        $officer,
                        $workOrder->target_sla ? (string) $workOrder->target_sla : null
                    );
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'target_sla' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
