@extends('layouts.app')

@section('title', 'Rating & Evaluasi Kepuasan Warga — ROVIX')
@section('header', 'Evaluasi & Rating Kepuasan Warga')
@section('subheader', 'Rekapitulasi penilaian skala 1–5 dan ulasan masyarakat atas penyelesaian perbaikan jalan')

@section('content')
<div class="space-y-4">
    @if(isset($unratedResolvedReports) && $unratedResolvedReports->isNotEmpty())
    <div class="bg-[#F4F1EA] rounded-lg border border-[#C76B45]/40 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 mb-3 border-b border-[#263B36]/10">
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">Laporan Selesai yang Menunggu Penilaian Anda</h3>
                <p class="text-[11px] text-[#353A37]/75">Berikan penilaian atas hasil perbaikan jalan pada tiket laporan Anda yang telah berstatus Resolved.</p>
            </div>
            <span class="px-2.5 py-0.5 rounded bg-[#C76B45] text-white font-mono-tech text-[10px] font-semibold uppercase self-start sm:self-auto">
                {{ $unratedResolvedReports->count() }} Tiket Siap Dinilai
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($unratedResolvedReports as $rep)
            <div class="p-3.5 rounded bg-[#E9E5DA] border border-[#263B36]/15 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="font-mono-tech font-bold text-xs text-[#263B36]">{{ $rep->ticket_code }}</span>
                    <p class="text-xs font-semibold text-[#353A37] truncate mt-0.5">{{ $rep->description }}</p>
                    <p class="text-[11px] text-[#353A37]/70">{{ $rep->district->name ?? '-' }} • {{ $rep->category->category_name ?? '-' }}</p>
                </div>
                <a href="{{ route('ratings.create', $rep->id) }}"
                    class="px-3 py-1.5 rounded bg-[#C76B45] hover:bg-[#b25c39] text-white text-xs font-semibold transition shrink-0">
                    Beri Rating
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-4 py-3 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
            <h3 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">Daftar Evaluasi & Rating Kepuasan Warga</h3>
            <span class="font-mono-tech text-xs text-[#353A37]/75">Total: <strong>{{ $ratings->count() }}</strong> Ulasan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Waktu</th>
                        <th class="px-4 py-2.5">Kode Tiket & Lokasi</th>
                        <th class="px-4 py-2.5">Pelapor</th>
                        <th class="px-4 py-2.5">Skor Rating</th>
                        <th class="px-4 py-2.5">Ulasan Masyarakat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($ratings as $rating)
                        <tr class="hover:bg-[#E9E5DA]/60">
                            <td class="px-4 py-3 font-mono-tech text-[11px] text-[#353A37] whitespace-nowrap">
                                {{ $rating->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono-tech font-bold text-[#263B36]">{{ $rating->report->ticket_code ?? '#' . $rating->report_id }}</span>
                                <p class="text-[11px] text-[#353A37]/75 line-clamp-1">{{ $rating->report->description ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 font-semibold text-[#353A37] whitespace-nowrap">
                                {{ $rating->user->name ?? 'Warga Pelapor' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded bg-[#A7B5A0]/35 text-[#263B36] font-mono-tech font-bold text-xs">
                                    {{ $rating->score }} / 5
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[#353A37]">
                                {{ $rating->review_text ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#353A37]/60">Belum ada ulasan yang dikirimkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
