@extends('layouts.app')

@section('title', 'Detail SPK ' . $workOrder->spk_number . ' — ROVIX')
@section('header', 'Detail SPK ' . $workOrder->spk_number)
@section('subheader', 'Rincian penugasan, laporan terkait, dan lokasi perbaikan')

@section('content')
@php $r = $workOrder->report; @endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="font-mono-tech font-bold text-sm text-[#263B36]">{{ $workOrder->spk_number }}</span>
            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded uppercase
                {{ $workOrder->work_status === 'completed' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($workOrder->work_status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                {{ $workOrder->work_status }}
            </span>
        </div>

        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Deskripsi Laporan</p>
            <p class="text-sm font-semibold text-[#353A37] mt-1">{{ $r->description ?? '-' }}</p>
            <p class="text-xs text-[#353A37]/70 mt-1">
                Tiket <span class="font-mono-tech font-semibold">{{ $r->ticket_code ?? '-' }}</span>
                • {{ $r->district->name ?? '-' }} • {{ $r->category->category_name ?? '-' }}
                @if($r?->category) • Prioritas: <strong>{{ [5=>'Kritis',4=>'Tinggi',3=>'Sedang',2=>'Rendah',1=>'Sangat Rendah'][(int) $r->category->severity_level] ?? '-' }}</strong> ({{ $r->category->severity_level }}/5) @endif
            </p>
            @if($r)
                <p class="text-xs text-[#353A37]/70 mt-1">Koordinat: <span class="font-mono-tech">{{ $r->latitude }}, {{ $r->longitude }}</span></p>
            @endif
        </div>

        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Catatan Perbaikan</p>
            <p class="text-xs text-[#353A37] mt-1 p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10">{{ $workOrder->repair_notes ?: 'Belum ada catatan.' }}</p>
        </div>

        @if($workOrder->after_photo_url)
        @php
            $afterPhotoSrc = str_starts_with((string) $workOrder->after_photo_url, 'http')
                ? $workOrder->after_photo_url
                : asset('storage/' . ltrim((string) $workOrder->after_photo_url, '/'));
        @endphp
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Bukti Foto Setelah Perbaikan</p>
                <a href="{{ $afterPhotoSrc }}" target="_blank" rel="noopener" class="text-[10px] font-mono-tech text-[#263B36] hover:underline font-semibold">Perbesar &rarr;</a>
            </div>
            <img src="{{ $afterPhotoSrc }}" alt="Bukti foto setelah perbaikan {{ $workOrder->spk_number }}"
                class="w-full h-56 object-cover rounded border border-[#263B36]/15"
                onerror="this.onerror=null; this.src='https://placehold.co/600x400/263B36/E9E5DA?text=Foto+Selesai+Diperbaiki';">
            <p class="text-[10px] font-mono-tech text-[#353A37]/55 mt-1">{{ $workOrder->after_photo_url }}</p>
        </div>
        @endif
    </div>

    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 space-y-4 text-xs">
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Petugas Pelaksana</p>
            <p class="font-semibold text-[#353A37] mt-1">{{ $workOrder->officer->name ?? '-' }}</p>
            <p class="font-mono-tech text-[#353A37]/70">{{ $workOrder->officer->phone_number ?? '' }}</p>
            <p class="text-[#353A37]/70">{{ $workOrder->officer->district->name ?? 'Lintas Wilayah' }}</p>
        </div>
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Target SLA</p>
            <p class="font-mono-tech font-semibold text-[#263B36] mt-1">{{ $workOrder->target_sla?->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Diterbitkan</p>
            <p class="font-mono-tech mt-1">{{ $workOrder->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        @if($workOrder->resolved_at)
        <div>
            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/60">Selesai</p>
            <p class="font-mono-tech mt-1">{{ $workOrder->resolved_at->format('d/m/Y H:i') }}</p>
        </div>
        @endif

        <div class="pt-3 border-t border-[#263B36]/10 flex flex-col gap-2">
            @if($r)
            <a target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $r->latitude }},{{ $r->longitude }}"
                class="px-3 py-2 bg-[#E9E5DA] text-[#263B36] border border-[#263B36]/20 rounded font-semibold text-center hover:bg-[#A7B5A0]/30">Buka di Google Maps</a>
            @endif
            <a href="{{ route('admin.work-orders.index') }}" class="px-3 py-2 bg-[#263B36] text-white rounded font-semibold text-center">&larr; Kembali ke Daftar SPK</a>
        </div>
    </div>
</div>
@endsection
