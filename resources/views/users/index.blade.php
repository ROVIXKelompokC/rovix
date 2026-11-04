@extends('layouts.app')

@section('title', 'Manajemen Pengguna & RBAC — ROVIX')
@section('header', 'Manajemen Akun Pengguna, Regu Petugas, & Role-Based Access Control (RBAC)')
@section('subheader', 'SCRUM-106 & SCRUM-110 • Kelola hak akses peran Warga, Petugas Lapangan, dan Admin Dinas PU')

@section('content')
<!-- Ringkasan Jumlah Peran (RBAC) -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500 font-medium">Total Semua Akun</p>
            <p class="text-2xl font-extrabold text-slate-800">{{ $roleCounts['all'] }}</p>
        </div>
        <span class="text-xl">👥</span>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
        <div>
            <p class="text-xs text-indigo-600 font-semibold">Admin Dinas PU</p>
            <p class="text-2xl font-extrabold text-indigo-700">{{ $roleCounts['admin'] }}</p>
        </div>
        <span class="text-xl">🛡️</span>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
        <div>
            <p class="text-xs text-amber-600 font-semibold">Petugas Lapangan</p>
            <p class="text-2xl font-extrabold text-amber-600">{{ $roleCounts['petugas'] }}</p>
        </div>
        <span class="text-xl">🛠️</span>
    </div>
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
        <div>
            <p class="text-xs text-emerald-600 font-semibold">Warga Pelapor</p>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $roleCounts['warga'] }}</p>
        </div>
        <span class="text-xl">🏠</span>
    </div>
</div>

<!-- Bar Pencarian, Filter Role RBAC, & Tombol Tambah Pengguna -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau nomor telepon..."
                class="px-3.5 py-2 rounded-lg border border-slate-300 text-xs w-full md:w-64 focus:ring-2 focus:ring-amber-500 outline-none">

            <select name="role" class="px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                <option value="">Semua Peran (RBAC)</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Dinas PU</option>
                <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                <option value="warga" {{ request('role') === 'warga' ? 'selected' : '' }}>Warga Pelapor</option>
            </select>

            <select name="district_id" class="px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white">
                <option value="">Semua Zona Wilayah</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) request('district_id') === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->city_code }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800">
                🔍 Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'district_id']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs hover:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.users.create') }}"
            class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs shadow-sm transition shrink-0">
            ➕ Tambah Akun Pengguna Baru
        </a>
    </div>
</div>

<!-- Tabel Daftar Pengguna -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">Nama & Email</th>
                    <th class="px-5 py-3.5">Peran Hak Akses (RBAC)</th>
                    <th class="px-5 py-3.5">Zona Wilayah</th>
                    <th class="px-5 py-3.5">Nomor Telepon</th>
                    <th class="px-5 py-3.5">Aktivitas</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                <tr class="hover:bg-slate-50/80">
                    <td class="px-5 py-3.5">
                        <div class="font-bold text-slate-800">{{ $u->name }}</div>
                        <div class="text-slate-400">{{ $u->email }}</div>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($u->role === 'admin')
                            <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[11px]">ADMIN DINAS</span>
                        @elseif($u->role === 'petugas')
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">PETUGAS LAPANGAN</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]">WARGA PELAPOR</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-slate-700">{{ $u->district->name ?? 'Seluruh Wilayah / Umum' }}</div>
                        @if($u->district)
                            <div class="text-slate-400 text-[11px]">Kode: {{ $u->district->city_code }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-slate-600">{{ $u->phone_number }}</td>
                    <td class="px-5 py-3.5 text-slate-500">
                        {{ $u->reports_count }} Aduan • {{ $u->work_orders_count }} SPK
                    </td>
                    <td class="px-5 py-3.5 text-right space-x-1.5">
                        <a href="{{ route('admin.users.show', $u) }}"
                            class="inline-block px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium">
                            Detail
                        </a>
                        <a href="{{ route('admin.users.edit', $u) }}"
                            class="inline-block px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg font-medium">
                            Edit
                        </a>
                        @if(auth()->id() !== $u->id)
                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg font-medium">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                        Tidak ada data pengguna yang sesuai dengan filter pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-slate-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
