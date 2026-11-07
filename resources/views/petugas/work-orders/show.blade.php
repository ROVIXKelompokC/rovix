@extends('layouts.app')

@section('title', 'Detail SPK ' . $workOrder->spk_number . ' — ROVIX')
@section('header', 'Detail SPK ' . $workOrder->spk_number)
@section('subheader', 'Kondisi awal lokasi, batas waktu SLA, instruksi admin, dan navigasi menuju titik perbaikan')

@section('content')
@php
    $r = $workOrder->report;
    $photoSrc = $r && $r->photo_url
        ? (str_starts_with((string) $r->photo_url, 'http') ? $r->photo_url : asset('storage/' . ltrim((string) $r->photo_url, '/')))
        : null;
    $severity = (int) ($r->category->severity_level ?? 1);
    $priority = [5 => 'Kritis', 4 => 'Tinggi', 3 => 'Sedang', 2 => 'Rendah', 1 => 'Sangat Rendah'][$severity] ?? '-';
    $overdue = $workOrder->work_status !== 'completed' && $workOrder->target_sla && $workOrder->target_sla->isPast() && ! $workOrder->target_sla->isToday();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="font-mono-tech font-bold text-sm text-[#263B36]">{{ $workOrder->spk_number }}</span>
            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded uppercase
                {{ $workOrder->work_status === 'completed' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($workOrder->work_status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                {{ $workOrder->work_status }}
            </span>
        </div>

        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60 mb-1.5">Foto Kondisi Awal (Before)</p>
            @if($photoSrc)
                <img src="{{ $photoSrc }}" alt="Foto kondisi awal {{ $r->ticket_code }}"
                    class="w-full max-h-80 object-cover rounded border border-[#263B36]/15 bg-[#E9E5DA]">
            @else
                <div class="p-6 text-center text-xs text-[#353A37]/60 rounded border border-dashed border-[#263B36]/25 bg-[#E9E5DA]">Foto kondisi awal tidak tersedia.</div>
            @endif
        </div>

        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Deskripsi Kerusakan</p>
            <p class="text-sm font-semibold text-[#353A37] mt-1">{{ $r->description ?? '-' }}</p>
            <p class="text-xs text-[#353A37]/70 mt-1">
                Tiket <span class="font-mono-tech font-semibold">{{ $r->ticket_code ?? '-' }}</span>
                • {{ $r->district->name ?? '-' }} • {{ $r->category->category_name ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Instruksi Admin</p>
            <p class="text-xs text-[#353A37] mt-1 p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10">{{ $workOrder->repair_notes ?: 'Tidak ada instruksi khusus.' }}</p>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 space-y-4 text-xs">
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Batas Waktu (SLA)</p>
            <p class="font-mono-tech font-bold text-sm text-[#263B36] mt-1">
                {{ $workOrder->target_sla?->format('d/m/Y') }}
                @if($overdue)<span class="ml-1 px-1.5 py-0.5 rounded bg-[#C76B45] text-white text-[9px] font-semibold">TERLAMBAT</span>@endif
            </p>
        </div>
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Prioritas</p>
            <p class="font-semibold text-[#353A37] mt-1">{{ $priority }} ({{ $severity }}/5)</p>
        </div>
        @if($r)
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Koordinat Lokasi</p>
            <p class="font-mono-tech mt-1">{{ $r->latitude }}, {{ $r->longitude }}</p>
        </div>
        @endif

        <div class="pt-3 border-t border-[#263B36]/10 flex flex-col gap-2">
            @if($r)
            <a target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $r->latitude }},{{ $r->longitude }}"
                class="px-3 py-2.5 bg-[#C76B45] hover:bg-[#b25c39] text-white rounded font-semibold text-center transition">Navigasi Google Maps</a>
            @endif
            <a href="{{ route('petugas.dashboard') }}" class="px-3 py-2 bg-[#263B36] text-white rounded font-semibold text-center">&larr; Kembali ke Daftar Tugas</a>
        </div>
    </div>
</div>
@endsection
