@extends('layouts.app')

@section('title', 'Manajemen Pengguna & RBAC — ROVIX')
@section('header', 'Manajemen Akun Pengguna, Regu Petugas, & Role-Based Access Control (RBAC)')
@section('subheader', 'SCRUM-106 & SCRUM-110 • Kelola hak akses peran Warga, Petugas Lapangan, dan Admin Dinas PU')

@section('content')
<!-- Ringkasan Jumlah Peran (RBAC) -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#F5F2EB] p-4 rounded-xl border border-[#A7B5A0]/50 flex items-center justify-between">
        <div>
            <p class="text-xs text-[#263B36] font-semibold">Total Semua Akun</p>
            <p class="text-2xl font-extrabold text-[#263B36]">{{ $roleCounts['all'] }}</p>
        </div>
        <span class="text-xl">👥</span>
    </div>
    <div class="bg-[#F5F2EB] p-4 rounded-xl border border-[#A7B5A0]/50 flex items-center justify-between">
        <div>
            <p class="text-xs text-[#263B36] font-semibold">Admin Dinas PU</p>
            <p class="text-2xl font-extrabold text-[#263B36]">{{ $roleCounts['admin'] }}</p>
        </div>
        <span class="text-xl">🛡️</span>
    </div>
    <div class="bg-[#F5F2EB] p-4 rounded-xl border border-[#A7B5A0]/50 flex items-center justify-between">
        <div>
            <p class="text-xs text-[#C76B45] font-semibold">Petugas Lapangan</p>
            <p class="text-2xl font-extrabold text-[#C76B45]">{{ $roleCounts['petugas'] }}</p>
        </div>
        <span class="text-xl">🛠️</span>
    </div>
    <div class="bg-[#F5F2EB] p-4 rounded-xl border border-[#A7B5A0]/50 flex items-center justify-between">
        <div>
            <p class="text-xs text-[#353A37] font-semibold">Warga Pelapor</p>
            <p class="text-2xl font-extrabold text-[#353A37]">{{ $roleCounts['warga'] }}</p>
        </div>
        <span class="text-xl">🏠</span>
    </div>
</div>

<!-- Bar Pencarian, Filter Role RBAC, & Tombol Tambah Pengguna -->
<div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau nomor telepon..."
                class="px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-xs w-full md:w-64 focus:ring-2 focus:ring-[#C76B45] outline-none">

            <select name="role" class="px-3 py-2 rounded-lg border border-[#A7B5A0] text-[#353A37] text-xs bg-white">
                <option value="">Semua Peran (RBAC)</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Dinas PU</option>
                <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                <option value="warga" {{ request('role') === 'warga' ? 'selected' : '' }}>Warga Pelapor</option>
            </select>

            <select name="district_id" class="px-3 py-2 rounded-lg border border-[#A7B5A0] text-[#353A37] text-xs bg-white">
                <option value="">Semua Zona Wilayah</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) request('district_id') === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->city_code }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-[#263B36] text-white rounded-lg text-xs font-semibold hover:bg-[#1d2e2a] transition">
                🔍 Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'district_id']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-[#A7B5A0]/30 text-[#263B36] rounded-lg text-xs font-medium hover:bg-[#A7B5A0]/50">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.users.create') }}"
            class="px-4 py-2.5 bg-[#C76B45] hover:bg-[#b25c39] text-white font-bold rounded-xl text-xs shadow-sm transition shrink-0">
            ➕ Tambah Akun Pengguna Baru
        </a>
    </div>
</div>

<!-- Tabel Daftar Pengguna -->
<div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#263B36] text-[#E9E5DA] uppercase border-b border-[#A7B5A0]/40">
                <tr>
                    <th class="px-5 py-3.5">Nama & Email</th>
                    <th class="px-5 py-3.5">Peran Hak Akses (RBAC)</th>
                    <th class="px-5 py-3.5">Zona Wilayah</th>
                    <th class="px-5 py-3.5">Nomor Telepon</th>
                    <th class="px-5 py-3.5">Aktivitas</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#A7B5A0]/30">
                @forelse($users as $u)
                <tr class="hover:bg-[#E9E5DA]/60">
                    <td class="px-5 py-3.5">
                        <div class="font-bold text-[#263B36]">{{ $u->name }}</div>
                        <div class="text-[#353A37]/70">{{ $u->email }}</div>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($u->role === 'admin')
                            <span class="px-2.5 py-1 rounded-full bg-[#263B36] text-[#E9E5DA] font-bold text-[11px]">ADMIN DINAS</span>
                        @elseif($u->role === 'petugas')
                            <span class="px-2.5 py-1 rounded-full bg-[#C76B45] text-white font-bold text-[11px]">PETUGAS LAPANGAN</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-[#A7B5A0]/40 text-[#263B36] border border-[#A7B5A0] font-bold text-[11px]">WARGA PELAPOR</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-[#353A37]">{{ $u->district->name ?? 'Seluruh Wilayah / Umum' }}</div>
                        @if($u->district)
                            <div class="text-[#353A37]/70 text-[11px]">Kode: {{ $u->district->city_code }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-[#353A37]">{{ $u->phone_number }}</td>
                    <td class="px-5 py-3.5 text-[#353A37]/80">
                        {{ $u->reports_count }} Aduan • {{ $u->work_orders_count }} SPK
                    </td>
                    <td class="px-5 py-3.5 text-right space-x-1.5">
                        <a href="{{ route('admin.users.show', $u) }}"
                            class="inline-block px-2.5 py-1 bg-[#A7B5A0]/30 hover:bg-[#A7B5A0]/50 text-[#263B36] rounded-lg font-semibold">
                            Detail
                        </a>
                        <a href="{{ route('admin.users.edit', $u) }}"
                            class="inline-block px-2.5 py-1 bg-[#263B36] hover:bg-[#1d2e2a] text-white rounded-lg font-semibold">
                            Edit
                        </a>
                        @if(auth()->id() !== $u->id)
                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 bg-[#C76B45] hover:bg-[#b25c39] text-white rounded-lg font-semibold">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-[#353A37]/60">
                        Tidak ada data pengguna yang sesuai dengan filter pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-[#A7B5A0]/30">
        {{ $users->links() }}
    </div>
</div>
@endsection
