@extends('layouts.app')

@section('title', 'Field View Petugas Lapangan — ROVIX')
@section('header', 'Field View — Antarmuka Kerja Petugas Teknis Lapangan')
@section('subheader', 'Daftar Surat Perintah Kerja (SPK) Aktif, Navigasi Titik Lokasi, & Unggah Bukti Fisik')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs font-semibold text-[#263B36] uppercase">SPK Baru Ditugaskan</p>
        <p class="text-3xl font-extrabold text-[#C76B45] mt-1">{{ $stats['assigned'] }}</p>
    </div>
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs font-semibold text-[#263B36] uppercase">Sedang Dikerjakan (In Progress)</p>
        <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F5F2EB] p-5 rounded-2xl border border-[#A7B5A0]/50 shadow-sm">
        <p class="text-xs font-semibold text-[#263B36] uppercase">Selesai Diperbaiki</p>
        <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['completed'] }}</p>
    </div>
</div>

<div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-6">
    <h3 class="font-bold text-[#263B36] text-base mb-4">🛠️ Daftar Penugasan SPK Anda ({{ auth()->user()->name }})</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($myWorkOrders as $wo)
        <div class="p-5 rounded-xl border border-[#A7B5A0]/50 hover:border-[#C76B45] transition bg-[#E9E5DA]/60">
            <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-sm text-[#263B36]">{{ $wo->spk_number }}</span>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#A7B5A0]/40 text-[#263B36] uppercase">
                    {{ $wo->work_status }}
                </span>
            </div>
            <p class="font-semibold text-[#353A37] text-sm">{{ $wo->report->description ?? '-' }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">📍 Zona: {{ $wo->report->district->name ?? '-' }} • Kategori: {{ $wo->report->category->category_name ?? '-' }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">⏳ Target SLA: <strong>{{ $wo->target_sla?->format('d M Y') }}</strong></p>
            <p class="text-xs text-[#353A37] mt-2 bg-[#F5F2EB] p-2.5 rounded-lg border border-[#A7B5A0]/40">
                Catatan Perbaikan / Instruksi: {{ $wo->repair_notes ?? 'Lakukan penambalan aspal sesuai SOP.' }}
            </p>
            <div class="mt-4 flex items-center gap-2">
                @if($wo->report)
                <a target="_blank" href="https://www.google.com/maps?q={{ $wo->report->latitude }},{{ $wo->report->longitude }}"
                    class="px-3 py-1.5 bg-[#C76B45] text-white rounded-lg text-xs font-semibold hover:bg-[#b25c39] transition shadow-sm">
                    🧭 Buka Navigasi Rute GPS
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-8 text-[#353A37]/60 text-sm">
            Belum ada penugasan SPK aktif untuk akun Anda.
        </div>
        @endforelse
    </div>
</div>
@endsection
