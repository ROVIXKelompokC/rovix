<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\OfficerWorkloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            default => redirect()->route('warga.dashboard'),
        };
    }

    public function adminDashboard(OfficerWorkloadService $workloadService)
    {
        // Jalankan distribusi beban kerja otomatis untuk laporan terverifikasi yang belum memiliki SPK
        $workloadService->autoDispatchVerifiedReports();

        $stats = [
            'total_reports' => Report::count(),
            'submitted_reports' => Report::where('status', 'submitted')->count(),
            'in_progress_reports' => Report::whereIn('status', ['verified', 'in_progress'])->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
            'total_users' => User::count(),
            'total_officers' => User::where('role', 'petugas')->count(),
            'total_districts' => District::count(),
            'total_categories' => ReportCategory::count(),
            'avg_rating' => round((float) Rating::avg('score'), 1) ?: 4.8,
        ];

        $recentReports = Report::with(['user', 'district', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $activeWorkOrders = WorkOrder::with(['report', 'officer'])
            ->latest()
            ->take(4)
            ->get();

        $rankedOfficers = $workloadService->getRankedOfficers();

        return view('dashboard.admin', compact('stats', 'recentReports', 'activeWorkOrders', 'rankedOfficers'));
    }

    public function petugasDashboard()
    {
        $user = Auth::user();

        $allMyWorkOrders = WorkOrder::with(['report.district', 'report.category'])
            ->where('officer_id', $user->id)
            ->latest()
            ->get();

        // Pada halaman Tugas SPK Aktif, tampilkan yang masih aktif (assigned & in_progress)
        $myWorkOrders = $allMyWorkOrders->whereIn('work_status', ['assigned', 'in_progress'])->values();

        $stats = [
            'assigned' => $allMyWorkOrders->where('work_status', 'assigned')->count(),
            'in_progress' => $allMyWorkOrders->where('work_status', 'in_progress')->count(),
            'completed' => $allMyWorkOrders->where('work_status', 'completed')->count(),
        ];

        return view('dashboard.petugas', compact('myWorkOrders', 'stats'));
    }

    public function petugasHistory()
    {
        $user = Auth::user();

        $allMyWorkOrders = WorkOrder::with(['report.district', 'report.category', 'report.rating'])
            ->where('officer_id', $user->id)
            ->latest('updated_at')
            ->get();

        $historyWorkOrders = $allMyWorkOrders->where('work_status', 'completed')->values();

        $stats = [
            'assigned' => $allMyWorkOrders->where('work_status', 'assigned')->count(),
            'in_progress' => $allMyWorkOrders->where('work_status', 'in_progress')->count(),
            'completed' => $historyWorkOrders->count(),
        ];

        return view('dashboard.petugas-history', compact('historyWorkOrders', 'stats'));
    }

    public function updateWorkOrderStatus(Request $request, WorkOrder $workOrder)
    {
        $user = Auth::user();

        if ($user->role === 'petugas' && (int) $workOrder->officer_id !== (int) $user->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah SPK petugas lain.');
        }

        $validated = $request->validate([
            'work_status' => 'required|in:assigned,in_progress,completed',
            'repair_notes' => 'nullable|string|max:1000',
        ]);

        $workOrder->update([
            'work_status' => $validated['work_status'],
            'repair_notes' => $validated['repair_notes'] ?? $workOrder->repair_notes,
            'resolved_at' => $validated['work_status'] === 'completed' ? now() : $workOrder->resolved_at,
        ]);

        // Sinkronkan status laporan induk dan catat di audit_logs
        if ($workOrder->report) {
            $oldReportStatus = $workOrder->report->status;
            $newReportStatus = match ($validated['work_status']) {
                'completed' => 'resolved',
                'in_progress' => 'in_progress',
                default => 'verified',
            };

            if ($oldReportStatus !== $newReportStatus) {
                $workOrder->report->update(['status' => $newReportStatus]);

                AuditLog::create([
                    'report_id' => $workOrder->report->id,
                    'user_id' => $user->id,
                    'action_name' => 'SPK_STATUS_' . strtoupper($validated['work_status']),
                    'previous_status' => $oldReportStatus,
                    'new_status' => $newReportStatus,
                ]);
            }
        }

        if ($validated['work_status'] === 'completed') {
            return redirect()->route('petugas.history')
                ->with('success', "SPK {$workOrder->spk_number} telah diselesaikan dan dipindahkan ke Riwayat Pengerjaan.");
        }

        return back()->with('success', "Status kerja SPK {$workOrder->spk_number} berhasil diperbarui menjadi " . strtoupper($validated['work_status']) . ".");
    }

    public function wargaDashboard()
    {
        $user = Auth::user();

        $myReports = Report::with(['district', 'category', 'rating'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $myReports->count(),
            'submitted' => $myReports->where('status', 'submitted')->count(),
            'in_progress' => $myReports->whereIn('status', ['verified', 'in_progress'])->count(),
            'resolved' => $myReports->where('status', 'resolved')->count(),
        ];

        return view('dashboard.warga', compact('myReports', 'stats'));
    }
}
