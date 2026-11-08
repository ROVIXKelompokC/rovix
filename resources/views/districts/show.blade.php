@extends('layouts.app')

@section('content')
<div class="p-6 bg-[#f4f1ea] min-h-screen text-[#1e292b]">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-[#1e292b] text-white p-4">
            <h2 class="text-xs font-bold uppercase tracking-wider">Detail Informasi Wilayah</h2>
        </div>
        
        <div class="p-6 space-y-4 text-xs">
            <div>
                <span class="text-gray-500 uppercase tracking-wider font-semibold text-[10px]">Kode Wilayah (City Code)</span>
                <p class="text-lg font-bold text-[#c86d44] mt-0.5">{{ $district->city_code }}</p>
            </div>

            <div>
                <span class="text-gray-500 uppercase tracking-wider font-semibold text-[10px]">Nama Kecamatan</span>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $district->name }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                <div class="bg-[#fdfbf7] p-3 rounded border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-500 font-bold uppercase">Petugas Active</span>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $district->users_count ?? 0 }}</p>
                </div>
                <div class="bg-[#fdfbf7] p-3 rounded border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-500 font-bold uppercase">Titik Laporan</span>
                    <p class="text-xl font-bold text-gray-800 mt-1">{{ $district->reports_count ?? 0 }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <a href="{{ route('admin.districts.index') }}" class="bg-[#1e292b] text-white text-xs px-4 py-2 rounded font-semibold hover:bg-black transition inline-block">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection