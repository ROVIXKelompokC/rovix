@extends('layouts.app')

@section('title', 'Portal Warga Pelapor — ROVIX')
@section('header', 'Portal Publik Warga — Pelaporan & Pelacakan Jalan Rusak')
@section('subheader', 'Laporkan titik kerusakan jalan dengan Geotagging GPS otomatis & pantau linimasa perbaikannya')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase">Total Aduan Saya</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase">Menunggu Verifikasi</p>
        <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['submitted'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase">Sedang Diperbaiki</p>
        <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase">Selesai Diperbaiki</p>
        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['resolved'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-800 text-sm">📜 Riwayat & Linimasa Tiket Aduan Saya</h3>
            <p class="text-xs text-slate-500">Pantau progres penanganan secara transparan (Submitted → Verified → In Progress → Resolved)</p>
        </div>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($myReports as $report)
        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="px-2.5 py-0.5 bg-slate-900 text-amber-400 font-bold text-xs rounded">{{ $report->ticket_code }}</span>
                    <span class="text-xs text-slate-400">{{ $report->created_at?->format('d M Y H:i') }}</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                        AI {{ $report->ai_validation ? 'Valid' : 'Review' }} ({{ $report->ai_confidence }}%)
                    </span>
                </div>
                <h4 class="font-bold text-slate-800 text-sm">{{ $report->description }}</h4>
                <p class="text-xs text-slate-400 mt-1">
                    📍 Koordinat: {{ $report->latitude }}, {{ $report->longitude }} • Zona: {{ $report->district->name ?? '-' }} • Kategori: {{ $report->category->category_name ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase
                    {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : ($report->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $report->status }}
                </span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-slate-400 text-sm">
            Anda belum memiliki riwayat laporan kerusakan jalan.
        </div>
        @endforelse
    </div>
</div>
@endsection
