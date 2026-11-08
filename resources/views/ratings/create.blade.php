@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Berikan Rating & Ulasan Perbaikan Jalan</h2>
    <p class="text-gray-600 mb-6">Laporan Tiket: <strong>#{{ $report->id }}</strong> - {{ $report->title ?? 'Kerusakan Jalan' }}</p>

    <form action="{{ route('ratings.store') }}" method="POST">
        @csrf
        <input type="hidden" name="report_id" value="{{ $report->id }}">

        <div class="mb-4">
            <label class="block font-semibold mb-2">Rating Bintang (1 - 5):</label>
            <select name="score" required class="w-full border rounded p-2">
                <option value="">-- Pilih Rating --</option>
                <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                <option value="2">⭐⭐ (2 - Kurang Puas)</option>
                <option value="1">⭐ (1 - Buruk)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Ulasan / Masukan:</label>
            <textarea name="review_text" rows="4" class="w-full border rounded p-2" placeholder="Tuliskan ulasan Anda mengenai hasil perbaikan jalan..."></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim Penilaian</button>
    </form>
</div>
@endsection