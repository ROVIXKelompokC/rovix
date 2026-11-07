<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\OfficerWorkloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkOrderController extends Controller
{
    /**
     * SCRUM-42 (PBI Read - sisi Admin):
     * Daftar seluruh SPK beserta ringkasan status, pencarian, dan filter.
     */
    public function index(Request $request)
    {
        $stats = [
            'total' => WorkOrder::count(),
            'assigned' => WorkOrder::where('work_status', 'assigned')->count(),
            'in_progress' => WorkOrder::where('work_status', 'in_progress')->count(),
            'completed' => WorkOrder::where('work_status', 'completed')->count(),
            'ready_to_dispatch' => $this->eligibleReports()->count(),
        ];

        $workOrders = WorkOrder::with(['report.district', 'report.category', 'officer'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('spk_number', 'like', "%{$search}%")
                        ->orWhereHas('report', fn ($r) => $r->where('ticket_code', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%"))
                        ->orWhereHas('officer', fn ($o) => $o->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('work_status'), fn ($query) => $query->where('work_status', $request->input('work_status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.work-orders.index', compact('stats', 'workOrders'));
    }

    /**
     * Detail satu SPK (dibuka dari tombol "Detail" pada daftar SPK).
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['report.district', 'report.category', 'report.user', 'officer.district']);

        return view('admin.work-orders.show', compact('workOrder'));
    }

    /**
     * SCRUM-38 (PBI Create - form):
     * Form penerbitan SPK baru. Hanya laporan berstatus 'verified' yang belum punya SPK
     * yang boleh dipilih. Daftar petugas diurutkan oleh OfficerWorkloadService.
     */
    public function create(Request $request, OfficerWorkloadService $workloadService)
    {
        $reports = $this->eligibleReports()
            ->with(['district', 'category'])
            ->orderByDesc('id')
            ->get();

        $selectedReportId = $request->integer('report_id') ?: $reports->first()?->id;
        $selectedReport = $reports->firstWhere('id', $selectedReportId);

        $defaultSla = now()->addDays(2)->toDateString();

        $officers = $workloadService->getRankedOfficers($selectedReport?->district_id, $defaultSla);

        return view('admin.work-orders.create', [
            'reports' => $reports,
            'officers' => $officers,
            'selectedReportId' => $selectedReportId,
            'defaultSla' => $defaultSla,
            'nextSpkNumber' => $this->generateSpkNumber(),
        ]);
    }

    /**
     * SCRUM-38 (PBI Create - simpan):
     * Simpan SPK baru, ubah status laporan menjadi 'in_progress', dan catat ke audit_logs.
     */
    public function store(Request $request, OfficerWorkloadService $workloadService)
    {
        $validated = $request->validate([
            'spk_number' => ['required', 'string', 'max:50', 'unique:work_orders,spk_number'],
            'report_id' => ['required', 'integer', 'exists:reports,id', 'unique:work_orders,report_id'],
            'officer_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'petugas'),
            ],
            'target_sla' => ['required', 'date', 'after_or_equal:today'],
            'repair_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'spk_number.unique' => 'Nomor SPK tersebut sudah dipakai.',
            'report_id.unique' => 'Laporan ini sudah memiliki SPK.',
            'officer_id.exists' => 'Petugas yang dipilih tidak valid.',
            'target_sla.after_or_equal' => 'Target SLA tidak boleh tanggal yang sudah lewat.',
        ]);

        $report = Report::findOrFail($validated['report_id']);

        if ($report->status !== 'verified') {
            return back()->withInput()->withErrors([
                'report_id' => 'SPK hanya bisa diterbitkan untuk laporan berstatus VERIFIED (saat ini: ' . strtoupper($report->status) . ').',
            ]);
        }

        if ($workloadService->hasRedundantActiveWorkOrder($report->id)) {
            return back()->withInput()->withErrors([
                'report_id' => 'Laporan ini masih memiliki SPK aktif.',
            ]);
        }

        $workOrder = DB::transaction(function () use ($validated, $report) {
            // Jika officer_id kosong, WorkOrder::booted() memilih petugas otomatis (beban kerja terendah).
            // WorkOrder::booted() juga menggeser target_sla bila bentrok dengan jadwal petugas.
            $workOrder = WorkOrder::create([
                'report_id' => $report->id,
                'officer_id' => $validated['officer_id'] ?? null,
                'spk_number' => $validated['spk_number'],
                'target_sla' => $validated['target_sla'],
                'work_status' => 'assigned',
                'repair_notes' => $validated['repair_notes'] ?? null,
            ]);

            $previousStatus = $report->status;
            $report->update(['status' => 'in_progress']);

            AuditLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_name' => 'SPK_ISSUED',
                'previous_status' => $previousStatus,
                'new_status' => 'in_progress',
                'created_at' => now(),
            ]);

            return $workOrder;
        });

        $workOrder->load('officer');
        $message = "SPK {$workOrder->spk_number} berhasil diterbitkan dan ditugaskan ke {$workOrder->officer?->name}.";

        if ($workOrder->target_sla->toDateString() !== \Carbon\Carbon::parse($validated['target_sla'])->toDateString()) {
            $message .= ' Target SLA digeser ke ' . $workOrder->target_sla->format('d/m/Y') . ' karena bentrok dengan jadwal petugas.';
        }

        return redirect()->route('admin.work-orders.index')->with('success', $message);
    }

    /** Laporan 'verified' yang belum punya SPK. */
    private function eligibleReports()
    {
        return Report::query()
            ->where('status', 'verified')
            ->whereDoesntHave('workOrder');
    }

    /** Nomor SPK berikutnya, format SPK-PU-2026-003 (aman walau ada SPK yang dihapus). */
    private function generateSpkNumber(): string
    {
        $prefix = 'SPK-PU-' . now()->format('Y') . '-';

        $maxSuffix = WorkOrder::where('spk_number', 'like', $prefix . '%')
            ->pluck('spk_number')
            ->map(fn ($n) => (int) substr($n, strlen($prefix)))
            ->max() ?? 0;

        return $prefix . str_pad((string) ($maxSuffix + 1), 3, '0', STR_PAD_LEFT);
    }
}
