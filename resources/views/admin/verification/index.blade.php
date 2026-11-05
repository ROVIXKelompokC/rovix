@extends('layouts.app')

@section('title', 'Verifikasi Laporan & Pemetaan Web GIS — ROVIX')
@section('header', 'Verifikasi Laporan & Pemetaan Web GIS (FR-06 & FR-07)')
@section('subheader', 'Validasi aduan masyarakat, peta interaktif Leaflet.js, deteksi duplikasi spasial Haversine (< 30m), dan rekam jejak Audit Log')

@section('content')
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

    <!-- Baris Ringkasan Metrik Verifikasi -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
                <span>Total Tiket Masuk</span>
                <span class="w-2 h-2 rounded-full bg-[#263B36]"></span>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['total'] }}</p>
                <span class="text-[11px] text-[#353A37]/75">
                    <strong class="font-mono-tech text-[#263B36]">{{ $stats['ai_valid'] }}</strong> lolos AI Vision
                </span>
            </div>
        </div>

        <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
                <span>Antrean Verifikasi</span>
                <span class="w-2 h-2 rounded-full bg-[#C76B45]"></span>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['submitted'] }}</p>
                <span class="text-[11px] font-mono-tech text-[#C76B45] uppercase font-semibold">Submitted</span>
            </div>
        </div>

        <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
                <span>Terverifikasi & Diproses</span>
                <span class="w-2 h-2 rounded-full bg-[#A7B5A0]"></span>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <p class="text-2xl font-bold font-mono-tech text-[#263B36]">
                    {{ $stats['verified'] + $stats['in_progress'] + $stats['resolved'] }}
                </p>
                <span class="text-[11px] text-[#353A37]/75">
                    <strong class="font-mono-tech text-[#263B36]">{{ $stats['verified'] }}</strong> siap SPK
                </span>
            </div>
        </div>

        <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
                <span>Ditolak / Spam</span>
                <span class="w-2 h-2 rounded-full bg-red-700"></span>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <p class="text-2xl font-bold font-mono-tech text-red-700">{{ $stats['rejected'] }}</p>
                <span class="text-[11px] font-mono-tech text-red-700/80 uppercase font-semibold">Rejected</span>
            </div>
        </div>
    </div>

    <!-- SECTION 1: Peta Interaktif Sebaran Laporan (Web GIS Leaflet.js - FR-06 / SCRUM-9) -->
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-4 py-3 border-b border-[#263B36]/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                        Peta Interaktif Sebaran Laporan Kerusakan Jalan (Web GIS Leaflet.js)
                    </h3>
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase bg-[#A7B5A0]/35 text-[#263B36] rounded border border-[#263B36]/20">
                        {{ $mapReports->count() }} TITIK GPS
                    </span>
                </div>
                <p class="text-[11px] text-[#353A37]/70 mt-0.5">
                    Klik marker pada peta untuk melihat foto laporan, skor keyakinan AI Vision, dan membuka halaman verifikasi tiket.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-[10px] font-mono-tech">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#C76B45]/15 text-[#C76B45] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#C76B45]"></span> Submitted
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500/15 text-amber-800 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span> Verified
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#263B36]/15 text-[#263B36] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#263B36]"></span> In Progress
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#A7B5A0]/40 text-[#263B36] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-700"></span> Resolved
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-100 text-red-800 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-red-700"></span> Rejected
                </span>
                <button type="button" id="resetMapBoundsBtn"
                    class="px-2.5 py-1 rounded bg-[#263B36] text-[#E9E5DA] hover:bg-[#1d2e2a] font-semibold transition">
                    Reset Fokus Peta
                </button>
            </div>
        </div>

        <div id="verificationGisMap" class="w-full h-[420px] z-0"></div>
    </div>

    <!-- SECTION 2: Filter & Tabel Antrean Verifikasi Tiket Aduan (FR-07 / SCRUM-8 & SCRUM-9) -->
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="p-3.5 border-b border-[#263B36]/15">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                        Antrean Verifikasi & Validasi Tiket Laporan Warga
                    </h3>
                    <p class="text-[11px] text-[#353A37]/70">
                        Periksa keabsahan foto AI Vision, cek indikasi duplikasi radius &lt; 30m (Haversine), lalu setujui (Approve) atau tolak spam (Reject).
                    </p>
                </div>

                <!-- Quick Filter Tabs Status -->
                <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-mono-tech">
                    <a href="{{ route('admin.verification.index', array_merge(request()->except('page'), ['status' => 'all'])) }}"
                        class="px-2.5 py-1 rounded border transition {{ (!request('status') || request('status') === 'all') ? 'bg-[#263B36] text-white border-[#263B36] font-semibold' : 'bg-[#E9E5DA] text-[#353A37] border-[#263B36]/15 hover:bg-[#A7B5A0]/30' }}">
                        Semua ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.verification.index', array_merge(request()->except('page'), ['status' => 'submitted'])) }}"
                        class="px-2.5 py-1 rounded border transition {{ request('status') === 'submitted' ? 'bg-[#C76B45] text-white border-[#C76B45] font-semibold' : 'bg-[#E9E5DA] text-[#C76B45] border-[#C76B45]/30 hover:bg-[#C76B45]/15' }}">
                        Menunggu Verifikasi ({{ $stats['submitted'] }})
                    </a>
                    <a href="{{ route('admin.verification.index', array_merge(request()->except('page'), ['status' => 'verified'])) }}"
                        class="px-2.5 py-1 rounded border transition {{ request('status') === 'verified' ? 'bg-amber-700 text-white border-amber-700 font-semibold' : 'bg-[#E9E5DA] text-amber-900 border-amber-700/25 hover:bg-amber-500/15' }}">
                        Verified ({{ $stats['verified'] }})
                    </a>
                    <a href="{{ route('admin.verification.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}"
                        class="px-2.5 py-1 rounded border transition {{ request('status') === 'rejected' ? 'bg-red-700 text-white border-red-700 font-semibold' : 'bg-[#E9E5DA] text-red-800 border-red-700/25 hover:bg-red-500/15' }}">
                        Rejected ({{ $stats['rejected'] }})
                    </a>
                </div>
            </div>

            <!-- Form Pencarian & Filter Multi-Kriteria -->
            <form action="{{ route('admin.verification.index') }}" method="GET"
                class="mt-3 pt-3 border-t border-[#263B36]/10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5">
                <div class="lg:col-span-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode tiket (RVX-...), deskripsi lokasi, atau nama pelapor..."
                        class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs focus:border-[#C76B45] outline-none">
                </div>

                <div class="lg:col-span-2">
                    <select name="status" class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs focus:border-[#C76B45] outline-none">
                        <option value="all">Semua Status Tiket</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted (Menunggu)</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified (Terverifikasi)</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress (SPK Berjalan)</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak/Spam)</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select name="district_id" class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs focus:border-[#C76B45] outline-none">
                        <option value="">Semua Wilayah Kecamatan</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ (string) request('district_id') === (string) $district->id ? 'selected' : '' }}>
                                {{ $district->name }} ({{ $district->city_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select name="category_id" class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-xs focus:border-[#C76B45] outline-none">
                        <option value="">Semua Kategori Kerusakan</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                [Lv.{{ $category->severity_level }}] {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 px-3.5 py-2 bg-[#263B36] hover:bg-[#1d2e2a] text-white rounded text-xs font-semibold transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'district_id', 'category_id']))
                        <a href="{{ route('admin.verification.index') }}"
                            class="px-3 py-2 bg-[#A7B5A0]/35 hover:bg-[#A7B5A0]/55 text-[#263B36] rounded text-xs font-semibold transition text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Antrean Verifikasi -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Kode Tiket & Pelapor</th>
                        <th class="px-4 py-2.5">Foto & Detail Kerusakan</th>
                        <th class="px-4 py-2.5">Lokasi & Deteksi Duplikat</th>
                        <th class="px-4 py-2.5">Validasi AI Vision</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5 text-right">Tindakan Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($reports as $report)
                        @php
                            $photoSrc = str_starts_with((string) $report->photo_url, 'http')
                                ? $report->photo_url
                                : asset('storage/' . ltrim((string) $report->photo_url, '/'));
                            $severity = (int) ($report->category->severity_level ?? 1);
                            $duplicates = $report->nearby_duplicates ?? collect();
                        @endphp
                        <tr class="hover:bg-[#E9E5DA]/60 align-top">
                            <!-- Kolom 1: Kode Tiket & Pelapor -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.verification.show', $report->id) }}"
                                    class="font-mono-tech font-bold text-[#263B36] hover:text-[#C76B45] underline decoration-[#263B36]/30 text-xs">
                                    {{ $report->ticket_code }}
                                </a>
                                <p class="text-[11px] font-semibold text-[#353A37] mt-1">
                                    {{ $report->user->name ?? 'Warga Pelapor' }}
                                </p>
                                <p class="font-mono-tech text-[10px] text-[#353A37]/70">
                                    {{ $report->created_at?->format('d/m/Y H:i') }} WIB
                                </p>
                            </td>

                            <!-- Kolom 2: Foto & Detail Kerusakan -->
                            <td class="px-4 py-3 min-w-[260px] max-w-md">
                                <div class="flex items-start gap-3">
                                    <a href="{{ route('admin.verification.show', $report->id) }}"
                                        class="w-16 h-14 rounded border border-[#263B36]/20 bg-[#263B36]/10 overflow-hidden shrink-0 flex items-center justify-center relative group">
                                        <img src="{{ $photoSrc }}"
                                            alt="Foto {{ $report->ticket_code }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <div class="hidden flex-col items-center justify-center text-[#263B36]/75 p-1 text-center">
                                            <svg class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                                            </svg>
                                            <span class="text-[8px] font-mono-tech uppercase leading-none mt-0.5">CITRA JALAN</span>
                                        </div>
                                    </a>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                            <span class="px-1.5 py-0.5 rounded bg-[#263B36]/10 text-[#263B36] font-semibold text-[10px]">
                                                {{ $report->category->category_name ?? 'Kerusakan Jalan' }}
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded font-mono-tech font-bold text-[10px]
                                                {{ $severity >= 4 ? 'bg-[#C76B45]/20 text-[#C76B45]' : 'bg-[#A7B5A0]/40 text-[#263B36]' }}">
                                                Severity Lv.{{ $severity }}/5
                                            </span>
                                        </div>
                                        <p class="text-[#353A37] font-medium line-clamp-2 leading-snug">
                                            {{ $report->description }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 3: Lokasi & Deteksi Duplikat Haversine -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-[#263B36]">
                                    {{ $report->district->name ?? '-' }}
                                    @if($report->district?->city_code)
                                        <span class="font-mono-tech text-[10px] text-[#353A37]/65">({{ $report->district->city_code }})</span>
                                    @endif
                                </div>
                                <div class="font-mono-tech text-[10px] text-[#353A37]/75 mt-0.5">
                                    {{ number_format((float) $report->latitude, 5) }}, {{ number_format((float) $report->longitude, 5) }}
                                </div>

                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                    <button type="button"
                                        onclick="focusReportMarker({{ $report->id }}, {{ (float) $report->latitude }}, {{ (float) $report->longitude }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/20 font-mono-tech text-[10px] font-semibold transition">
                                        <svg class="w-3 h-3 text-[#C76B45]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        Fokus Peta
                                    </button>

                                    @if($duplicates->isNotEmpty())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500/20 text-amber-900 border border-amber-700/30 font-mono-tech text-[10px] font-semibold"
                                            title="Terdeteksi {{ $duplicates->count() }} laporan lain dalam radius < 30m ({{ $duplicates->first()->ticket_code }}: {{ $duplicates->first()->distance_meters }}m)">
                                            &#9888; Duplikat &lt;30m ({{ $duplicates->first()->distance_meters }}m)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-[#A7B5A0]/25 text-[#263B36] font-mono-tech text-[10px]">
                                            Radius Aman (&gt;30m)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 4: Validasi AI Vision -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @if($report->ai_validation)
                                        <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/40 text-[#263B36] font-mono-tech font-bold text-[10px] uppercase">
                                            AI Valid
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-[#C76B45]/20 text-[#C76B45] font-mono-tech font-bold text-[10px] uppercase">
                                            Perlu Cek Manual
                                        </span>
                                    @endif
                                    <span class="font-mono-tech font-bold text-xs text-[#263B36]">
                                        {{ number_format((float) $report->ai_confidence, 1) }}%
                                    </span>
                                </div>
                                <div class="w-28 h-1.5 bg-[#263B36]/15 rounded-full overflow-hidden mt-1.5">
                                    <div class="h-full rounded-full {{ $report->ai_confidence >= 75 ? 'bg-[#263B36]' : 'bg-[#C76B45]' }}"
                                        style="width: {{ min(100, max(5, (float) $report->ai_confidence)) }}%"></div>
                                </div>
                            </td>

                            <!-- Kolom 5: Status Tiket -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($report->status === 'submitted')
                                    <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#C76B45]/20 text-[#C76B45] border border-[#C76B45]/30">
                                        Submitted
                                    </span>
                                @elseif($report->status === 'verified')
                                    <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-[10px] bg-amber-500/20 text-amber-900 border border-amber-600/30">
                                        Verified
                                    </span>
                                @elseif($report->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#263B36] text-white">
                                        In Progress
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#A7B5A0]/45 text-[#263B36]">
                                        Resolved
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded font-mono-tech font-semibold uppercase text-[10px] bg-red-100 text-red-800 border border-red-300">
                                        Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 6: Tindakan Verifikasi (SCRUM-8 & SCRUM-9) -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex flex-wrap items-center justify-end gap-1.5 min-w-[210px]">
                                    <a href="{{ route('admin.verification.show', $report->id) }}"
                                        class="px-2.5 py-1.5 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/20 rounded text-xs font-semibold transition">
                                        Detail
                                    </a>

                                    @if($report->status === 'submitted')
                                        <button type="button"
                                            onclick="openApproveModal({{ $report->id }}, '{{ $report->ticket_code }}', '{{ addslashes(Str::limit($report->description, 70)) }}')"
                                            class="px-2.5 py-1.5 bg-[#263B36] hover:bg-[#1d2e2a] text-white rounded text-xs font-semibold transition">
                                            Approve
                                        </button>

                                        <button type="button"
                                            onclick="openRejectModal({{ $report->id }}, '{{ $report->ticket_code }}', '{{ addslashes(Str::limit($report->description, 70)) }}')"
                                            class="px-2.5 py-1.5 bg-[#C76B45] hover:bg-[#b25c39] text-white rounded text-xs font-semibold transition">
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-[#353A37]/60">
                                Tidak ada laporan kerusakan jalan yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-4 py-3 border-t border-[#263B36]/10 bg-[#E9E5DA]/40">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- SECTION 3: Panel Riwayat Aktivitas Audit Log (SCRUM-8 & SCRUM-9) -->
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-4 py-3 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">
                    Riwayat Aktivitas & Audit Log Verifikasi (Immutable Audit Trail)
                </h3>
                <p class="text-[11px] text-[#353A37]/70">
                    Rekam jejak otomatis setiap perubahan status laporan oleh Admin Verifikator maupun Petugas Lapangan.
                </p>
            </div>
            <span class="px-2.5 py-0.5 rounded bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase font-semibold">
                10 LOG TERBARU
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Waktu Log</th>
                        <th class="px-4 py-2.5">Kode Tiket & Wilayah</th>
                        <th class="px-4 py-2.5">Aktor / Verifikator</th>
                        <th class="px-4 py-2.5">Aksi & Catatan Verifikasi</th>
                        <th class="px-4 py-2.5">Transisi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-[#E9E5DA]/60">
                            <td class="px-4 py-2.5 font-mono-tech text-[11px] text-[#353A37] whitespace-nowrap">
                                {{ $log->created_at ? \Illuminate\Support\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($log->report)
                                    <a href="{{ route('admin.verification.show', $log->report->id) }}"
                                        class="font-mono-tech font-bold text-[#263B36] hover:text-[#C76B45] underline">
                                        {{ $log->report->ticket_code }}
                                    </a>
                                    <span class="block text-[10px] text-[#353A37]/70">
                                        {{ $log->report->district->name ?? '-' }}
                                    </span>
                                @else
                                    <span class="font-mono-tech text-[#353A37]/60">Tiket #{{ $log->report_id }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <div class="font-semibold text-[#263B36]">{{ $log->user->name ?? 'Sistem ROVIX' }}</div>
                                <div class="font-mono-tech text-[10px] uppercase text-[#353A37]/70">
                                    Role: {{ $log->user->role ?? 'system' }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="font-mono-tech font-semibold text-[11px] text-[#263B36]">
                                    {{ $log->action_name }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 font-mono-tech text-[10px] font-semibold uppercase">
                                    <span class="px-2 py-0.5 rounded bg-[#E9E5DA] text-[#353A37] border border-[#263B36]/20">
                                        {{ $log->previous_status ?? 'none' }}
                                    </span>
                                    <span class="text-[#C76B45] font-bold">&rarr;</span>
                                    <span class="px-2 py-0.5 rounded
                                        {{ $log->new_status === 'verified' ? 'bg-amber-500/20 text-amber-900 border border-amber-600/30' : ($log->new_status === 'rejected' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-[#263B36] text-white') }}">
                                        {{ $log->new_status }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-[#353A37]/60">
                                Belum ada catatan aktivitas di dalam Audit Log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI APPROVE (VERIFIKASI LAPORAN - SCRUM-8) -->
<div id="approveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4">
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/25 max-w-md w-full p-5 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-[#263B36]/15">
            <div>
                <h4 class="font-bold text-sm text-[#263B36]">Verifikasi Laporan Kerusakan Jalan</h4>
                <p id="approveModalTicket" class="font-mono-tech text-xs text-[#C76B45] font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeApproveModal()" class="text-[#353A37]/60 hover:text-[#263B36] text-lg font-bold">&times;</button>
        </div>

        <form id="approveForm" method="POST" action="" class="mt-3 space-y-3">
            @csrf
            <p id="approveModalDesc" class="text-xs text-[#353A37]/85 bg-[#E9E5DA] p-2.5 rounded border border-[#263B36]/10"></p>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">
                    Catatan Verifikasi Admin (Opsional)
                </label>
                <textarea name="notes" rows="2"
                    placeholder="Contoh: Koordinat & foto valid, diteruskan ke antrean disposisi SPK..."
                    class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-xs text-[#353A37] focus:border-[#263B36] outline-none"></textarea>
                <p class="text-[10px] text-[#353A37]/70 mt-1">
                    Aksi ini akan mengubah status tiket menjadi <strong class="font-mono-tech">VERIFIED</strong> dan mencatatnya ke <strong class="font-mono-tech">audit_logs</strong>.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#263B36]/10">
                <button type="button" onclick="closeApproveModal()"
                    class="px-3.5 py-1.5 rounded bg-[#E9E5DA] text-[#353A37] text-xs font-semibold hover:bg-[#A7B5A0]/30">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-1.5 rounded bg-[#263B36] hover:bg-[#1d2e2a] text-white text-xs font-semibold transition">
                    Konfirmasi Verifikasi (Approve)
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI REJECT (TOLAK LAPORAN SPAM/DUPLIKAT - SCRUM-8) -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4">
    <div class="bg-[#F4F1EA] rounded-lg border border-[#C76B45]/40 max-w-md w-full p-5 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-[#263B36]/15">
            <div>
                <h4 class="font-bold text-sm text-[#C76B45]">Tolak Laporan (Indikasi Spam / Duplikat)</h4>
                <p id="rejectModalTicket" class="font-mono-tech text-xs text-[#263B36] font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-[#353A37]/60 hover:text-[#263B36] text-lg font-bold">&times;</button>
        </div>

        <form id="rejectForm" method="POST" action="" class="mt-3 space-y-3">
            @csrf
            <p id="rejectModalDesc" class="text-xs text-[#353A37]/85 bg-[#E9E5DA] p-2.5 rounded border border-[#263B36]/10"></p>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">
                    Pilih Cepat Alasan Penolakan
                </label>
                <div class="flex flex-wrap gap-1.5 mb-2">
                    <button type="button" onclick="setRejectReason('Foto tidak menampilkan objek kerusakan jalan (Spam)')"
                        class="px-2 py-1 rounded bg-[#E9E5DA] hover:bg-[#C76B45]/20 text-[#353A37] text-[10px] font-medium border border-[#263B36]/15">
                        Foto Bukan Jalan Rusak
                    </button>
                    <button type="button" onclick="setRejectReason('Laporan duplikat dengan tiket aktif dalam radius < 30 meter')"
                        class="px-2 py-1 rounded bg-[#E9E5DA] hover:bg-[#C76B45]/20 text-[#353A37] text-[10px] font-medium border border-[#263B36]/15">
                        Duplikat Lokasi (&lt; 30m)
                    </button>
                    <button type="button" onclick="setRejectReason('Koordinat GPS tidak sesuai / berada di luar wilayah kerja Dinas PU')"
                        class="px-2 py-1 rounded bg-[#E9E5DA] hover:bg-[#C76B45]/20 text-[#353A37] text-[10px] font-medium border border-[#263B36]/15">
                        Koordinat Tidak Valid
                    </button>
                </div>

                <label class="block text-xs font-semibold text-[#263B36] mb-1">
                    Alasan Penolakan <span class="text-[#C76B45]">*</span>
                </label>
                <textarea id="rejectReasonInput" name="reason" rows="2" required
                    placeholder="Tuliskan alasan penolakan laporan..."
                    class="w-full px-3 py-2 rounded border border-[#263B36]/25 bg-white text-xs text-[#353A37] focus:border-[#C76B45] outline-none"></textarea>
                <p class="text-[10px] text-[#353A37]/70 mt-1">
                    Status laporan akan diubah menjadi <strong class="font-mono-tech text-[#C76B45]">REJECTED</strong> dan alasan tercatat di <strong class="font-mono-tech">audit_logs</strong>.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#263B36]/10">
                <button type="button" onclick="closeRejectModal()"
                    class="px-3.5 py-1.5 rounded bg-[#E9E5DA] text-[#353A37] text-xs font-semibold hover:bg-[#A7B5A0]/30">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-1.5 rounded bg-[#C76B45] hover:bg-[#b25c39] text-white text-xs font-semibold transition">
                    Tolak Laporan (Reject)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let verificationMap = null;
    const markersById = {};
    const defaultBounds = [];

    function getStatusColor(status) {
        switch (status) {
            case 'submitted': return '#C76B45';
            case 'verified': return '#D97706';
            case 'in_progress': return '#263B36';
            case 'resolved': return '#2F6F4E';
            case 'rejected': return '#991B1B';
            default: return '#263B36';
        }
    }

    function createCustomMarkerIcon(status, severity) {
        const bgColor = getStatusColor(status);
        return L.divIcon({
            className: 'custom-rovix-marker',
            html: `
                <div style="
                    width: 28px;
                    height: 28px;
                    border-radius: 9999px;
                    background: ${bgColor};
                    border: 2.5px solid #F4F1EA;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.35);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #FFFFFF;
                    font-family: 'JetBrains Mono', monospace;
                    font-size: 11px;
                    font-weight: 700;
                " title="Severity Lv.${severity}">${severity}</div>
            `,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14]
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const mapContainer = document.getElementById('verificationGisMap');
        if (!mapContainer || typeof L === 'undefined') return;

        verificationMap = L.map(mapContainer).setView([-6.2880, 106.8420], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors • ROVIX Web GIS'
        }).addTo(verificationMap);

        const reportsData = @json($mapReports);

        reportsData.forEach(item => {
            if (!item.latitude || !item.longitude) return;

            const latLng = [item.latitude, item.longitude];
            defaultBounds.push(latLng);

            const statusColor = getStatusColor(item.status);
            const marker = L.marker(latLng, {
                icon: createCustomMarkerIcon(item.status, item.severity_level)
            }).addTo(verificationMap);

            const popupHtml = `
                <div style="min-width: 230px; max-width: 260px; font-family: 'Plus Jakarta Sans', sans-serif; color: #353A37;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:6px; margin-bottom:6px;">
                        <span style="font-family:'JetBrains Mono',monospace; font-weight:700; font-size:12px; color:#263B36;">
                            ${item.ticket_code}
                        </span>
                        <span style="background:${statusColor}; color:#fff; font-family:'JetBrains Mono',monospace; font-size:9px; font-weight:700; padding:2px 6px; border-radius:4px; text-transform:uppercase;">
                            ${item.status}
                        </span>
                    </div>
                    <div style="font-size:11px; font-weight:600; color:#263B36; margin-bottom:4px;">
                        ${item.category_name} (Lv.${item.severity_level}/5)
                    </div>
                    <p style="font-size:11px; line-height:1.35; margin:0 0 6px 0; color:#353A37;">
                        ${item.description}
                    </p>
                    <div style="font-size:10px; color:#555; background:#E9E5DA; padding:5px 7px; border-radius:4px; margin-bottom:8px; font-family:'JetBrains Mono',monospace;">
                        <div>Wilayah: <b>${item.district_name}</b></div>
                        <div>Skor AI: <b>${item.ai_confidence}%</b> (${item.ai_validation ? 'VALID' : 'CEK MANUAL'})</div>
                        <div>Pelapor: ${item.reporter_name}</div>
                    </div>
                    <a href="${item.show_url}" style="display:block; text-align:center; background:#263B36; color:#E9E5DA; text-decoration:none; font-size:11px; font-weight:600; padding:6px 10px; border-radius:4px;">
                        Tinjau & Verifikasi Tiket &rarr;
                    </a>
                </div>
            `;

            marker.bindPopup(popupHtml);
            markersById[item.id] = marker;
        });

        if (defaultBounds.length > 0) {
            verificationMap.fitBounds(defaultBounds, { padding: [45, 45], maxZoom: 15 });
        }

        const resetBtn = document.getElementById('resetMapBoundsBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                if (defaultBounds.length > 0) {
                    verificationMap.fitBounds(defaultBounds, { padding: [45, 45], maxZoom: 15 });
                } else {
                    verificationMap.setView([-6.2880, 106.8420], 12);
                }
            });
        }

        setTimeout(() => verificationMap.invalidateSize(), 150);
        if (window.ResizeObserver) {
            new ResizeObserver(() => verificationMap.invalidateSize()).observe(mapContainer);
        }
    });

    function focusReportMarker(reportId, lat, lng) {
        if (!verificationMap) return;
        const mapEl = document.getElementById('verificationGisMap');
        if (mapEl) {
            mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        verificationMap.setView([lat, lng], 16, { animate: true });
        if (markersById[reportId]) {
            markersById[reportId].openPopup();
        }
    }

    function openApproveModal(id, ticketCode, description) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        document.getElementById('approveModalTicket').textContent = ticketCode;
        document.getElementById('approveModalDesc').textContent = description;
        form.action = `/admin/verification/${id}/approve`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openRejectModal(id, ticketCode, description) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        document.getElementById('rejectModalTicket').textContent = ticketCode;
        document.getElementById('rejectModalDesc').textContent = description;
        document.getElementById('rejectReasonInput').value = '';
        form.action = `/admin/verification/${id}/reject`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setRejectReason(text) {
        const input = document.getElementById('rejectReasonInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }
</script>
@endsection
