@extends('layouts.app')

@section('title', 'Riwayat Pengerjaan SPK — ROVIX')
@section('header', 'Riwayat Pengerjaan SPK (History)')
@section('subheader', 'Daftar Surat Perintah Kerja (SPK) yang telah selesai dikerjakan di lapangan')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Total Selesai</p>
        <p class="text-xl font-bold font-mono-tech text-[#263B36]">{{ $stats['completed'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Sedang Dikerjakan</p>
        <p class="text-xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">SPK Ditugaskan</p>
        <p class="text-xl font-bold font-mono-tech text-[#263B36]">{{ $stats['assigned'] }}</p>
    </div>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <div class="px-4 py-3 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Arsip Riwayat SPK Selesai ({{ auth()->user()->name }})</h3>
        </div>
        <a href="{{ route('petugas.dashboard') }}" class="px-3 py-1 bg-[#263B36] text-white rounded text-xs font-semibold hover:bg-[#1d2e2a] transition">
            &larr; Kembali ke Tugas Aktif
        </a>
    </div>

    <div class="divide-y divide-[#263B36]/10">
        @forelse($historyWorkOrders as $wo)
        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#E9E5DA]/50">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 bg-[#263B36] text-[#E9E5DA] font-mono-tech font-semibold text-xs rounded">{{ $wo->spk_number }}</span>
                    <span class="px-2 py-0.5 bg-[#A7B5A0]/40 text-[#263B36] font-mono-tech font-semibold text-[10px] uppercase rounded">COMPLETED</span>
                    <span class="text-xs font-mono-tech text-[#353A37]/70">Selesai: {{ $wo->updated_at?->format('d/m/Y H:i') }}</span>
                </div>
                <p class="font-semibold text-[#353A37] text-sm">{{ $wo->report->description ?? '-' }}</p>
                <p class="text-xs text-[#353A37]/75 mt-0.5">
                    Tiket: <span class="font-mono-tech">{{ $wo->report->ticket_code ?? '-' }}</span> • Wilayah: {{ $wo->report->district->name ?? '-' }} • Kategori: {{ $wo->report->category->category_name ?? '-' }}
                </p>
                @if($wo->repair_notes)
                <p class="text-xs text-[#263B36] mt-1.5 bg-[#E9E5DA] px-2.5 py-1.5 rounded border border-[#263B36]/10">
                    <strong>Catatan Perbaikan:</strong> {{ $wo->repair_notes }}
                </p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($wo->report && $wo->report->rating)
                    <span class="px-2.5 py-1 rounded bg-[#A7B5A0]/30 text-[#263B36] font-mono-tech font-semibold text-xs">
                        Rating Warga: {{ $wo->report->rating->score }}/5
                    </span>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-[#353A37]/60 text-xs">
            Belum ada riwayat SPK yang diselesaikan.
        </div>
        @endforelse
    </div>
</div>
@endsection
