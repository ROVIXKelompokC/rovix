@extends('layouts.app')

@section('title', 'Portal Pelapor — ROVIX')
@section('header', 'Portal Publik — Pelaporan & Pemantauan Jalan Rusak')
@section('subheader', 'Pantau progres tindak lanjut laporan kerusakan jalan secara transparan')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#F4F1EA] p-5 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Total Laporan Saya</p>
        <p class="text-3xl font-bold font-mono-tech text-[#263B36] mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-5 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Menunggu Verifikasi</p>
        <p class="text-3xl font-bold font-mono-tech text-[#C76B45] mt-1">{{ $stats['submitted'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-5 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Dalam Penanganan</p>
        <p class="text-3xl font-bold font-mono-tech text-[#263B36] mt-1">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-5 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Selesai Diperbaiki</p>
        <p class="text-3xl font-bold font-mono-tech text-[#263B36] mt-1">{{ $stats['resolved'] }}</p>
    </div>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <div class="px-6 py-4 border-b border-[#263B36]/15 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Riwayat & Status Tiket Laporan</h3>
            <p class="text-[11px] text-[#353A37]/70 mt-0.5">Alur Status: Submitted &rarr; Verified &rarr; In Progress &rarr; Resolved</p>
        </div>
    </div>

    <div class="divide-y divide-[#263B36]/10">
        @forelse($myReports as $report)
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-[#E9E5DA]/50">
            <div>
                <div class="flex items-center gap-3 mb-1.5">
                    <span class="px-2 py-0.5 bg-[#263B36] text-[#E9E5DA] font-mono-tech font-semibold text-xs rounded">{{ $report->ticket_code }}</span>
                    <span class="text-xs font-mono-tech text-[#353A37]/70">{{ $report->created_at?->format('d/m/Y H:i') }}</span>
                    <span class="px-2 py-0.5 text-[11px] font-mono-tech font-medium rounded bg-[#A7B5A0]/30 text-[#263B36]">
                        Skor AI: {{ $report->ai_confidence }}%
                    </span>
                </div>
                <h4 class="font-semibold text-[#353A37] text-sm">{{ $report->description }}</h4>
                <p class="text-xs text-[#353A37]/75 mt-1">
                    Koordinat: <span class="font-mono-tech">{{ $report->latitude }}, {{ $report->longitude }}</span> • Wilayah: {{ $report->district->name ?? '-' }} • Kategori: {{ $report->category->category_name ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="px-2.5 py-1 rounded text-[11px] font-mono-tech font-semibold uppercase
                    {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                    {{ $report->status }}
                </span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-[#353A37]/60 text-xs">
            Anda belum memiliki riwayat laporan kerusakan jalan.
        </div>
        @endforelse
    </div>
</div>
@endsection
