@extends('layouts.app')

@section('title', 'Disposisi SPK Digital — ROVIX')
@section('header', 'Disposisi Surat Perintah Kerja (SPK) Digital')
@section('subheader', 'Penerbitan SPK perbaikan jalan, pembagian beban kerja petugas, dan pemantauan target SLA')

@section('content')
<!-- Ringkasan SPK -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Total SPK</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Assigned</p>
        <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['assigned'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">In Progress</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Completed</p>
        <p class="text-2xl font-bold font-mono-tech text-[#353A37]">{{ $stats['completed'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#C76B45]/40 flex items-baseline justify-between col-span-2 lg:col-span-1">
        <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Siap Diterbitkan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['ready_to_dispatch'] }}</p>
    </div>
</div>

<!-- Pencarian, Filter, & Tombol Terbitkan SPK -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-3.5 mb-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <form action="{{ route('admin.work-orders.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no. SPK, kode tiket, atau nama petugas..."
                class="px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs w-full sm:w-64 focus:border-[#C76B45] outline-none">

            <select name="work_status" class="px-3 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-xs bg-white w-full sm:w-auto">
                <option value="">Semua Status SPK</option>
                @foreach(['assigned' => 'Assigned', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $val => $label)
                    <option value="{{ $val }}" {{ request('work_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="px-3.5 py-2 bg-[#263B36] text-white rounded text-xs font-semibold hover:bg-[#1d2e2a] transition flex-1 sm:flex-none">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'work_status']))
                    <a href="{{ route('admin.work-orders.index') }}" class="px-3 py-2 bg-[#A7B5A0]/30 text-[#263B36] rounded text-xs font-medium hover:bg-[#A7B5A0]/50 text-center flex-1 sm:flex-none">Reset</a>
                @endif
            </div>
        </form>

        <a href="{{ route('admin.work-orders.create') }}"
            class="px-4 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition text-center">
            + Terbitkan SPK Baru
        </a>
    </div>
</div>

<!-- Tabel SPK -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs min-w-[860px]">
            <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-2.5">Nomor SPK</th>
                    <th class="px-4 py-2.5">Laporan & Lokasi</th>
                    <th class="px-4 py-2.5">Petugas Pelaksana</th>
                    <th class="px-4 py-2.5">Target SLA</th>
                    <th class="px-4 py-2.5">Status</th>
                    <th class="px-4 py-2.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#263B36]/10">
                @forelse($workOrders as $wo)
                    @php
                        $overdue = $wo->work_status !== 'completed' && $wo->target_sla && $wo->target_sla->isPast() && ! $wo->target_sla->isToday();
                    @endphp
                    <tr class="hover:bg-[#E9E5DA]/40">
                        <td class="px-4 py-3 font-mono-tech font-semibold text-[#263B36]">{{ $wo->spk_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37]">{{ \Illuminate\Support\Str::limit($wo->report->description ?? '-', 70) }}</p>
                            <p class="text-[11px] text-[#353A37]/70 mt-0.5">
                                <span class="font-mono-tech">{{ $wo->report->ticket_code ?? '-' }}</span>
                                • {{ $wo->report->district->name ?? '-' }}
                                • {{ $wo->report->category->category_name ?? '-' }}
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37]">{{ $wo->officer->name ?? '-' }}</p>
                            <p class="text-[11px] font-mono-tech text-[#353A37]/70">{{ $wo->officer->phone_number ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 font-mono-tech">
                            {{ $wo->target_sla?->format('d/m/Y') }}
                            @if($overdue)
                                <span class="ml-1 px-1.5 py-0.5 rounded bg-[#C76B45] text-white text-[9px] font-semibold">TERLAMBAT</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded uppercase
                                {{ $wo->work_status === 'completed' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($wo->work_status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                                {{ $wo->work_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.work-orders.show', $wo) }}"
                                class="px-3 py-1.5 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 rounded text-[11px] font-semibold">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-[#353A37]/60">
                            Belum ada SPK yang cocok. Klik <strong>+ Terbitkan SPK Baru</strong> untuk membuat SPK dari laporan yang sudah terverifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($workOrders->hasPages())
        <div class="px-4 py-3 border-t border-[#263B36]/10">
            {{ $workOrders->links() }}
        </div>
    @endif
</div>
@endsection
