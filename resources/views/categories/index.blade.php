@extends('layouts.app')

@section('title', 'Master Data Kategori Kerusakan — ROVIX')
@section('header', 'Master Data Kategori Kerusakan Jalan')
@section('subheader', 'Daftar jenis kerusakan jalan dan tingkat keparahannya')

@section('content')
@if(session('success'))
    <div class="mb-4 px-3 py-2 rounded bg-[#A7B5A0]/40 text-[#263B36] text-xs font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4">
    <a href="{{ route('admin.categories.create') }}"
        class="px-4 py-2 bg-[#C76B45] text-white font-semibold rounded text-xs">
        + Tambah Kategori Baru
    </a>
</div>

<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
    <table class="w-full text-left text-xs">
        <thead class="bg-[#263B36] text-[#E9E5DA] text-[10px] uppercase">
            <tr>
                <th class="px-4 py-2.5">Nama Kategori</th>
                <th class="px-4 py-2.5">Tingkat Keparahan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#263B36]/10">
            @foreach($categories as $c)
            <tr>
                <td class="px-4 py-2.5 font-bold text-[#263B36]">{{ $c->category_name }}</td>
                <td class="px-4 py-2.5">Level {{ $c->severity_level }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection