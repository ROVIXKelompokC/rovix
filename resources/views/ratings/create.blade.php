@extends('layouts.app')

@section('title', 'Beri Rating Perbaikan Jalan — ROVIX')
@section('header', 'Formulir Penilaian & Ulasan Perbaikan Jalan')
@section('subheader', 'Berikan penilaian skala 1–5 atas hasil pengerjaan perbaikan jalan pada tiket laporan Anda')

@section('content')
<div class="w-full max-w-2xl bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 sm:p-6">
    <div class="pb-3 mb-4 border-b border-[#263B36]/15">
        <span class="px-2.5 py-0.5 rounded bg-[#263B36] text-[#E9E5DA] font-mono-tech font-bold text-xs">
            {{ $report->ticket_code }}
        </span>
        <h3 class="text-sm font-bold text-[#263B36] mt-2">{{ $report->description }}</h3>
        <p class="text-xs text-[#353A37]/75 mt-0.5">
            Wilayah: {{ $report->district->name ?? '-' }} • Kategori: {{ $report->category->category_name ?? '-' }}
        </p>
    </div>

    <form action="{{ route('ratings.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <input type="hidden" name="report_id" value="{{ $report->id }}">

        <div>
            <label class="block font-semibold text-[#263B36] mb-1.5">Skor Kepuasan Perbaikan (Skala 1 – 5)</label>
            <select name="score" required class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] focus:border-[#C76B45] outline-none">
                <option value="">-- Pilih Nilai Rating --</option>
                <option value="5" {{ old('score') == '5' ? 'selected' : '' }}>5 / 5 — Sangat Puas (Perbaikan Rapi & Cepat)</option>
                <option value="4" {{ old('score') == '4' ? 'selected' : '' }}>4 / 5 — Puas (Perbaikan Baik)</option>
                <option value="3" {{ old('score') == '3' ? 'selected' : '' }}>3 / 5 — Cukup</option>
                <option value="2" {{ old('score') == '2' ? 'selected' : '' }}>2 / 5 — Kurang Puas</option>
                <option value="1" {{ old('score') == '1' ? 'selected' : '' }}>1 / 5 — Buruk</option>
            </select>
            @error('score')
                <p class="text-[#C76B45] text-[11px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-semibold text-[#263B36] mb-1.5">Ulasan / Masukan Warga</label>
            <textarea name="review_text" rows="4"
                class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] focus:border-[#C76B45] outline-none"
                placeholder="Tuliskan ulasan Anda mengenai kualitas hasil perbaikan jalan di lokasi tersebut...">{{ old('review_text') }}</textarea>
            @error('review_text')
                <p class="text-[#C76B45] text-[11px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#263B36]/10">
            <a href="{{ route('warga.ratings.index') }}"
                class="px-3.5 py-2 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 font-semibold">
                Batal
            </a>
            <button type="submit"
                class="px-4 py-2 rounded bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold transition">
                Kirim Penilaian
            </button>
        </div>
    </form>
</div>
@endsection
