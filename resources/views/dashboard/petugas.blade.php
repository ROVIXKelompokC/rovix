@extends('layouts.app')

@section('title', 'Penugasan Lapangan — ROVIX')
@section('header', 'Panel Kerja Petugas Teknis Lapangan')
@section('subheader', 'Daftar Surat Perintah Kerja (SPK) Aktif, Koordinat Lokasi, & Instruksi Penanganan')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">SPK Ditugaskan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['assigned'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Dalam Pengerjaan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Selesai Dikerjakan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['completed'] }}</p>
    </div>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5">
    <div class="pb-3 mb-4 border-b border-[#263B36]/10 flex flex-wrap items-center justify-between gap-2">
        <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Daftar Surat Perintah Kerja ({{ auth()->user()->name }})</h3>
        <span class="font-mono-tech text-xs text-[#353A37]/70">{{ $myWorkOrders->count() }} PENUGASAN</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($myWorkOrders as $wo)
        <div class="p-4 rounded-lg border border-[#263B36]/15 bg-[#E9E5DA]/50 flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                    <span class="font-mono-tech font-bold text-xs text-[#263B36]">{{ $wo->spk_number }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded bg-[#A7B5A0]/40 text-[#263B36] uppercase">
                        {{ $wo->work_status }}
                    </span>
                </div>
                <p class="font-semibold text-[#353A37] text-sm">{{ $wo->report->description ?? '-' }}</p>
                <div class="mt-2 space-y-1 text-xs text-[#353A37]/75">
                    <p>Wilayah: <strong>{{ $wo->report->district->name ?? '-' }}</strong> • Kategori: {{ $wo->report->category->category_name ?? '-' }}</p>
                    <p>Batas Waktu SLA: <span class="font-mono-tech font-semibold text-[#263B36]">{{ $wo->target_sla?->format('d/m/Y') }}</span></p>
                </div>
                <div class="text-xs text-[#353A37] mt-3 bg-[#F4F1EA] p-3 rounded border border-[#263B36]/10">
                    <span class="font-mono-tech text-[10px] uppercase text-[#263B36]/70 block mb-0.5">Instruksi Teknis</span>
                    {{ $wo->repair_notes ?? 'Lakukan perbaikan struktur perkerasan jalan sesuai standar teknis.' }}
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[#263B36]/10 flex flex-wrap items-center justify-between gap-2.5">
                @if($wo->report)
                <span class="font-mono-tech text-[11px] text-[#353A37]/70">{{ $wo->report->latitude }}, {{ $wo->report->longitude }}</span>
                <a target="_blank" href="https://www.google.com/maps?q={{ $wo->report->latitude }},{{ $wo->report->longitude }}"
                    class="px-3.5 py-2 bg-[#C76B45] text-white rounded text-xs font-semibold hover:bg-[#b25c39] transition text-center w-full sm:w-auto">
                    Buka Koordinat Peta &rarr;
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-8 text-[#353A37]/60 text-xs">
            Belum ada penugasan SPK aktif untuk akun Anda.
        </div>
        @endforelse
    </div>
</div>
@endsection
