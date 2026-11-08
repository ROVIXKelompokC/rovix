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
        $ratings = Rating::with(['report', 'user'])->latest()->get();
        return view('ratings.index', compact('ratings'));
    }

    // SCRUM-46 (Create): Form rating untuk laporan berstatus 'resolved'
    public function create($report_id)
    {
        $report = Report::where('id', $report_id)
            ->where('status', 'resolved')
            ->firstOrFail();

        return view('ratings.create', compact('report'));
    }

    // SCRUM-46 (Create): Menyimpan rating bintang & ulasan ke database
    public function store(Request $request)
    {
        $request->validate([
            'report_id'   => 'required|exists:reports,id',
            'score'       => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        Rating::create([
            'report_id'   => $request->report_id,
            'user_id'     => Auth::id(),
            'score'       => $request->score,
            'review_text' => $request->review_text,
        ]);

        return redirect()->route('ratings.index')->with('success', 'Terima kasih atas ulasan dan rating Anda!');
    }
}