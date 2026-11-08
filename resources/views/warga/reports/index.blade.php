@extends('layouts.app')

@section('title', 'Riwayat Laporan Aduan Saya — ROVIX')
@section('header', 'Riwayat Laporan Kerusakan Jalan')
@section('subheader', 'Pantau status verifikasi, disposisi SPK teknis, dan bukti penyelesaian perbaikan jalan Anda')

@section('content')
<!-- Ringkasan Statistik Status Laporan -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase tracking-wider">Total Laporan</p>
            <p class="text-2xl font-bold font-mono-tech text-[#263B36] mt-0.5">{{ $stats['total'] }}</p>
        </div>
        <div class="w-8 h-8 rounded bg-[#263B36]/10 flex items-center justify-center text-[#263B36]">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase tracking-wider">Menunggu</p>
            <p class="text-2xl font-bold font-mono-tech text-[#C76B45] mt-0.5">{{ $stats['submitted'] }}</p>
        </div>
        <div class="w-8 h-8 rounded bg-[#C76B45]/15 flex items-center justify-center text-[#C76B45]">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase tracking-wider">Diproses</p>
            <p class="text-2xl font-bold font-mono-tech text-[#263B36] mt-0.5">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="w-8 h-8 rounded bg-[#263B36]/10 flex items-center justify-center text-[#263B36]">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
    </div>
    <div class="bg-[#F4F1EA] px-4 py-3 rounded-lg border border-[#263B36]/15 flex items-baseline justify-between shadow-sm">
        <div>
            <p class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase tracking-wider">Selesai</p>
            <p class="text-2xl font-bold font-mono-tech text-emerald-700 mt-0.5">{{ $stats['resolved'] }}</p>
        </div>
        <div class="w-8 h-8 rounded bg-emerald-100 flex items-center justify-center text-emerald-700">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
</div>

<!-- Toolbar Pencarian & Filter Status -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-3.5 mb-4 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Form Filter & Search -->
        <form action="{{ route('warga.reports.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#353A37]/50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode tiket (mis. RVX-2026) atau deskripsi..."
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-[#263B36]/20 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
            </div>

            <select name="status" onchange="this.form.submit()"
                class="px-3 py-1.5 text-xs bg-white border border-[#263B36]/20 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status Tiket</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Menunggu Verifikasi (Submitted)</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi (Verified)</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diperbaiki (In Progress)</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai Diperbaiki (Resolved)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak / Spam (Rejected)</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-[#263B36] hover:bg-[#1e302b] text-white text-xs font-medium rounded transition">
                Filter
            </button>

            @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'all'))
                <a href="{{ route('warga.reports.index') }}" class="px-2.5 py-1.5 text-xs text-[#C76B45] hover:underline font-medium">
                    Reset
                </a>
            @endif
        </form>

        <!-- Tombol Buat Laporan Baru -->
        <a href="{{ route('warga.reports.create') }}"
            class="px-3.5 py-2 rounded text-xs font-bold bg-[#C76B45] hover:bg-[#a95734] text-white transition flex items-center justify-center gap-2 shadow-sm shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Buat Laporan Baru</span>
        </a>
    </div>
</div>

<!-- Daftar Riwayat Laporan -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden shadow-sm">
    <div class="px-4 sm:px-5 py-3 border-b border-[#263B36]/15 flex items-center justify-between gap-2 bg-[#E9E5DA]/40">
        <div>
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Daftar Tiket Aduan Kerusakan Jalan</h3>
            <p class="text-[11px] text-[#353A37]/70">Urutan laporan terbaru dari akun Anda</p>
        </div>
        <span class="text-xs font-mono-tech text-[#353A37]/75 font-semibold">
            Menampilkan {{ $reports->count() }} dari {{ $reports->total() }} Laporan
        </span>
    </div>

    <div class="divide-y divide-[#263B36]/10">
        @forelse($reports as $report)
        <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-[#E9E5DA]/50 transition">
            <!-- Kolom Konten & Bukti Foto -->
            <div class="flex items-start gap-3.5 min-w-0">
                <!-- Foto Thumbnail Mini -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded bg-[#263B36]/10 border border-[#263B36]/20 overflow-hidden shrink-0 relative group">
                    <img src="{{ $report->photo_src }}" alt="{{ $report->ticket_code }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                        onerror="this.onerror=null; this.src='https://placehold.co/160x160/263B36/E9E5DA?text=Foto+Jalan';">
                    @if($report->ai_validation)
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-emerald-500 border border-white" title="Tervalidasi AI Vision"></span>
                    @endif
                </div>

                <!-- Informasi Laporan -->
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                        <span class="px-2 py-0.5 bg-[#263B36] text-white font-mono-tech font-bold text-xs rounded">
                            {{ $report->ticket_code }}
                        </span>
                        <span class="text-[11px] font-mono-tech text-[#353A37]/70">
                            {{ $report->created_at?->format('d/m/Y H:i') }} WIB
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded bg-[#A7B5A0]/25 text-[#263B36] border border-[#A7B5A0]/40">
                            {{ $report->district->name ?? 'Kecamatan' }}
                        </span>
                        @if($report->category)
                            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold rounded bg-white text-[#353A37] border border-[#263B36]/20">
                                {{ $report->category->category_name }}
                                <span class="text-[#C76B45] font-bold">★ {{ $report->category->severity_level ?? 1 }}/5</span>
                            </span>
                        @endif
                        <span class="px-2 py-0.5 text-[10px] font-mono-tech font-bold rounded {{ $report->ai_validation ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                            AI Vision: {{ number_format($report->ai_confidence, 1) }}%
                        </span>
                    </div>

                    <h4 class="font-bold text-[#353A37] text-sm leading-snug line-clamp-2">
                        {{ $report->description }}
                    </h4>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-[#353A37]/75">
                        <span class="flex items-center gap-1 font-mono-tech text-[11px]">
                            <svg class="w-3.5 h-3.5 text-[#C76B45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ number_format($report->latitude, 5) }}, {{ number_format($report->longitude, 5) }}
                        </span>

                        @if($report->workOrder)
                            <span class="inline-flex items-center gap-1 font-mono-tech text-[11px] text-[#263B36] font-medium">
                                <svg class="w-3 h-3 text-[#263B36]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                SPK: {{ $report->workOrder->spk_number }}
                                @if($report->workOrder->officer)
                                    ({{ $report->workOrder->officer->name }})
                                @endif
                            </span>
                        @endif

                        @if($report->rating)
                            <span class="inline-flex items-center gap-1 font-mono-tech text-[11px] text-amber-600 font-semibold">
                                ★ Rating: {{ $report->rating->score }}/5
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kolom Status & Aksi -->
            <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-[#263B36]/10 shrink-0">
                <div>
                    @if($report->status === 'resolved')
                        <span class="px-2.5 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Selesai Diperbaiki
                        </span>
                    @elseif($report->status === 'in_progress')
                        <span class="px-2.5 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#263B36] text-white">
                            Sedang Diperbaiki
                        </span>
                    @elseif($report->status === 'verified')
                        <span class="px-2.5 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/30">
                            Terverifikasi
                        </span>
                    @elseif($report->status === 'rejected')
                        <span class="px-2.5 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-red-100 text-red-800 border border-red-300">
                            Ditolak / Spam
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#C76B45]/20 text-[#C76B45] border border-[#C76B45]/30">
                            Menunggu Verifikasi
                        </span>
                    @endif
                </div>

                <a href="{{ route('warga.reports.show', $report->id) }}"
                    class="px-3 py-1.5 rounded text-xs font-bold bg-white hover:bg-[#263B36] text-[#263B36] hover:text-white border border-[#263B36]/30 transition flex items-center gap-1 shadow-sm">
                    <span>Lihat Detail Tiket</span>
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <div class="w-14 h-14 rounded-full bg-[#263B36]/10 text-[#263B36] flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h4 class="font-bold text-sm text-[#263B36]">Belum Ada Laporan Ditemukan</h4>
            <p class="text-xs text-[#353A37]/70 mt-1 max-w-sm mx-auto">
                @if(request()->filled('search') || request()->filled('status'))
                    Tidak ada laporan aduan yang cocok dengan filter pencarian Anda. Coba reset filter.
                @else
                    Anda belum pernah membuat laporan aduan kerusakan jalan. Mari berkontribusi memperbaiki infrastruktur kota Anda!
                @endif
            </p>
            <div class="mt-4 flex items-center justify-center gap-2">
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('warga.reports.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded bg-[#263B36]/10 text-[#263B36] hover:bg-[#263B36]/20 transition">
                        Reset Filter
                    </a>
                @endif
                <a href="{{ route('warga.reports.create') }}" class="px-4 py-2 text-xs font-bold rounded bg-[#C76B45] hover:bg-[#a95734] text-white transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Buat Laporan Sekarang</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($reports->hasPages())
    <div class="px-4 py-3 border-t border-[#263B36]/15 bg-[#E9E5DA]/30">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection

