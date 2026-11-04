@extends('layouts.app')

@section('title', 'Tugas SPK Petugas — ROVIX')
@section('header', 'Tugas SPK Lapangan')
@section('subheader', 'Daftar Surat Perintah Kerja (SPK) Aktif & Update Status Pengerjaan')

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        @forelse($myWorkOrders as $wo)
        <div class="p-4 rounded-lg border border-[#263B36]/15 bg-[#E9E5DA]/50 flex flex-col justify-between">
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

                <!-- Form Ubah Status Kerja Tiket Penugasan SPK -->
                <form action="{{ route('petugas.work-orders.update-status', $wo) }}" method="POST" class="mt-3 pt-3 border-t border-[#263B36]/10 space-y-2.5">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-[11px] font-semibold text-[#263B36] mb-1">Ubah Status Kerja SPK</label>
                        <select name="work_status" class="w-full px-3 py-1.5 rounded border border-[#263B36]/25 bg-white text-xs text-[#353A37] focus:border-[#C76B45] outline-none">
                            <option value="assigned" {{ $wo->work_status === 'assigned' ? 'selected' : '' }}>Assigned (Baru Ditugaskan)</option>
                            <option value="in_progress" {{ $wo->work_status === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan)</option>
                            <option value="completed" {{ $wo->work_status === 'completed' ? 'selected' : '' }}>Completed (Selesai Diperbaiki)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#263B36] mb-1">Catatan Pengerjaan Lapangan</label>
                        <input type="text" name="repair_notes" value="{{ old('repair_notes', $wo->repair_notes) }}"
                            placeholder="Catatan progres / material perbaikan..."
                            class="w-full px-3 py-1.5 rounded border border-[#263B36]/25 bg-white text-xs text-[#353A37] focus:border-[#C76B45] outline-none">
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        @if($wo->report)
                        <a target="_blank" href="https://www.google.com/maps?q={{ $wo->report->latitude }},{{ $wo->report->longitude }}"
                            class="px-3 py-1.5 bg-[#E9E5DA] text-[#263B36] border border-[#263B36]/20 rounded text-xs font-semibold hover:bg-[#A7B5A0]/30 transition text-center flex-1 sm:flex-none">
                            Buka Peta GPS
                        </a>
                        @endif
                        <button type="submit" class="px-3.5 py-1.5 bg-[#C76B45] hover:bg-[#b25c39] text-white rounded text-xs font-semibold transition text-center flex-1 sm:flex-none">
                            Simpan Status Kerja
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center py-6 text-[#353A37]/60 text-xs">
            Tidak ada tugas SPK aktif saat ini. Cek <a href="{{ route('petugas.history') }}" class="text-[#C76B45] font-semibold underline">Riwayat Pengerjaan</a> untuk melihat SPK yang telah selesai.
        </div>
        @endforelse
    </div>
</div>
@endsection
