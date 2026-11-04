<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use App\Models\WorkOrder;
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

    public function adminDashboard()
    {
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

        return view('dashboard.admin', compact('stats', 'recentReports', 'activeWorkOrders'));
    }

    public function petugasDashboard()
    {
        $user = Auth::user();

        $myWorkOrders = WorkOrder::with(['report.district', 'report.category'])
            ->where('officer_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'assigned' => $myWorkOrders->where('work_status', 'assigned')->count(),
            'in_progress' => $myWorkOrders->where('work_status', 'in_progress')->count(),
            'completed' => $myWorkOrders->where('work_status', 'completed')->count(),
        ];

        return view('dashboard.petugas', compact('myWorkOrders', 'stats'));
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
