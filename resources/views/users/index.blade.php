@extends('layouts.app')

@section('title', 'Manajemen Pengguna & RBAC — ROVIX')
@section('header', 'Manajemen Pengguna & Hak Akses (RBAC)')
@section('subheader', 'Pengelolaan akun Warga Pelapor, Petugas Teknis Lapangan, dan Administrator Dinas PU')

@section('content')
<!-- Ringkasan Distribusi Peran (RBAC) -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#F4F1EA] p-4 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Total Akun Terdaftar</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36] mt-1">{{ $roleCounts['all'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-4 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Admin Dinas PU</p>
        <p class="text-2xl font-bold font-mono-tech text-[#263B36] mt-1">{{ $roleCounts['admin'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-4 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Petugas Lapangan</p>
        <p class="text-2xl font-bold font-mono-tech text-[#C76B45] mt-1">{{ $roleCounts['petugas'] }}</p>
    </div>
    <div class="bg-[#F4F1EA] p-4 rounded-lg border border-[#263B36]/15">
        <p class="text-[11px] font-mono-tech text-[#353A37]/70 uppercase">Warga Pelapor</p>
        <p class="text-2xl font-bold font-mono-tech text-[#353A37] mt-1">{{ $roleCounts['warga'] }}</p>
    </div>
</div>

<!-- Bar Pencarian, Filter Role RBAC, & Tombol Tambah Pengguna -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau nomor telepon..."
                class="px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs w-full md:w-64 focus:border-[#C76B45] outline-none">

            <select name="role" class="px-3 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-xs bg-white w-full sm:w-auto">
                <option value="">Semua Peran (RBAC)</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Dinas PU</option>
                <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                <option value="warga" {{ request('role') === 'warga' ? 'selected' : '' }}>Warga Pelapor</option>
            </select>

            <select name="district_id" class="px-3 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-xs bg-white w-full sm:w-auto">
                <option value="">Semua Wilayah Kecamatan</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) request('district_id') === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->city_code }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-[#263B36] text-white rounded text-xs font-semibold hover:bg-[#1d2e2a] transition w-full sm:w-auto">
                Terapkan Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'district_id']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-[#A7B5A0]/30 text-[#263B36] rounded text-xs font-medium hover:bg-[#A7B5A0]/50 text-center w-full sm:w-auto">
                    Atur Ulang
                </a>
            @endif
        </form>

        <a href="{{ route('admin.users.create') }}"
            class="px-4 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition shrink-0 text-center w-full md:w-auto">
            + Tambah Pengguna Baru
        </a>
    </div>
</div>

<!-- Tabel Daftar Pengguna -->
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[11px] uppercase">
                <tr>
                    <th class="px-5 py-3">Nama & Email</th>
                    <th class="px-5 py-3">Otorisasi Peran</th>
                    <th class="px-5 py-3">Wilayah Tugas</th>
                    <th class="px-5 py-3">Kontak</th>
                    <th class="px-5 py-3">Rekam Aktivitas</th>
                    <th class="px-5 py-3 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#263B36]/10">
                @forelse($users as $u)
                <tr class="hover:bg-[#E9E5DA]/60">
                    <td class="px-5 py-3.5">
                        <div class="font-bold text-[#263B36]">{{ $u->name }}</div>
                        <div class="font-mono-tech text-[11px] text-[#353A37]/70">{{ $u->email }}</div>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($u->role === 'admin')
                            <span class="px-2 py-0.5 rounded bg-[#263B36] text-[#E9E5DA] font-mono-tech font-semibold text-[10px] uppercase">ADMIN DINAS</span>
                        @elseif($u->role === 'petugas')
                            <span class="px-2 py-0.5 rounded bg-[#C76B45] text-white font-mono-tech font-semibold text-[10px] uppercase">PETUGAS PU</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/40 text-[#263B36] font-mono-tech font-semibold text-[10px] uppercase">WARGA</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-[#353A37]">{{ $u->district->name ?? 'Lintas Wilayah / Umum' }}</div>
                        @if($u->district)
                            <div class="font-mono-tech text-[#353A37]/65 text-[11px]">{{ $u->district->city_code }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 font-mono-tech text-[#353A37]">{{ $u->phone_number }}</td>
                    <td class="px-5 py-3.5 font-mono-tech text-[#353A37]/80 text-[11px]">
                        {{ $u->reports_count }} Laporan • {{ $u->work_orders_count }} SPK
                    </td>
                    <td class="px-5 py-3.5 text-right space-x-1">
                        <a href="{{ route('admin.users.show', $u) }}"
                            class="inline-block px-2.5 py-1 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 rounded font-medium">
                            Rincian
                        </a>
                        <a href="{{ route('admin.users.edit', $u) }}"
                            class="inline-block px-2.5 py-1 bg-[#263B36] hover:bg-[#1d2e2a] text-white rounded font-medium">
                            Ubah
                        </a>
                        @if(auth()->id() !== $u->id)
                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Hapus akun {{ $u->name }} dari sistem?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 bg-[#C76B45] hover:bg-[#b25c39] text-white rounded font-medium">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-[#353A37]/60">
                        Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-3.5 border-t border-[#263B36]/10">
        {{ $users->links() }}
    </div>
</div>
@endsection
