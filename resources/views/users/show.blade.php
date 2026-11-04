@extends('layouts.app')

@section('title', 'Detail Profil Pengguna — ROVIX')
@section('header', 'Detail Profil & Wewenang Hak Akses Pengguna')
@section('subheader', 'SCRUM-110 • Informasi akun, penugasan zona wilayah, dan riwayat aktivitas operasional')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-900 text-amber-400 font-extrabold text-2xl flex items-center justify-center">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">{{ $user->email }} • 📞 {{ $user->phone_number }}</p>
                <p class="text-xs text-slate-400 mt-1">
                    📍 Zona Wilayah: <strong>{{ $user->district->name ?? 'Umum / Seluruh Wilayah' }}</strong>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-amber-500 text-slate-950 font-bold rounded-lg text-xs">
                ✏️ Edit Pengguna
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-lg text-xs">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="font-bold text-sm text-slate-800 mb-3">📋 Riwayat Laporan Aduan ({{ $user->reports->count() }})</h4>
            <div class="space-y-2.5 text-xs">
                @forelse($user->reports as $r)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-800">{{ $r->ticket_code }}</span> — {{ $r->description }}
                        </div>
                        <span class="uppercase font-semibold text-amber-700 shrink-0">{{ $r->status }}</span>
                    </div>
                @empty
                    <p class="text-slate-400">Tidak ada riwayat laporan aduan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="font-bold text-sm text-slate-800 mb-3">🛠️ Riwayat Penugasan SPK ({{ $user->workOrders->count() }})</h4>
            <div class="space-y-2.5 text-xs">
                @forelse($user->workOrders as $wo)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-800">{{ $wo->spk_number }}</span> — {{ $wo->report->description ?? '-' }}
                        </div>
                        <span class="uppercase font-semibold text-indigo-700 shrink-0">{{ $wo->work_status }}</span>
                    </div>
                @empty
                    <p class="text-slate-400">Tidak ada riwayat penugasan SPK.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
