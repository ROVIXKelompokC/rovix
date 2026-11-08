@extends('layouts.app')

@section('title', 'Tugas SPK Petugas — ROVIX')
@section('header', 'Tugas SPK Lapangan')
@section('subheader', 'Daftar Surat Perintah Kerja (SPK) Aktif & Navigasi Lapangan')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">SPK Ditugaskan</p>
        <p class="text-xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['assigned'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Sedang Dikerjakan</p>
        <p class="text-xl font-bold font-mono-tech text-[#263B36]">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-2.5 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Selesai (Riwayat)</p>
        <a href="{{ route('petugas.history') }}" class="text-xl font-bold font-mono-tech text-[#263B36] hover:text-[#C76B45] transition">{{ $stats['completed'] }} &rarr;</a>
    </div>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4">
    <div class="pb-2.5 mb-3 border-b border-[#263B36]/10 flex flex-wrap items-center justify-between gap-2">
        <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Daftar Tugas SPK Aktif ({{ auth()->user()->name }})</h3>
        <a href="{{ route('petugas.history') }}" class="font-mono-tech text-xs text-[#C76B45] font-semibold hover:underline">Lihat Riwayat Selesai &rarr;</a>
    </div>

    <!-- Filter Status & Pencarian Tugas -->
    <div class="flex flex-col sm:flex-row gap-2.5 mb-3.5">
        <input type="text" id="spk-search" placeholder="Cari no. SPK, kode tiket, atau deskripsi..."
            class="px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs w-full sm:w-72 focus:border-[#C76B45] outline-none">
        <select id="spk-status" class="px-3 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-xs bg-white w-full sm:w-auto">
            <option value="">Semua Status</option>
            <option value="assigned">Assigned</option>
            <option value="in_progress">In Progress</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5" id="spk-list">
        @forelse($myWorkOrders as $wo)
        <div class="spk-card p-4 rounded-lg border border-[#263B36]/15 bg-[#E9E5DA]/50 flex flex-col justify-between"
            data-status="{{ $wo->work_status }}"
            data-search="{{ strtolower($wo->spk_number . ' ' . ($wo->report->ticket_code ?? '') . ' ' . ($wo->report->description ?? '')) }}">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="font-mono-tech font-bold text-xs text-[#263B36]">{{ $wo->spk_number }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded uppercase
                        {{ $wo->work_status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]' }}">
                        {{ $wo->work_status }}
                    </span>
                </div>
                <p class="font-semibold text-[#353A37] text-sm">{{ $wo->report->description ?? '-' }}</p>
                <div class="mt-1.5 space-y-0.5 text-xs text-[#353A37]/75">
                    <p>Tiket: <span class="font-mono-tech font-semibold text-[#263B36]">{{ $wo->report->ticket_code ?? '-' }}</span> • Wilayah: <strong>{{ $wo->report->district->name ?? '-' }}</strong></p>
                    <p>Kategori: {{ $wo->report->category->category_name ?? '-' }} • Target SLA: <span class="font-mono-tech font-semibold text-[#263B36]">{{ $wo->target_sla?->format('d/m/Y') }}</span></p>
                </div>

                <div class="mt-3 pt-3 border-t border-[#263B36]/10 flex flex-wrap items-center justify-end gap-2">
                    @if($wo->report)
                    <a target="_blank" href="https://www.google.com/maps/dir/?api=1&destination={{ $wo->report->latitude }},{{ $wo->report->longitude }}"
                        class="px-3 py-1.5 bg-[#E9E5DA] text-[#263B36] border border-[#263B36]/20 rounded text-xs font-semibold hover:bg-[#A7B5A0]/30 transition text-center flex-1 sm:flex-none">
                        Navigasi Google Maps
                    </a>
                    @endif
                    <a href="{{ route('petugas.work-orders.show', $wo) }}"
                        class="px-3 py-1.5 bg-[#263B36] text-white rounded text-xs font-semibold hover:bg-[#1d2e2a] transition text-center flex-1 sm:flex-none">
                        Detail SPK
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-6 text-[#353A37]/60 text-xs">
            Tidak ada tugas SPK aktif saat ini. Cek <a href="{{ route('petugas.history') }}" class="text-[#C76B45] font-semibold underline">Riwayat Pengerjaan</a> untuk melihat SPK yang telah selesai.
        </div>
        @endforelse
    </div>
    <p id="spk-empty" class="hidden text-center py-6 text-[#353A37]/60 text-xs">Tidak ada SPK yang cocok dengan pencarian atau filter.</p>
</div>

<script>
    (function () {
        var search = document.getElementById('spk-search');
        var status = document.getElementById('spk-status');
        var empty = document.getElementById('spk-empty');
        var cards = document.querySelectorAll('.spk-card');
        function apply() {
            var q = search.value.trim().toLowerCase();
            var st = status.value;
            var shown = 0;
            cards.forEach(function (c) {
                var ok = (!st || c.dataset.status === st) && (!q || c.dataset.search.indexOf(q) !== -1);
                c.style.display = ok ? '' : 'none';
                if (ok) shown++;
            });
            empty.classList.toggle('hidden', shown > 0 || cards.length === 0);
        }
        search.addEventListener('input', apply);
        status.addEventListener('change', apply);
    })();
</script>
@endsection
