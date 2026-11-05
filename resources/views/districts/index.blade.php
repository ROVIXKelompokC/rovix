@extends('layouts.app')

@section('content')
<div class="p-6 bg-[#f4f1ea] min-h-screen text-[#1e292b]">
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight">Manajemen Wilayah & Zonasi Kecamatan</h1>
            <p class="text-xs text-gray-500">Pengelolaan zona operasional dinas, kode wilayah administratif, dan sebaran petugas</p>
        </div>
        <div class="bg-[#e2ded4] text-xs px-3 py-1.5 rounded font-medium text-gray-700">
            Wilayah: Kecamatan Jagakarsa
        </div>
    </div>

    <!-- Stat Card -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-[#ebe7df] p-4 rounded-lg flex justify-between items-center shadow-sm">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-wider text-gray-500">TOTAL KECAMATAN</p>
                <p class="text-2xl font-bold mt-1">{{ $totalDistricts ?? $districts->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Button Add -->
    <div class="bg-[#ebe7df] p-4 rounded-t-lg flex justify-between items-center border-b border-[#d8d3c7]">
        <form method="GET" action="{{ route('admin.districts.index') }}" class="flex gap-2 w-2/3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kecamatan atau kode wilayah..." class="w-full text-xs bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#1e292b]">
            <button type="submit" class="bg-[#1e292b] text-white text-xs px-4 py-2 rounded font-semibold hover:bg-black transition">Terapkan Filter</button>
        </form>
        <a href="{{ route('admin.districts.create') }}" class="bg-[#c86d44] hover:bg-[#b05c35] text-white text-xs font-bold px-4 py-2 rounded transition shadow-sm">
            + Tambah Wilayah Baru
        </a>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-3 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white shadow-sm rounded-b-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#1e292b] text-white text-[11px] uppercase tracking-wider">
                    <th class="p-3">KODE WILAYAH</th>
                    <th class="p-3">NAMA KECAMATAN</th>
                    <th class="p-3">JUMLAH PETUGAS</th>
                    <th class="p-3">TITIK LAPORAN</th>
                    <th class="p-3 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-xs">
                @forelse($districts as $district)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 font-bold text-gray-800">{{ $district->city_code }}</td>
                    <td class="p-3 font-medium">{{ $district->name }}</td>
                    <td class="p-3 text-gray-600">{{ $district->users_count ?? 0 }} Personel</td>
                    <td class="p-3 text-gray-600">{{ $district->reports_count ?? 0 }} Lokasi</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.districts.show', $district->id) }}" class="bg-[#1e292b] text-white text-[10px] px-3 py-1 rounded font-medium hover:bg-black transition">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500 italic">Data wilayah kecamatan belum tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $districts->links() }}
    </div>
</div>
@endsection