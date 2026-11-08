@extends('layouts.app')

@section('title', 'Tambah Kategori Kerusakan — ROVIX')
@section('header', 'Tambah Kategori Kerusakan Jalan')
@section('subheader', 'Tambahkan jenis kerusakan jalan baru beserta tingkat keparahannya (skala 1 sampai 5)')

@section('content')
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-6 max-w-xl my-4">
    @if(session('success'))
        <div class="mb-4 px-3 py-2 rounded bg-[#A7B5A0]/40 text-[#263B36] text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="category_name" class="block text-[11px] font-mono-tech uppercase text-[#353A37]/75 mb-1">Nama Kategori</label>
            <input type="text" id="category_name" name="category_name" value="{{ old('category_name') }}"
                placeholder="Contoh: Jalan Berlubang Dalam"
                class="px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs w-full focus:border-[#C76B45] outline-none">
            @error('category_name')
                <p class="mt-1 text-[11px] text-[#C76B45]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="severity_level" class="block text-[11px] font-mono-tech uppercase text-[#353A37]/75 mb-1">Tingkat Keparahan (1–5)</label>
            <select id="severity_level" name="severity_level"
                class="px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs w-full">
                <option value="">Pilih tingkat keparahan</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ (string) old('severity_level') === (string) $i ? 'selected' : '' }}>Level {{ $i }}</option>
                @endfor
            </select>
            @error('severity_level')
                <p class="mt-1 text-[11px] text-[#C76B45]">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="px-4 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition">
                Simpan Kategori
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 bg-[#A7B5A0]/30 text-[#263B36] rounded text-xs font-medium hover:bg-[#A7B5A0]/50">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection