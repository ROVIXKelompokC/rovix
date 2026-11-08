<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // SCRUM-50 (Read): Menampilkan daftar ulasan & rekapitulasi rating
    public function index()
    {
        $ratings = Rating::with(['report.district', 'report.category', 'user'])->latest()->get();

        $unratedResolvedReports = collect();
        if (Auth::check() && Auth::user()->role === 'warga') {
            $unratedResolvedReports = Report::with(['district', 'category'])
                ->where('user_id', Auth::id())
                ->where('status', 'resolved')
                ->whereDoesntHave('rating')
                ->latest()
                ->get();
        }

        return view('ratings.index', compact('ratings', 'unratedResolvedReports'));
    }

    // SCRUM-46 (Create): Form rating untuk laporan berstatus 'resolved'
    public function create($report_id)
    {
        $report = Report::with(['district', 'category'])
            ->where('id', $report_id)
            ->where('status', 'resolved')
            ->firstOrFail();

        if ($report->rating) {
            return redirect()->route('warga.ratings.index')
                ->with('error', "Tiket {$report->ticket_code} sudah pernah diberikan penilaian.");
        }

        return view('ratings.create', compact('report'));
    }

    // SCRUM-46 (Create): Menyimpan rating bintang & ulasan ke database
    public function store(Request $request)
    {
        $request->validate([
            'report_id'   => 'required|exists:reports,id|unique:ratings,report_id',
            'score'       => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ], [
            'report_id.unique' => 'Tiket laporan ini sudah memiliki penilaian rating.',
            'score.required'   => 'Silakan pilih nilai rating 1 sampai 5.',
        ]);

        Rating::create([
            'report_id'   => $request->report_id,
            'user_id'     => Auth::id(),
            'score'       => $request->score,
            'review_text' => $request->review_text,
        ]);

        $redirectRoute = Auth::user()->role === 'admin' ? 'admin.ratings.index' : 'warga.ratings.index';

        return redirect()->route($redirectRoute)->with('success', 'Terima kasih, penilaian dan ulasan perbaikan jalan berhasil disimpan.');
    }
}
