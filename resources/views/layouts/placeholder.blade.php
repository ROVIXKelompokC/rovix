@extends('layouts.app')

@section('title', $title . ' — ROVIX')
@section('header', $title)
@section('subheader', $subtitle)

@section('content')
<div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-12 text-center max-w-2xl mx-auto my-6">
    <div class="w-16 h-16 rounded-2xl bg-[#A7B5A0]/30 text-[#263B36] flex items-center justify-center text-3xl mx-auto mb-4">
        {{ $icon ?? '📌' }}
    </div>
    <h3 class="text-lg font-bold text-[#263B36] mb-2">{{ $title }}</h3>
    <p class="text-xs text-[#353A37]/80 leading-relaxed mb-6">
        {{ $description }}
    </p>
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#E9E5DA] border border-[#A7B5A0]/50 text-xs text-[#263B36] font-medium">
        <span>⚙️</span>
        <span>Halaman fitur ini telah disiapkan pada kerangka rute sistem dan siap diimplementasikan.</span>
    </div>
</div>
@endsection
