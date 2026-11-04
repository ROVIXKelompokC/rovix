@extends('layouts.app')

@section('title', 'Rincian Profil Pengguna — ROVIX')
@section('header', 'Rincian Profil & Otorisasi Pengguna')
@section('subheader', 'Informasi identitas akun, penugasan wilayah kecamatan, dan rekam jejak operasional')

@section('content')
<div class="max-w-4xl space-y-4">
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded bg-[#263B36] text-[#E9E5DA] font-mono-tech font-bold text-base flex items-center justify-center shrink-0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-base font-bold text-[#263B36]">{{ $user->name }}</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono-tech font-semibold uppercase bg-[#C76B45] text-white">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-xs font-mono-tech text-[#353A37]/80 mt-0.5">{{ $user->email }} • {{ $user->phone_number }}</p>
                <p class="text-xs text-[#353A37]/75 mt-0.5">
                    Wilayah Tugas: <strong>{{ $user->district->name ?? 'Lintas Wilayah / Umum' }}</strong>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-3.5 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs text-center flex-1 sm:flex-none">
                Ubah Data
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 font-semibold rounded text-xs text-center flex-1 sm:flex-none">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4">
            <div class="pb-2.5 mb-3 border-b border-[#263B36]/10 flex items-center justify-between gap-2">
                <h4 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">Riwayat Laporan Aduan</h4>
                <span class="font-mono-tech text-xs text-[#353A37]/70">{{ $user->reports->count() }} TIKET</span>
            </div>
            <div class="space-y-2 text-xs">
                @forelse($user->reports as $r)
                    <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10 flex justify-between gap-3">
                        <div>
                            <span class="font-mono-tech font-bold text-[#263B36] block">{{ $r->ticket_code }}</span>
                            <span class="text-[#353A37]/85">{{ $r->description }}</span>
                        </div>
                        <span class="uppercase font-mono-tech font-semibold text-[10px] text-[#C76B45] shrink-0">{{ $r->status }}</span>
                    </div>
                @empty
                    <p class="text-[#353A37]/60 py-2">Tidak ada riwayat laporan aduan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4">
            <div class="pb-2.5 mb-3 border-b border-[#263B36]/10 flex items-center justify-between gap-2">
                <h4 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">Riwayat Penugasan SPK</h4>
                <span class="font-mono-tech text-xs text-[#353A37]/70">{{ $user->workOrders->count() }} SPK</span>
            </div>
            <div class="space-y-2 text-xs">
                @forelse($user->workOrders as $wo)
                    <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10 flex justify-between gap-3">
                        <div>
                            <span class="font-mono-tech font-bold text-[#263B36] block">{{ $wo->spk_number }}</span>
                            <span class="text-[#353A37]/85">{{ $wo->report->description ?? '-' }}</span>
                        </div>
                        <span class="uppercase font-mono-tech font-semibold text-[10px] text-[#263B36] shrink-0">{{ $wo->work_status }}</span>
                    </div>
                @empty
                    <p class="text-[#353A37]/60 py-2">Tidak ada riwayat penugasan SPK.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
