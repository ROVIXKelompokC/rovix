@extends('layouts.app')

@section('title', 'Portal Pelapor — ROVIX')
@section('header', 'Portal Publik — Pelaporan & Pemantauan Jalan Rusak')
@section('subheader', 'Pantau progres tindak lanjut laporan kerusakan jalan secara transparan')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Total Laporan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Menunggu</p>
        <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['submitted'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Diproses</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Selesai</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['resolved'] }}</p>
    </div>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <div class="px-4 sm:px-5 py-3 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Riwayat & Status Tiket Laporan</h3>
            <p class="text-[11px] text-[#353A37]/70">Alur Status: Submitted &rarr; Verified &rarr; In Progress &rarr; Resolved</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warga.reports.index') }}" class="px-3 py-1.5 rounded text-xs font-semibold bg-[#263B36]/10 hover:bg-[#263B36]/20 text-[#263B36] transition flex items-center gap-1.5">
                <span>Lihat Semua Riwayat</span>
            </a>
            <a href="{{ route('warga.reports.create') }}" class="px-3 py-1.5 rounded text-xs font-semibold bg-[#C76B45] hover:bg-[#a95734] text-white transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Buat Laporan</span>
            </a>
        </div>
    </div>

    <div class="divide-y divide-[#263B36]/10">
        @forelse($myReports as $report)
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#E9E5DA]/50">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
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
            <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                <span class="px-2.5 py-1 rounded text-[11px] font-mono-tech font-semibold uppercase
                    {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                    {{ $report->status }}
                </span>
                <a href="{{ route('warga.reports.show', $report->id) }}" class="p-1.5 rounded hover:bg-[#263B36]/10 text-[#263B36] transition" title="Lihat Detail Tiket">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
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
