@extends('layouts.app')

@section('title', 'Detail Tiket ' . $report->ticket_code . ' — ROVIX')
@section('header', 'Detail Status Tiket Laporan: ' . $report->ticket_code)
@section('subheader', 'Informasi pelacakan alur verifikasi, disposisi SPK teknis, dan bukti fisik penyelesaian perbaikan jalan')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <!-- Breadcrumb & Navigasi Atas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('warga.reports.index') }}" class="text-[#263B36] hover:text-[#C76B45] font-semibold flex items-center gap-1 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Riwayat Laporan</span>
            </a>
            <span class="text-[#353A37]/40">/</span>
            <span class="font-mono-tech font-bold text-[#C76B45]">{{ $report->ticket_code }}</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-mono-tech text-[#353A37]/70">Dibuat: {{ $report->created_at?->format('d/m/Y H:i') }} WIB</span>
            <a href="{{ route('warga.reports.create') }}" class="px-3 py-1.5 rounded text-xs font-bold bg-[#C76B45] hover:bg-[#a95734] text-white transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Lapor Baru</span>
            </a>
        </div>
    </div>

    <!-- Banner Khusus Status Tiket Ditolak (Jika Ada) -->
    @if($report->status === 'rejected')
        <div class="p-4 rounded-lg bg-red-100 border-l-4 border-red-600 text-red-950 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-red-900">Laporan Ditolak / Ditandai Sebagai Spam</h4>
                    <p class="text-xs text-red-800 mt-1">
                        Tiket aduan ini telah ditolak oleh Admin Dinas PU setelah proses tinjauan manual. Foto atau objek yang dilaporkan tidak memenuhi kriteria kerusakan infrastruktur jalan atau terindikasi duplikasi/spam.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- 1. STEPPER PROGRESS STATUS PENANGANAN LAPORAN -->
    @php
        $statusOrder = [
            'submitted' => 1,
            'verified' => 2,
            'in_progress' => 3,
            'resolved' => 4,
            'rejected' => 0,
        ];
        $currentStep = $statusOrder[$report->status] ?? 1;
    @endphp
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
        <h3 class="font-bold text-xs uppercase tracking-wider text-[#263B36] mb-4">
            Alur Progres Penanganan Tiket Aduan
        </h3>

        <div class="relative">
            <!-- Garis Penghubung Stepper -->
            <div class="hidden sm:block absolute top-4 left-0 right-0 h-1 bg-[#263B36]/15 z-0 mx-8">
                <div class="h-full bg-[#263B36] transition-all duration-500"
                    style="width: {{ $report->status === 'rejected' ? '0%' : (($currentStep - 1) / 3 * 100) }}%;"></div>
            </div>

            <!-- Titik-Titik Tahapan -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 relative z-10">
                <!-- Tahap 1: Laporan Diterima -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-mono-tech font-bold border-2 transition
                        {{ $currentStep >= 1 ? 'bg-[#263B36] text-white border-[#263B36]' : 'bg-white text-[#353A37]/50 border-[#263B36]/20' }}">
                        @if($currentStep > 1) ✓ @else 1 @endif
                    </div>
                    <span class="font-bold text-xs mt-2 text-[#263B36]">1. Aduan Dikirim</span>
                    <span class="text-[10px] text-[#353A37]/70 font-mono-tech">Laporan Warga Masuk</span>
                </div>

                <!-- Tahap 2: Verifikasi Dinas PU -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-mono-tech font-bold border-2 transition
                        {{ $currentStep >= 2 ? 'bg-[#263B36] text-white border-[#263B36]' : 'bg-white text-[#353A37]/50 border-[#263B36]/20' }}">
                        @if($currentStep > 2) ✓ @else 2 @endif
                    </div>
                    <span class="font-bold text-xs mt-2 {{ $currentStep >= 2 ? 'text-[#263B36]' : 'text-[#353A37]/50' }}">2. Verifikasi PU</span>
                    <span class="text-[10px] text-[#353A37]/70 font-mono-tech">Validasi Admin Dinas</span>
                </div>

                <!-- Tahap 3: Disposisi & Pengerjaan Petugas -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-mono-tech font-bold border-2 transition
                        {{ $currentStep >= 3 ? 'bg-[#263B36] text-white border-[#263B36]' : 'bg-white text-[#353A37]/50 border-[#263B36]/20' }}">
                        @if($currentStep > 3) ✓ @else 3 @endif
                    </div>
                    <span class="font-bold text-xs mt-2 {{ $currentStep >= 3 ? 'text-[#263B36]' : 'text-[#353A37]/50' }}">3. Perbaikan Jalan</span>
                    <span class="text-[10px] text-[#353A37]/70 font-mono-tech">Tindakan Petugas Lapangan</span>
                </div>

                <!-- Tahap 4: Selesai Diperbaiki -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-mono-tech font-bold border-2 transition
                        {{ $currentStep >= 4 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-[#353A37]/50 border-[#263B36]/20' }}">
                        @if($currentStep >= 4) ✓ @else 4 @endif
                    </div>
                    <span class="font-bold text-xs mt-2 {{ $currentStep >= 4 ? 'text-emerald-700' : 'text-[#353A37]/50' }}">4. Selesai (Resolved)</span>
                    <span class="text-[10px] text-[#353A37]/70 font-mono-tech">Hasil Tervalidasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KONTEN UTAMA: 2 KOLOM (FOTO/AI & RINCIAN/PETA) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <!-- KOLOM KIRI (5 Kolom): BUKTI FOTO & AI VISION -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Card Foto Bukti Kerusakan Jalan -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden shadow-sm">
                <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between bg-[#E9E5DA]/50">
                    <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider">Foto Kerusakan Jalan</h4>
                    <span class="text-[10px] font-mono-tech text-[#353A37]/70">Dokumentasi Pelapor</span>
                </div>
                <div class="relative bg-black/10 group">
                    <img src="{{ $report->photo_src }}" alt="{{ $report->ticket_code }}"
                        class="w-full h-64 sm:h-72 object-cover"
                        onerror="this.onerror=null; this.src='https://placehold.co/600x400/263B36/E9E5DA?text=Foto+Kerusakan+Jalan';">
                    <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between bg-black/70 backdrop-blur-sm px-3 py-1.5 rounded text-white text-[11px]">
                        <span class="font-mono-tech truncate max-w-[200px]">{{ basename((string) $report->photo_url) }}</span>
                        <a href="{{ $report->photo_src }}" target="_blank" class="hover:underline flex items-center gap-1 font-semibold text-white">
                            <span>Perbesar</span>
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Panel Analisis AI Computer Vision -->
                <div class="p-4 border-t border-[#263B36]/15 bg-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-[#263B36] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#C76B45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m10 15 5-3-5-3v6z"/></svg>
                            <span>Analisis AI Vision ROVIX</span>
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono-tech font-bold uppercase
                            {{ $report->ai_validation ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                            {{ $report->ai_validation ? 'Objek Valid Kerusakan Jalan' : 'Perlu Verifikasi Petugas' }}
                        </span>
                    </div>

                    <!-- Bar Confidence AI -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[11px] font-mono-tech">
                            <span class="text-[#353A37]/75">Tingkat Keyakinan AI:</span>
                            <span class="font-bold text-[#263B36]">{{ number_format($report->ai_confidence, 2) }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-[#263B36]/10 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $report->ai_validation ? 'bg-emerald-600' : 'bg-amber-500' }}"
                                style="width: {{ min(100, max(0, $report->ai_confidence)) }}%;"></div>
                        </div>
                    </div>
                    <p class="text-[10px] text-[#353A37]/65 mt-2">
                        {{ $report->ai_validation ? 'Citra telah dipindai otomatis oleh model Google Gemini Vision / modul heuristik lokal ROVIX dan terkonfirmasi memuat kerusakan jalan.' : 'Citra terdeteksi memiliki tingkat keyakinan rendah dan memerlukan validasi manual oleh tim verifikator.' }}
                    </p>
                </div>
            </div>

            <!-- Foto Bukti Hasil Selesai Diperbaiki (Jika Sudah Ada SPK Selesai) -->
            @if($report->workOrder && $report->workOrder->after_photo_url)
            <div class="bg-[#F4F1EA] rounded-lg border border-emerald-600/30 overflow-hidden shadow-sm">
                <div class="px-4 py-2.5 border-b border-emerald-600/20 bg-emerald-50 flex items-center justify-between">
                    <h4 class="font-bold text-xs text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Dokumentasi Selesai Diperbaiki</span>
                    </h4>
                    <span class="text-[10px] font-mono-tech text-emerald-800 font-semibold">Hasil Petugas</span>
                </div>
                @php
                    $afterPhotoSrc = str_starts_with((string) $report->workOrder->after_photo_url, 'http')
                        ? $report->workOrder->after_photo_url
                        : asset('storage/' . ltrim((string) $report->workOrder->after_photo_url, '/'));
                @endphp
                <div class="relative bg-black/10">
                    <img src="{{ $afterPhotoSrc }}" alt="Hasil Perbaikan"
                        class="w-full h-64 object-cover"
                        onerror="this.onerror=null; this.src='https://placehold.co/600x400/263B36/E9E5DA?text=Foto+Selesai+Diperbaiki';">
                </div>
                @if($report->workOrder->repair_notes)
                <div class="p-3 bg-white text-xs text-[#353A37] border-t border-[#263B36]/10">
                    <p class="font-bold text-[11px] text-[#263B36] uppercase tracking-wide">Catatan Teknis Petugas:</p>
                    <p class="text-xs text-[#353A37]/80 mt-0.5">{{ $report->workOrder->repair_notes }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Penilaian & Ulasan Warga (Rating) -->
            @if($report->rating)
            <div class="bg-[#F4F1EA] rounded-lg border border-amber-300 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider">Ulasan Anda</h4>
                    <div class="flex items-center gap-0.5 text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $report->rating->score ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                        <span class="font-mono-tech text-xs font-bold text-amber-700 ml-1">({{ $report->rating->score }}/5)</span>
                    </div>
                </div>
                <p class="text-xs text-[#353A37] italic bg-white p-3 rounded border border-[#263B36]/10">
                    "{{ $report->rating->review_text ?? 'Tidak ada ulasan teks.' }}"
                </p>
                <p class="text-[10px] font-mono-tech text-[#353A37]/60 mt-2 text-right">
                    Dinilai pada: {{ $report->rating->created_at?->format('d/m/Y H:i') }}
                </p>
            </div>
            @elseif($report->status === 'resolved' && auth()->id() === $report->user_id)
            <div class="bg-[#F4F1EA] rounded-lg border border-[#C76B45]/40 p-4 shadow-sm flex items-center justify-between gap-3">
                <div>
                    <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider">Berikan Penilaian Perbaikan</h4>
                    <p class="text-[11px] text-[#353A37]/75 mt-0.5">Perbaikan jalan telah selesai. Berikan rating 1–5 atas hasil pengerjaan petugas.</p>
                </div>
                <a href="{{ route('ratings.create', $report->id) }}"
                    class="px-3.5 py-2 rounded bg-[#C76B45] hover:bg-[#b25c39] text-white text-xs font-bold transition shrink-0">
                    Beri Rating
                </a>
            </div>
            @endif
        </div>

        <!-- KOLOM KANAN (7 Kolom): DETAIL TIKET, PETA GIS & TIMELINE AUDIT -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Informasi Pokok Tiket Aduan -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#263B36]/15 pb-3 mb-3">
                    <div>
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Nomor Tiket Aduan</span>
                        <h3 class="text-lg font-bold font-mono-tech text-[#263B36]">{{ $report->ticket_code }}</h3>
                    </div>

                    <div>
                        @if($report->status === 'resolved')
                            <span class="px-3 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Selesai Diperbaiki
                            </span>
                        @elseif($report->status === 'in_progress')
                            <span class="px-3 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#263B36] text-white">
                                Sedang Diperbaiki
                            </span>
                        @elseif($report->status === 'verified')
                            <span class="px-3 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/30">
                                Terverifikasi
                            </span>
                        @elseif($report->status === 'rejected')
                            <span class="px-3 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-red-100 text-red-800 border border-red-300">
                                Ditolak / Spam
                            </span>
                        @else
                            <span class="px-3 py-1 rounded text-xs font-mono-tech font-bold uppercase bg-[#C76B45]/20 text-[#C76B45] border border-[#C76B45]/30">
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detail Grid Parameter -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 text-xs">
                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Jenis Kerusakan Jalan</span>
                        <p class="font-bold text-[#263B36] mt-0.5">
                            {{ $report->category->category_name ?? '-' }}
                        </p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-mono-tech font-bold rounded bg-[#C76B45]/15 text-[#C76B45]">
                            Tingkat Bahaya: Level {{ $report->category->severity_level ?? 1 }}/5
                        </span>
                    </div>

                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Wilayah Kecamatan</span>
                        <p class="font-bold text-[#263B36] mt-0.5">
                            {{ $report->district->name ?? '-' }}
                        </p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-mono-tech rounded bg-[#263B36]/10 text-[#263B36]">
                            Kode Wilayah: {{ $report->district->city_code ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div class="p-3 rounded bg-white border border-[#263B36]/10 mb-2">
                    <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Deskripsi & Patokan Lokasi:</span>
                    <p class="text-xs text-[#353A37] mt-1 leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </p>
                </div>
            </div>

            <!-- Peta Lokasi Spasial GIS (Leaflet.js) -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-[#263B36]/15 pb-2.5 mb-3">
                    <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#C76B45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Peta Lokasi Kerusakan Jalan</span>
                    </h4>
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                        class="px-2.5 py-1 bg-white hover:bg-[#263B36] text-[#263B36] hover:text-white border border-[#263B36]/20 rounded text-[11px] font-semibold transition flex items-center gap-1">
                        <span>Buka di Google Maps</span>
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                </div>

                <div id="reportMapShow" class="w-full h-56 rounded-lg border border-[#263B36]/20 shadow-inner z-10 mb-2"></div>

                <div class="flex items-center justify-between text-[11px] font-mono-tech text-[#353A37]/80 bg-white px-3 py-1.5 rounded border border-[#263B36]/10">
                    <span>Koordinat Presisi:</span>
                    <span class="font-bold text-[#263B36]">{{ $report->latitude }}, {{ $report->longitude }}</span>
                </div>
            </div>

            <!-- Disposisi SPK Lapangan (Jika sudah ditugaskan) -->
            @if($report->workOrder)
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-[#263B36]/15 pb-2.5 mb-3">
                    <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#263B36]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span>Disposisi SPK Digital Dinas PU</span>
                    </h4>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono-tech font-bold uppercase
                        {{ $report->workOrder->work_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-[#263B36] text-white' }}">
                        SPK: {{ strtoupper($report->workOrder->work_status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Nomor SPK</span>
                        <p class="font-bold font-mono-tech text-[#263B36] mt-0.5">{{ $report->workOrder->spk_number }}</p>
                    </div>

                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Petugas Penanggung Jawab</span>
                        <p class="font-bold text-[#263B36] mt-0.5">{{ $report->workOrder->officer->name ?? 'Regu Lapangan' }}</p>
                        @if($report->workOrder->officer?->phone_number)
                            <span class="text-[10px] font-mono-tech text-[#353A37]/70">Kontak: {{ $report->workOrder->officer->phone_number }}</span>
                        @endif
                    </div>

                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Batas Waktu (Target SLA)</span>
                        <p class="font-bold font-mono-tech text-[#C76B45] mt-0.5">
                            {{ \Carbon\Carbon::parse($report->workOrder->target_sla)->format('d F Y') }}
                        </p>
                    </div>

                    <div class="p-2.5 rounded bg-white border border-[#263B36]/10">
                        <span class="text-[10px] font-mono-tech text-[#353A37]/70 uppercase">Waktu Selesai Pengerjaan</span>
                        <p class="font-bold font-mono-tech text-emerald-800 mt-0.5">
                            {{ $report->workOrder->resolved_at ? \Carbon\Carbon::parse($report->workOrder->resolved_at)->format('d/m/Y H:i') . ' WIB' : 'Dalam Proses Pengerjaan' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Riwayat Jejak Audit Trail (Audit Logs) -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
                <h4 class="font-bold text-xs text-[#263B36] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#263B36]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Riwayat Aktivitas & Jejak Audit Tiket</span>
                </h4>

                <div class="flow-root">
                    <ul class="-mb-3 space-y-3">
                        @forelse($report->auditLogs as $log)
                        <li class="relative flex gap-3 text-xs">
                            <div class="flex-none w-2 h-2 rounded-full bg-[#C76B45] mt-1.5 ring-4 ring-white"></div>
                            <div class="flex-auto">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-[#263B36]">{{ $log->action_name }}</span>
                                    <span class="text-[10px] font-mono-tech text-[#353A37]/60">
                                        {{ $log->created_at?->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-[#353A37]/75 mt-0.5">
                                    Pelaku: <span class="font-semibold">{{ $log->user->name ?? 'Sistem' }}</span>
                                    @if($log->new_status)
                                        • Status: <span class="font-mono-tech font-bold uppercase">{{ $log->new_status }}</span>
                                    @endif
                                </p>
                            </div>
                        </li>
                        @empty
                        <li class="text-xs text-[#353A37]/60 italic">
                            Belum ada rekam jejak aktivitas lain pada tiket ini.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Peta Leaflet untuk Halaman Detail Show -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportLat = {{ (float) $report->latitude }};
        const reportLng = {{ (float) $report->longitude }};

        const mapShow = L.map('reportMapShow', {
            center: [reportLat, reportLng],
            zoom: 15,
            zoomControl: true,
            scrollWheelZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap | ROVIX GIS'
        }).addTo(mapShow);

        const customMarkerIcon = L.divIcon({
            className: 'custom-show-pin',
            html: `
                <div style="background-color: #263B36; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.4);">
                    <div style="background-color: #C76B45; width: 10px; height: 10px; border-radius: 50%;"></div>
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });

        const marker = L.marker([reportLat, reportLng], { icon: customMarkerIcon }).addTo(mapShow);
        marker.bindPopup(`
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px;">
                <strong style="color: #263B36;">{{ $report->ticket_code }}</strong><br>
                <span>{{ $report->district->name ?? 'Kecamatan' }}</span><br>
                <span style="font-family: monospace; font-size: 10px; color: #C76B45;">{{ $report->latitude }}, {{ $report->longitude }}</span>
            </div>
        `).openPopup();

        // Tambah lingkaran radius indikator 30m
        L.circle([reportLat, reportLng], {
            color: '#C76B45',
            fillColor: '#C76B45',
            fillOpacity: 0.15,
            radius: 30
        }).addTo(mapShow);

        window.addEventListener('resize', () => mapShow.invalidateSize());
    });
</script>
@endsection

