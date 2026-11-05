@extends('layouts.app')

@section('title', 'Detail Verifikasi Tiket ' . $report->ticket_code . ' — ROVIX')
@section('header', 'Tinjauan & Verifikasi Tiket: ' . $report->ticket_code)
@section('subheader', 'Pemeriksaan bukti citra AI Vision, radius duplikasi Haversine (< 30m), dan rekam jejak Audit Log tiket')

@section('content')
@php
    $photoSrc = str_starts_with((string) $report->photo_url, 'http')
        ? $report->photo_url
        : asset('storage/' . ltrim((string) $report->photo_url, '/'));
    $severity = (int) ($report->category->severity_level ?? 1);
@endphp

<div class="space-y-4">

    @if(isset($errors) && $errors->any())
        <div class="px-4 py-3 rounded bg-[#C76B45]/15 border-l-4 border-[#C76B45] text-[#263B36] text-xs">
            <p class="font-bold mb-1">Validasi Gagal:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bar Header Ringkasan Tiket & Navigasi Kembali -->
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="px-3 py-1 rounded bg-[#263B36] text-[#E9E5DA] font-mono-tech font-bold text-sm">
                {{ $report->ticket_code }}
            </span>

            @if($report->status === 'submitted')
                <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-xs bg-[#C76B45]/20 text-[#C76B45] border border-[#C76B45]/30">
                    SUBMITTED • MENUNGGU VERIFIKASI
                </span>
            @elseif($report->status === 'verified')
                <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-xs bg-amber-500/20 text-amber-900 border border-amber-600/30">
                    VERIFIED • SIAP DISPOSISI SPK
                </span>
            @elseif($report->status === 'in_progress')
                <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-xs bg-[#263B36] text-white">
                    IN PROGRESS • SPK BERJALAN
                </span>
            @elseif($report->status === 'resolved')
                <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-xs bg-[#A7B5A0]/45 text-[#263B36]">
                    RESOLVED • PERBAIKAN SELESAI
                </span>
            @else
                <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-xs bg-red-100 text-red-800 border border-red-300">
                    REJECTED • LAPORAN DITOLAK
                </span>
            @endif

            <span class="text-xs font-mono-tech text-[#353A37]/75">
                Dilaporkan: {{ $report->created_at?->format('d/m/Y H:i') }} WIB
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a target="_blank"
                href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                class="px-3 py-1.5 bg-[#E9E5DA] hover:bg-[#A7B5A0]/35 text-[#263B36] border border-[#263B36]/20 rounded text-xs font-semibold transition">
                Buka Google Maps &nearr;
            </a>
            <a href="{{ route('admin.verification.index') }}"
                class="px-3.5 py-1.5 bg-[#263B36] hover:bg-[#1d2e2a] text-[#E9E5DA] rounded text-xs font-semibold transition">
                &larr; Kembali ke Antrean
            </a>
        </div>
    </div>

    <!-- Grid Utama: Kiri (7 Kolom = Bukti Foto, AI Vision, & Form Verifikasi), Kanan (5 Kolom = Peta GIS Radius 30m, Haversine, & Audit Log) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

        <!-- KOLOM KIRI (7 Kolom) -->
        <div class="lg:col-span-7 space-y-4">

            <!-- Kartu Pratinjau Foto & Validasi AI Vision -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                        Bukti Citra Lapangan & Hasil Validasi AI Computer Vision (FR-03)
                    </h3>
                    <span class="font-mono-tech text-[11px] font-bold {{ $report->ai_validation ? 'text-[#263B36]' : 'text-[#C76B45]' }}">
                        Confidence: {{ number_format((float) $report->ai_confidence, 2) }}%
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Area Pratinjau Foto Bukti Kerusakan -->
                    <div class="relative w-full h-64 sm:h-72 rounded-lg overflow-hidden border border-[#263B36]/20 bg-[#263B36] flex items-center justify-center">
                        <img src="{{ $photoSrc }}"
                            alt="Foto Kerusakan Jalan {{ $report->ticket_code }}"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; document.getElementById('fallbackRoadPreview').classList.remove('hidden');">

                        <!-- Fallback Visual jika file sampel belum ada di disk lokal -->
                        <div id="fallbackRoadPreview" class="hidden flex-col items-center justify-center text-center p-6 text-[#E9E5DA]">
                            <div class="w-14 h-14 rounded-full bg-[#C76B45]/25 border border-[#C76B45] flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-[#C76B45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                                </svg>
                            </div>
                            <p class="font-bold text-sm text-white">Pratinjau Citra Kerusakan Jalan ({{ $report->ticket_code }})</p>
                            <p class="font-mono-tech text-xs text-[#A7B5A0] mt-1">Path Berkas: {{ $report->photo_url }}</p>
                            <p class="text-[11px] text-[#E9E5DA]/75 mt-2 max-w-md">
                                Citra telah dianalisis oleh modul AI Vision ROVIX dengan tingkat keyakinan <strong>{{ $report->ai_confidence }}%</strong>.
                            </p>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2 bg-black/65 backdrop-blur-sm px-3 py-2 rounded text-white text-xs">
                            <span class="font-mono-tech">{{ $report->photo_url }}</span>
                            <span class="px-2 py-0.5 rounded font-mono-tech font-bold text-[10px] uppercase {{ $report->ai_validation ? 'bg-[#A7B5A0] text-[#263B36]' : 'bg-[#C76B45] text-white' }}">
                                {{ $report->ai_validation ? 'AI Vision: Valid Road Damage' : 'AI Vision: Manual Check Needed' }}
                            </span>
                        </div>
                    </div>

                    <!-- Metrik Validasi AI & Klasifikasi Kerusakan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10">
                            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/70">Status Validasi AI</p>
                            <p class="text-sm font-bold text-[#263B36] mt-0.5">
                                {{ $report->ai_validation ? 'Terverifikasi AI Vision' : 'Perlu Validasi Manual' }}
                            </p>
                        </div>
                        <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10">
                            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/70">Skor Keyakinan AI</p>
                            <p class="text-sm font-bold font-mono-tech text-[#C76B45] mt-0.5">
                                {{ number_format((float) $report->ai_confidence, 2) }}%
                            </p>
                        </div>
                        <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10">
                            <p class="text-[10px] font-mono-tech uppercase text-[#353A37]/70">Tingkat Keparahan</p>
                            <p class="text-sm font-bold font-mono-tech text-[#263B36] mt-0.5">
                                Severity Level {{ $severity }} / 5
                            </p>
                        </div>
                    </div>

                    <!-- Detail Deskripsi, Kategori, dan Identitas Warga Pelapor -->
                    <div class="p-4 rounded bg-[#E9E5DA]/70 border border-[#263B36]/15 space-y-3 text-xs">
                        <div>
                            <span class="text-[10px] font-mono-tech uppercase text-[#353A37]/70 block">Kategori Kerusakan Jalan</span>
                            <span class="font-bold text-sm text-[#263B36]">{{ $report->category->category_name ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-mono-tech uppercase text-[#353A37]/70 block">Deskripsi Laporan Warga</span>
                            <p class="text-[#353A37] font-medium leading-relaxed mt-0.5">{{ $report->description }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-[#263B36]/10">
                            <div>
                                <span class="text-[10px] font-mono-tech uppercase text-[#353A37]/70 block">Identitas Warga Pelapor</span>
                                <p class="font-bold text-[#263B36] mt-0.5">{{ $report->user->name ?? 'Warga Pelapor' }}</p>
                                <p class="font-mono-tech text-[11px] text-[#353A37]/80">
                                    {{ $report->user->email ?? '-' }} • {{ $report->user->phone_number ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-[10px] font-mono-tech uppercase text-[#353A37]/70 block">Wilayah & Titik Koordinat GPS</span>
                                <p class="font-bold text-[#263B36] mt-0.5">
                                    {{ $report->district->name ?? '-' }} ({{ $report->district->city_code ?? '-' }})
                                </p>
                                <p class="font-mono-tech text-[11px] text-[#353A37]/80">
                                    Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Keputusan Verifikasi Admin (SCRUM-8: Approve / Reject + Audit Log) -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between">
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                        Keputusan Verifikasi & Validasi Tiket (SCRUM-8)
                    </h3>
                    <span class="font-mono-tech text-[10px] uppercase text-[#353A37]/70">
                        OTOMATIS TERCATAT DI AUDIT_LOGS
                    </span>
                </div>

                <div class="p-4">
                    @if(in_array($report->status, ['in_progress', 'resolved'], true))
                        <div class="p-3.5 rounded bg-[#A7B5A0]/30 border border-[#263B36]/20 text-xs text-[#263B36]">
                            <p class="font-bold">Tiket Sudah Berada Pada Tahap Operasional SPK ({{ strtoupper($report->status) }})</p>
                            <p class="mt-1 text-[#353A37]/80">
                                Laporan ini telah terverifikasi dan memiliki Surat Perintah Kerja
                                @if($report->workOrder)
                                    (<strong class="font-mono-tech">{{ $report->workOrder->spk_number }}</strong> — Petugas: <strong>{{ $report->workOrder->officer->name ?? '-' }}</strong>).
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Form 1: Setujui / Verifikasi Laporan -->
                            <form action="{{ route('admin.verification.approve', $report->id) }}" method="POST"
                                class="p-3.5 rounded-lg bg-[#E9E5DA]/75 border border-[#263B36]/20 flex flex-col justify-between space-y-3">
                                @csrf
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-xs text-[#263B36] uppercase">1. Setujui & Verifikasi Laporan</span>
                                        <span class="px-2 py-0.5 rounded bg-[#263B36] text-white font-mono-tech text-[10px]">APPROVE</span>
                                    </div>
                                    <p class="text-[11px] text-[#353A37]/75 mb-2.5">
                                        Ubah status menjadi <strong>VERIFIED</strong> agar siap diterbitkan SPK perbaikan jalan.
                                    </p>
                                    <label class="block text-[11px] font-semibold text-[#263B36] mb-1">Catatan Verifikasi (Opsional)</label>
                                    <textarea name="notes" rows="2"
                                        placeholder="Contoh: Bukti foto valid dan lokasi sesuai..."
                                        class="w-full px-3 py-1.5 rounded border border-[#263B36]/25 bg-white text-xs text-[#353A37] focus:border-[#263B36] outline-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full py-2 px-3 bg-[#263B36] hover:bg-[#1d2e2a] text-white font-semibold rounded text-xs transition">
                                    &#10003; Verifikasi Laporan (Approve)
                                </button>
                            </form>

                            <!-- Form 2: Tolak Laporan Spam / Duplikat -->
                            <form action="{{ route('admin.verification.reject', $report->id) }}" method="POST"
                                class="p-3.5 rounded-lg bg-[#C76B45]/10 border border-[#C76B45]/30 flex flex-col justify-between space-y-3">
                                @csrf
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-xs text-[#C76B45] uppercase">2. Tolak Laporan (Spam / Duplikat)</span>
                                        <span class="px-2 py-0.5 rounded bg-[#C76B45] text-white font-mono-tech text-[10px]">REJECT</span>
                                    </div>
                                    <p class="text-[11px] text-[#353A37]/75 mb-2.5">
                                        Tolak aduan apabila terbukti foto spam, tidak relevan, atau duplikat lokasi.
                                    </p>
                                    <label class="block text-[11px] font-semibold text-[#263B36] mb-1">Alasan Penolakan <span class="text-[#C76B45]">*</span></label>
                                    <textarea name="reason" rows="2" required
                                        placeholder="Wajib isi alasan penolakan (contoh: Foto spam / duplikat tiket)..."
                                        class="w-full px-3 py-1.5 rounded border border-[#C76B45]/40 bg-white text-xs text-[#353A37] focus:border-[#C76B45] outline-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full py-2 px-3 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition">
                                    &times; Tolak Laporan (Reject)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (5 Kolom: Peta Titik GPS + Radius 30m, Haversine Proximity, & Audit Log Tiket) -->
        <div class="lg:col-span-5 space-y-4">

            <!-- Peta GIS Titik Lokasi & Zona Radius 30 Meter (Haversine) -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between">
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                        Titik GPS & Zona Radius Duplikasi 30m
                    </h3>
                    <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/35 text-[#263B36] font-mono-tech text-[10px] font-semibold">
                        HAVERSINE 30M
                    </span>
                </div>
                <div id="detailReportMap" class="w-full h-64 z-0"></div>
            </div>

            <!-- Hasil Deteksi Duplikasi Spasial Haversine (< 30 Meter) -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4">
                <div class="pb-2.5 mb-3 border-b border-[#263B36]/10 flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">
                        Deteksi Kedekatan Lokasi (Radius &lt; 30m)
                    </h4>
                    <span class="font-mono-tech text-xs font-bold {{ $nearbyReports->isNotEmpty() ? 'text-[#C76B45]' : 'text-[#263B36]' }}">
                        {{ $nearbyReports->count() }} Terdeteksi
                    </span>
                </div>

                @if($nearbyReports->isNotEmpty())
                    <div class="space-y-2 text-xs">
                        @foreach($nearbyReports as $nearby)
                            <div class="p-3 rounded bg-amber-500/15 border border-amber-700/30 flex items-center justify-between gap-2">
                                <div>
                                    <a href="{{ route('admin.verification.show', $nearby->id) }}"
                                        class="font-mono-tech font-bold text-[#263B36] hover:text-[#C76B45] underline">
                                        {{ $nearby->ticket_code }}
                                    </a>
                                    <p class="text-[11px] text-[#353A37] line-clamp-1">{{ $nearby->description }}</p>
                                    <span class="font-mono-tech text-[10px] uppercase text-amber-900 font-semibold">
                                        Status: {{ $nearby->status }}
                                    </span>
                                </div>
                                <span class="px-2 py-1 rounded bg-[#C76B45] text-white font-mono-tech font-bold text-[11px] shrink-0">
                                    {{ $nearby->distance_meters }} m
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10 text-xs text-[#263B36]">
                        <span class="font-semibold">&#10003; Tidak Ada Indikasi Duplikat Spasial:</span>
                        Tidak ditemukan laporan aktif lain dalam radius 30 meter dari titik koordinat ini.
                    </div>
                @endif
            </div>

            <!-- Riwayat Audit Log Khusus Tiket Ini -->
            <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4">
                <div class="pb-2.5 mb-3 border-b border-[#263B36]/10 flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-[#263B36]">
                        Riwayat Audit Log Tiket ({{ $report->ticket_code }})
                    </h4>
                    <span class="font-mono-tech text-[11px] text-[#353A37]/70">
                        {{ $report->auditLogs->count() }} LOG
                    </span>
                </div>

                <div class="space-y-2.5 text-xs">
                    @forelse($report->auditLogs as $log)
                        <div class="p-3 rounded bg-[#E9E5DA] border border-[#263B36]/10 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono-tech font-bold text-[#263B36] text-[11px]">
                                    {{ $log->action_name }}
                                </span>
                                <span class="font-mono-tech text-[10px] text-[#353A37]/70">
                                    {{ $log->created_at ? \Illuminate\Support\Carbon::parse($log->created_at)->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-[#353A37]/80">
                                Oleh: <strong>{{ $log->user->name ?? 'Sistem' }}</strong> ({{ strtoupper($log->user->role ?? 'system') }})
                            </p>
                            <div class="inline-flex items-center gap-1.5 font-mono-tech text-[10px] font-semibold uppercase pt-0.5">
                                <span class="px-1.5 py-0.5 rounded bg-white text-[#353A37] border border-[#263B36]/20">
                                    {{ $log->previous_status ?? 'none' }}
                                </span>
                                <span class="text-[#C76B45]">&rarr;</span>
                                <span class="px-1.5 py-0.5 rounded bg-[#263B36] text-white">
                                    {{ $log->new_status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-[#353A37]/60 py-3 text-center">
                            Belum ada riwayat perubahan status untuk tiket ini.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('detailReportMap');
        if (!mapEl || typeof L === 'undefined') return;

        const lat = {{ (float) $report->latitude }};
        const lng = {{ (float) $report->longitude }};

        const map = L.map(mapEl).setView([lat, lng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Lingkaran radius 30 meter (Haversine Proximity Zone)
        L.circle([lat, lng], {
            color: '#C76B45',
            fillColor: '#C76B45',
            fillOpacity: 0.18,
            weight: 2,
            radius: 30
        }).addTo(map);

        // Marker utama laporan
        L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`<b>{{ $report->ticket_code }}</b><br>{{ addslashes(Str::limit($report->description, 80)) }}<br>Radius Deteksi: 30m`)
            .openPopup();

        setTimeout(() => map.invalidateSize(), 150);
    });
</script>
@endsection
