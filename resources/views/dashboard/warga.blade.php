@extends('layouts.app')

@section('title', 'Portal Warga Pelapor — ROVIX')
@section('header', 'Portal Publik Warga — Pelaporan & Pelacakan Jalan Rusak')
@section('subheader', 'Laporkan titik kerusakan jalan dengan Geotagging GPS otomatis & pantau linimasa perbaikannya')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs text-[#263B36] font-semibold uppercase">Total Aduan Saya</p>
        <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs text-[#263B36] font-semibold uppercase">Menunggu Verifikasi</p>
        <p class="text-3xl font-extrabold text-[#C76B45] mt-1">{{ $stats['submitted'] }}</p>
    </div>
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs text-[#263B36] font-semibold uppercase">Sedang Diperbaiki</p>
        <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs text-[#263B36] font-semibold uppercase">Selesai Diperbaiki</p>
        <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['resolved'] }}</p>
    </div>
</div>

<div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-[#A7B5A0]/30 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-[#263B36] text-sm">📜 Riwayat & Linimasa Tiket Aduan Saya</h3>
            <p class="text-xs text-[#353A37]/75">Pantau progres penanganan secara transparan (Submitted → Verified → In Progress → Resolved)</p>
        </div>
    </div>

    <div class="divide-y divide-[#A7B5A0]/30">
        @forelse($myReports as $report)
        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-[#E9E5DA]/50">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="px-2.5 py-0.5 bg-[#263B36] text-[#E9E5DA] font-bold text-xs rounded">{{ $report->ticket_code }}</span>
                    <span class="text-xs text-[#353A37]/70">{{ $report->created_at?->format('d M Y H:i') }}</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-[#A7B5A0]/30 text-[#263B36] border border-[#A7B5A0]">
                        AI {{ $report->ai_validation ? 'Valid' : 'Review' }} ({{ $report->ai_confidence }}%)
                    </span>
                </div>
                <h4 class="font-bold text-[#353A37] text-sm">{{ $report->description }}</h4>
                <p class="text-xs text-[#353A37]/75 mt-1">
                    📍 Koordinat: {{ $report->latitude }}, {{ $report->longitude }} • Zona: {{ $report->district->name ?? '-' }} • Kategori: {{ $report->category->category_name ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase
                    {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                    {{ $report->status }}
                </span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-[#353A37]/60 text-sm">
            Anda belum memiliki riwayat laporan kerusakan jalan.
        </div>
        @endforelse
    </div>
</div>
@endsection
