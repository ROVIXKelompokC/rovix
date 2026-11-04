@extends('layouts.app')

@section('title', 'Detail Profil Pengguna — ROVIX')
@section('header', 'Detail Profil & Wewenang Hak Akses Pengguna')
@section('subheader', 'SCRUM-110 • Informasi akun, penugasan zona wilayah, dan riwayat aktivitas operasional')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#263B36] text-[#E9E5DA] font-extrabold text-2xl flex items-center justify-center shadow">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-xl font-bold text-[#263B36]">{{ $user->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-[#C76B45] text-white">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-sm text-[#353A37]">{{ $user->email }} • 📞 {{ $user->phone_number }}</p>
                <p class="text-xs text-[#353A37]/75 mt-1">
                    📍 Zona Wilayah: <strong>{{ $user->district->name ?? 'Umum / Seluruh Wilayah' }}</strong>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-bold rounded-lg text-xs shadow-sm">
                ✏️ Edit Pengguna
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-[#A7B5A0]/30 hover:bg-[#A7B5A0]/50 text-[#263B36] font-semibold rounded-lg text-xs">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-6">
            <h4 class="font-bold text-sm text-[#263B36] mb-3">📋 Riwayat Laporan Aduan ({{ $user->reports->count() }})</h4>
            <div class="space-y-2.5 text-xs">
                @forelse($user->reports as $r)
                    <div class="p-3 rounded-lg bg-[#E9E5DA] border border-[#A7B5A0]/40 flex justify-between gap-2">
                        <div>
                            <span class="font-bold text-[#263B36]">{{ $r->ticket_code }}</span> — {{ $r->description }}
                        </div>
                        <span class="uppercase font-semibold text-[#C76B45] shrink-0">{{ $r->status }}</span>
                    </div>
                @empty
                    <p class="text-[#353A37]/60">Tidak ada riwayat laporan aduan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-6">
            <h4 class="font-bold text-sm text-[#263B36] mb-3">🛠️ Riwayat Penugasan SPK ({{ $user->workOrders->count() }})</h4>
            <div class="space-y-2.5 text-xs">
                @forelse($user->workOrders as $wo)
                    <div class="p-3 rounded-lg bg-[#E9E5DA] border border-[#A7B5A0]/40 flex justify-between gap-2">
                        <div>
                            <span class="font-bold text-[#263B36]">{{ $wo->spk_number }}</span> — {{ $wo->report->description ?? '-' }}
                        </div>
                        <span class="uppercase font-semibold text-[#263B36] shrink-0">{{ $wo->work_status }}</span>
                    </div>
                @empty
                    <p class="text-[#353A37]/60">Tidak ada riwayat penugasan SPK.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
