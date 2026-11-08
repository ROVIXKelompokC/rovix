@extends('layouts.app')

@section('content')
<div class="p-6 bg-[#f4f1ea] min-h-screen text-[#1e292b]">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="bg-[#1e292b] text-white p-4">
            <h2 class="text-sm font-bold uppercase tracking-wider">Tambah Master Wilayah Baru</h2>
            <p class="text-[11px] text-gray-300">Masukkan kode area administratif dan nama kecamatan operasional</p>
        </div>

        <form action="{{ route('admin.districts.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Wilayah (City Code)</label>
                <input type="text" name="city_code" value="{{ old('city_code') }}" placeholder="Contoh: JKS-01" class="w-full text-xs bg-[#fdfbf7] border border-gray-300 rounded p-2.5 focus:outline-none focus:ring-1 focus:ring-[#1e292b] @error('city_code') border-red-500 @enderror" required>
                @error('city_code')
                    <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Kecamatan</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kecamatan Jagakarsa" class="w-full text-xs bg-[#fdfbf7] border border-gray-300 rounded p-2.5 focus:outline-none focus:ring-1 focus:ring-[#1e292b] @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('admin.districts.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">Batal</a>
                <button type="submit" class="bg-[#c86d44] hover:bg-[#b05c35] text-white text-xs font-bold px-5 py-2.5 rounded transition shadow-sm">
                    Simpan Wilayah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection