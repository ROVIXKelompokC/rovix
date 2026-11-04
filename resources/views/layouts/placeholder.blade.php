@extends('layouts.app')

@section('title', $title . ' — ROVIX')
@section('header', $title)
@section('subheader', $subtitle)

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center max-w-2xl mx-auto my-6">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-3xl mx-auto mb-4">
        {{ $icon ?? '📌' }}
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2">{{ $title }}</h3>
    <p class="text-xs text-slate-500 leading-relaxed mb-6">
        {{ $description }}
    </p>
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500">
        <span>⚙️</span>
        <span>Halaman fitur ini telah disiapkan pada kerangka rute sistem dan siap diimplementasikan.</span>
    </div>
</div>
@endsection
