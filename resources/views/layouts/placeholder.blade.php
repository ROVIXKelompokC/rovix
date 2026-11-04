@extends('layouts.app')

@section('title', $title . ' — ROVIX')
@section('header', $title)
@section('subheader', $subtitle)

@section('content')
<div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-8 max-w-2xl my-4">
    <div class="flex items-center gap-2.5 mb-3">
        <span class="w-2 h-2 rounded-full bg-[#C76B45]"></span>
        <span class="font-mono-tech text-[11px] uppercase tracking-wider text-[#263B36]/75">Modul Sistem Terdaftar</span>
    </div>
    <h3 class="text-base font-bold text-[#263B36] mb-2">{{ $title }}</h3>
    <p class="text-xs text-[#353A37]/80 leading-relaxed mb-6">
        {{ $description }}
    </p>
    <div class="pt-4 border-t border-[#263B36]/10 flex items-center justify-between text-[11px] text-[#353A37]/70 font-mono-tech">
        <span>Rute: /{{ request()->path() }}</span>
        <span>Status: Siap Diimplementasikan</span>
    </div>
</div>
@endsection
