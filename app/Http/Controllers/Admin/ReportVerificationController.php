<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Services\HaversineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportVerificationController extends Controller
{
    /**
     * SCRUM-9 (PBI Read):
     * Menampilkan antrean verifikasi laporan masuk, peta sebaran titik kerusakan jalan
     * berbasis Web GIS (Leaflet.js), deteksi kedekatan duplikasi (Haversine < 30m),
     * serta riwayat aktivitas Audit Log.
     */
    public function index(Request $request, HaversineService $haversineService)
    {
        $stats = [
            'total' => Report::count(),
            'submitted' => Report::where('status', 'submitted')->count(),
            'verified' => Report::where('status', 'verified')->count(),
            'in_progress' => Report::where('status', 'in_progress')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'ai_valid' => Report::where('ai_validation', true)->count(),
        ];

        $baseQuery = Report::with(['user', 'district', 'category', 'workOrder.officer'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('ticket_code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status') && $request->input('status') !== 'all', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('district_id'), function ($query) use ($request) {
                $query->where('district_id', $request->input('district_id'));
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->input('category_id'));
            });

        // Data ringan untuk marker Peta Interaktif Leaflet.js (FR-06)
        $mapReports = (clone $baseQuery)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(function (Report $report) {
                return [
                    'id' => $report->id,
                    'ticket_code' => $report->ticket_code,
                    'description' => Str::limit($report->description, 110),
                    'photo_url' => $report->photo_url,
                    'latitude' => (float) $report->latitude,
                    'longitude' => (float) $report->longitude,
                    'ai_validation' => (bool) $report->ai_validation,
                    'ai_confidence' => (float) $report->ai_confidence,
                    'status' => $report->status,
                    'district_name' => $report->district->name ?? '-',
                    'city_code' => $report->district->city_code ?? '-',
                    'category_name' => $report->category->category_name ?? 'Kerusakan Jalan',
                    'severity_level' => (int) ($report->category->severity_level ?? 1),
                    'reporter_name' => $report->user->name ?? 'Warga Pelapor',
                    'created_at_formatted' => $report->created_at?->format('d/m/Y H:i') ?? '-',
                    'show_url' => route('admin.verification.show', $report->id),
                ];
            })
            ->values();

        // Urutkan agar laporan berstatus 'submitted' (menunggu verifikasi) tampil paling atas di antrean
        $reports = $baseQuery
            ->orderByRaw("CASE
                WHEN status = 'submitted' THEN 0
                WHEN status = 'verified' THEN 1
                WHEN status = 'in_progress' THEN 2
                WHEN status = 'resolved' THEN 3
                ELSE 4
            END")
            ->latest()
            ->paginate(8)
            ->withQueryString();

        // Hitung indikasi duplikasi spasial radius < 30 meter menggunakan HaversineService
        $reports->getCollection()->transform(function (Report $report) use ($haversineService) {
            if ($report->latitude && $report->longitude) {
                $nearby = $haversineService->findNearbyActiveReports(
                    (float) $report->latitude,
                    (float) $report->longitude,
                    HaversineService::DEFAULT_RADIUS_METERS,
                    $report->id
                );
                $report->setAttribute('nearby_duplicates', $nearby);
            } else {
                $report->setAttribute('nearby_duplicates', collect());
            }

            return $report;
        });

        // 10 Riwayat Audit Log terbaru untuk panel Audit Trail (SCRUM-8 & SCRUM-9)
        $auditLogs = AuditLog::with(['user', 'report.district'])
            ->latest('created_at')
            ->take(10)
            ->get();

        $districts = District::orderBy('name')->get();
        $categories = ReportCategory::orderByDesc('severity_level')->get();

        return view('admin.verification.index', compact(
            'stats',
            'reports',
            'mapReports',
            'auditLogs',
            'districts',
            'categories'
        ));
    }

    /**
     * SCRUM-9 (PBI Read Detail):
     * Menampilkan halaman detail tinjauan satu laporan kerusakan jalan,
     * pratinjau foto & skor AI Vision, radius duplikasi Haversine 30m, dan riwayat Audit Log tiket.
     */
    public function show($id, HaversineService $haversineService)
    {
        $report = Report::with([
            'user',
            'district',
            'category',
            'workOrder.officer',
            'rating',
            'auditLogs.user',
        ])->findOrFail($id);

        $nearbyReports = collect();
        if ($report->latitude && $report->longitude) {
            $nearbyReports = $haversineService->findNearbyActiveReports(
                (float) $report->latitude,
                (float) $report->longitude,
                HaversineService::DEFAULT_RADIUS_METERS,
                $report->id
            );
        }

        return view('admin.verification.show', compact('report', 'nearbyReports'));
    }

    /**
     * SCRUM-8 (PBI Create Verification & Audit Log):
     * Menyetujui / memverifikasi laporan kerusakan jalan warga ('submitted' -> 'verified')
     * dan otomatis mencatat rekam jejak ke tabel audit_logs dalam satu transaksi database.
     */
    public function approve(Request $request, $id)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin Dinas PU yang berwenang memverifikasi laporan.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        $report = Report::findOrFail($id);

        if (in_array($report->status, ['in_progress', 'resolved'], true)) {
            return back()->with('error', "Tiket {$report->ticket_code} sudah dalam tahap pengerjaan/selesai dan tidak dapat diverifikasi ulang.");
        }

        $previousStatus = $report->status;
        $notes = trim((string) ($validated['notes'] ?? ''));
        $actionName = $notes !== ''
            ? Str::limit("VERIFIKASI_LAPORAN: {$notes}", 100, '')
            : 'VERIFIKASI_LAPORAN';

        DB::transaction(function () use ($report, $previousStatus, $actionName) {
            $report->update([
                'status' => 'verified',
            ]);

            AuditLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_name' => $actionName,
                'previous_status' => $previousStatus,
                'new_status' => 'verified',
                'created_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            "Tiket laporan {$report->ticket_code} berhasil diverifikasi (VERIFIED) dan telah dicatat ke dalam Audit Log."
        );
    }

    /**
     * SCRUM-8 (PBI Create Rejection & Audit Log):
     * Menolak laporan indikasi spam / tidak valid / duplikat ('submitted' -> 'rejected')
     * dan otomatis mencatat rekam jejak beserta alasan penolakan ke tabel audit_logs.
     */
    public function reject(Request $request, $id)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin Dinas PU yang berwenang menolak laporan.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $reason = trim((string) ($validated['reason'] ?? $validated['notes'] ?? ''));
        if ($reason === '') {
            return back()
                ->withErrors(['reason' => 'Alasan penolakan laporan wajib diisi.'])
                ->with('error', 'Mohon cantumkan alasan penolakan laporan (contoh: foto spam, tidak relevan, atau duplikat).');
        }

        $report = Report::findOrFail($id);

        if ($report->status === 'resolved') {
            return back()->with('error', "Tiket {$report->ticket_code} yang sudah selesai diperbaiki (RESOLVED) tidak dapat ditolak.");
        }

        $previousStatus = $report->status;
        $actionName = Str::limit("TOLAK_LAPORAN_SPAM: {$reason}", 100, '');

        DB::transaction(function () use ($report, $previousStatus, $actionName) {
            $report->update([
                'status' => 'rejected',
            ]);

            AuditLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'action_name' => $actionName,
                'previous_status' => $previousStatus,
                'new_status' => 'rejected',
                'created_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            "Tiket laporan {$report->ticket_code} telah ditolak (REJECTED) dengan alasan: \"{$reason}\" dan tercatat di Audit Log."
        );
    }
}

