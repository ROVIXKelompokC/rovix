<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\District;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Services\AIServiceAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * SCRUM-7: Riwayat Laporan Aduan Warga
     * Menampilkan daftar riwayat laporan milik warga yang sedang login
     * ($reports = auth()->user()->reports), dilengkapi filter status, pencarian, dan pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mengambil seluruh laporan milik warga untuk rekap status
        $allReports = $user->reports;

        $stats = [
            'total' => $allReports->count(),
            'submitted' => $allReports->where('status', 'submitted')->count(),
            'in_progress' => $allReports->whereIn('status', ['verified', 'in_progress'])->count(),
            'resolved' => $allReports->where('status', 'resolved')->count(),
            'rejected' => $allReports->where('status', 'rejected')->count(),
        ];

        // Query riwayat laporan dengan eager loading relasi untuk optimasi performa
        $reportsQuery = $user->reports()
            ->with(['district', 'category', 'workOrder.officer', 'rating'])
            ->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $reportsQuery->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $reportsQuery->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $reportsQuery->paginate(8)->withQueryString();

        return view('warga.reports.index', compact('reports', 'stats', 'allReports'));
    }

    /**
     * SCRUM-6: Form Buat Laporan Kerusakan Jalan
     * Menampilkan formulir pembuatan laporan kerusakan jalan baru
     * (foto, deskripsi, category_id, district_id, latitude, longitude).
     */
    public function create()
    {
        $user = Auth::user();
        $categories = ReportCategory::orderBy('severity_level', 'desc')->get();
        $districts = District::orderBy('name')->get();

        return view('warga.reports.create', compact('categories', 'districts', 'user'));
    }

    /**
     * SCRUM-6: Simpan Laporan Aduan & Simulasi Validasi AI
     * Memvalidasi masukan warga, mengunggah berkas foto, menjalankan analisis
     * AI Vision (AIServiceAdapter), membuat kode tiket unik, dan mencatat Audit Log.
     */
    public function store(Request $request, AIServiceAdapter $aiService)
    {
        $validated = $request->validate([
            'photo' => 'required_without:foto|nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'foto' => 'required_without:photo|nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'description' => 'required_without:deskripsi|nullable|string|min:10|max:1000',
            'deskripsi' => 'required_without:description|nullable|string|min:10|max:1000',
            'category_id' => 'required|exists:report_categories,id',
            'district_id' => 'required|exists:districts,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'photo.required_without' => 'Berkas foto bukti kerusakan jalan wajib diunggah.',
            'foto.required_without' => 'Berkas foto bukti kerusakan jalan wajib diunggah.',
            'photo.image' => 'Berkas foto harus berupa gambar valid.',
            'foto.image' => 'Berkas foto harus berupa gambar valid.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran berkas foto maksimal 10 MB.',
            'foto.max' => 'Ukuran berkas foto maksimal 10 MB.',
            'description.required_without' => 'Deskripsi kerusakan jalan wajib diisi.',
            'deskripsi.required_without' => 'Deskripsi kerusakan jalan wajib diisi.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter agar memudahkan identifikasi oleh petugas.',
            'deskripsi.min' => 'Deskripsi laporan minimal 10 karakter agar memudahkan identifikasi oleh petugas.',
            'category_id.required' => 'Pilih jenis kategori kerusakan jalan.',
            'category_id.exists' => 'Kategori kerusakan jalan yang dipilih tidak valid.',
            'district_id.required' => 'Pilih wilayah kecamatan lokasi kerusakan.',
            'district_id.exists' => 'Wilayah kecamatan yang dipilih tidak valid.',
            'latitude.required' => 'Titik koordinat lintang (Latitude) wajib ditentukan.',
            'longitude.required' => 'Titik koordinat bujur (Longitude) wajib ditentukan.',
            'latitude.between' => 'Titik koordinat lintang tidak valid (-90 s/d 90).',
            'longitude.between' => 'Titik koordinat bujur tidak valid (-180 s/d 180).',
        ]);

        $photoFile = $request->file('photo') ?? $request->file('foto');
        $description = trim((string) ($validated['description'] ?? $validated['deskripsi'] ?? ''));

        // Jalankan modul AI Computer Vision (Gemini Multimodal Vision / Fallback Heuristik Lokal ROVIX)
        $aiResult = $aiService->validateRoadDamageImage($photoFile);

        // Simpan foto bukti kerusakan jalan ke storage publik
        $photoPath = $photoFile->store('reports', 'public');

        // Buat nomor kode tiket unik berformat: RVX-YYYY-XXXX (contoh: RVX-2026-0004)
        $year = date('Y');
        $lastReport = Report::latest('id')->first();
        $nextNumber = $lastReport ? ((int) $lastReport->id + 1) : 1;
        $ticketCode = sprintf('RVX-%s-%04d', $year, $nextNumber);

        while (Report::where('ticket_code', $ticketCode)->exists()) {
            $nextNumber++;
            $ticketCode = sprintf('RVX-%s-%04d', $year, $nextNumber);
        }

        // Simpan transaksi laporan baru beserta rekam jejak Audit Log
        $report = DB::transaction(function () use ($validated, $ticketCode, $description, $photoPath, $aiResult) {
            $newReport = Report::create([
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'district_id' => $validated['district_id'],
                'ticket_code' => $ticketCode,
                'description' => $description,
                'photo_url' => $photoPath,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'ai_validation' => (bool) ($aiResult['ai_validation'] ?? false),
                'ai_confidence' => (float) ($aiResult['ai_confidence'] ?? 0.00),
                'status' => 'submitted',
            ]);

            AuditLog::create([
                'report_id' => $newReport->id,
                'user_id' => Auth::id(),
                'action_name' => 'LAPORAN_BARU_WARGA',
                'previous_status' => null,
                'new_status' => 'submitted',
                'created_at' => now(),
            ]);

            return $newReport;
        });

        $confidenceFormatted = number_format($report->ai_confidence, 1);
        $aiStatusText = $report->ai_validation
            ? "Citra berhasil divalidasi oleh AI Vision (Tingkat Keyakinan: {$confidenceFormatted}%)."
            : 'Citra ditandai untuk pemeriksaan manual oleh Admin Dinas PU.';

        return redirect()->route('warga.reports.show', $report->id)
            ->with('success', "Laporan aduan [{$report->ticket_code}] berhasil dikirim! {$aiStatusText}");
    }

    /**
     * SCRUM-7: Detail Status Tiket Laporan Warga
     * Menampilkan informasi komprehensif status tiket, hasil validasi AI Vision,
     * koordinat lokasi peta GIS, progres disposisi SPK teknis, dan timeline audit log.
     */
    public function show($id)
    {
        $report = Report::with([
            'user',
            'district',
            'category',
            'workOrder.officer',
            'rating',
            'auditLogs.user',
        ])->findOrFail($id);

        // Otorisasi RBAC: Warga hanya diperbolehkan mengakses tiket laporan miliknya sendiri
        if (Auth::user()->role !== 'admin' && (int) $report->user_id !== (int) Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rincian tiket laporan ini.');
        }

        return view('warga.reports.show', compact('report'));
    }
}

