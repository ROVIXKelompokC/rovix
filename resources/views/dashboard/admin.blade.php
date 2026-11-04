@extends('layouts.app')

@section('title', 'Ikhtisar Operasional — ROVIX')
@section('header', 'Ikhtisar Operasional & Pemantauan Geospasial')
@section('subheader', 'Dinas Pekerjaan Umum • Pemantauan Titik Kerusakan Jalan, Validasi AI, & Disposisi SPK')

@section('content')
<!-- Metrik Operasional Utama (Kompak & Muat 100% Zoom) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Total Laporan</span>
            <span class="w-2 h-2 rounded-full bg-[#263B36] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['total_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech text-[#C76B45]">{{ $stats['submitted_reports'] }}</strong> antrean</span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>SPK Diproses</span>
            <span class="w-2 h-2 rounded-full bg-[#C76B45] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-2xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['in_progress_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech text-[#263B36]">{{ $stats['total_officers'] }}</strong> petugas</span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Perbaikan Selesai</span>
            <span class="w-2 h-2 rounded-full bg-[#A7B5A0] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-2xl font-bold font-mono-tech text-[#263B36]">{{ $stats['resolved_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75">Mutu: <strong class="font-mono-tech text-[#263B36]">{{ $stats['avg_rating'] }}/5.0</strong></span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-4 py-3 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Akun & RBAC</span>
            <span class="w-2 h-2 rounded-full bg-[#353A37] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-2xl font-bold font-mono-tech text-[#353A37]">{{ $stats['total_users'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech">{{ $stats['total_districts'] }}</strong> Zona</span>
        </div>
    </div>
</div>

<!-- Peta Interaktif Sebaran Kerusakan Jalan (Leaflet.js) & Pintasan Modul -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
    <div class="lg:col-span-8 bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden flex flex-col">
        <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Peta Sebaran Titik Kerusakan Jalan (Web GIS)</h3>
                <p class="text-[11px] text-[#353A37]/70">OpenStreetMap • Koordinat Geotagging Laporan Aktif</p>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase bg-[#A7B5A0]/30 text-[#263B36] rounded border border-[#263B36]/20">
                GPS AKTIF
            </span>
        </div>
        <div id="adminMap" class="h-56 sm:h-60 w-full z-0 flex-1 min-h-[220px]"></div>
    </div>

    <!-- Panel Aksi Cepat Manajemen Sistem -->
    <div class="lg:col-span-4 bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 flex flex-col justify-between">
        <div>
            <div class="pb-2.5 mb-3 border-b border-[#263B36]/10">
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Manajemen & Otorisasi Sistem</h3>
                <p class="text-[11px] text-[#353A37]/70">Pintasan pengelolaan pengguna, wilayah, dan operasional</p>
            </div>

            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}" class="block p-3 rounded bg-[#263B36] text-white hover:bg-[#1d2e2a] transition">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-xs">Manajemen Pengguna & RBAC</span>
                        <span class="text-[10px] font-mono-tech text-[#C76B45] shrink-0">BUKA &rarr;</span>
                    </div>
                    <p class="text-[11px] text-[#E9E5DA]/75 mt-0.5">Kelola hak akses Warga, Petugas, dan Admin</p>
                </a>

                <a href="{{ route('admin.users.create') }}" class="block p-3 rounded bg-[#C76B45] text-white hover:bg-[#b25c39] transition">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-xs">Registrasi Akun Personel</span>
                        <span class="text-[10px] font-mono-tech text-white shrink-0">TAMBAH +</span>
                    </div>
                    <p class="text-[11px] text-white/85 mt-0.5">Buat akun petugas teknis & zona wilayah</p>
                </a>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('admin.verifications.index') }}" class="p-2.5 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Verifikasi GIS</p>
                        <p class="text-[10px] text-[#353A37]/70">Validasi & Audit</p>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="p-2.5 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Disposisi SPK</p>
                        <p class="text-[10px] text-[#353A37]/70">Penugasan & SLA</p>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="p-2.5 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Master Kategori</p>
                        <p class="text-[10px] text-[#353A37]/70">Klasifikasi Rusak</p>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="p-2.5 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Master Wilayah</p>
                        <p class="text-[10px] text-[#353A37]/70">Zona Kecamatan</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Antrean Laporan Terbaru & SPK Aktif -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Tiket Laporan Kerusakan Terbaru</h3>
            <span class="text-[10px] font-mono-tech text-[#353A37]/70 shrink-0">5 TIKET TERAKHIR</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                    <tr>
                        <th class="px-3.5 py-2">Kode Tiket</th>
                        <th class="px-3.5 py-2">Deskripsi & Lokasi</th>
                        <th class="px-3.5 py-2">Skor AI</th>
                        <th class="px-3.5 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($recentReports as $report)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-3.5 py-2.5 font-mono-tech font-semibold text-[#263B36] whitespace-nowrap">{{ $report->ticket_code }}</td>
                        <td class="px-3.5 py-2.5">
                            <p class="font-semibold text-[#353A37] line-clamp-1">{{ $report->description }}</p>
                            <p class="text-[11px] text-[#353A37]/70">{{ $report->district->name ?? '-' }} • {{ $report->category->category_name ?? '-' }}</p>
                        </td>
                        <td class="px-3.5 py-2.5 font-mono-tech whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/30 text-[#263B36] font-medium text-[11px]">
                                {{ $report->ai_confidence }}%
                            </span>
                        </td>
                        <td class="px-3.5 py-2.5 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px]
                                {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                                {{ $report->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-5 text-center text-[#353A37]/60">Belum ada data laporan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-4 py-2.5 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Surat Perintah Kerja (SPK) Aktif</h3>
            <span class="text-[10px] font-mono-tech text-[#353A37]/70 shrink-0">PENUGASAN LAPANGAN</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                    <tr>
                        <th class="px-3.5 py-2">Nomor SPK</th>
                        <th class="px-3.5 py-2">Personel Pelaksana</th>
                        <th class="px-3.5 py-2">Batas SLA</th>
                        <th class="px-3.5 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($activeWorkOrders as $wo)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-3.5 py-2.5 font-mono-tech font-semibold text-[#263B36] whitespace-nowrap">{{ $wo->spk_number }}</td>
                        <td class="px-3.5 py-2.5">
                            <p class="font-semibold text-[#353A37]">{{ $wo->officer->name ?? '-' }}</p>
                            <p class="text-[11px] font-mono-tech text-[#353A37]/70">{{ $wo->officer->phone_number ?? '-' }}</p>
                        </td>
                        <td class="px-3.5 py-2.5 font-mono-tech text-[#353A37] whitespace-nowrap">{{ $wo->target_sla?->format('d/m/Y') }}</td>
                        <td class="px-3.5 py-2.5 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#A7B5A0]/40 text-[#263B36]">
                                {{ $wo->work_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-5 text-center text-[#353A37]/60">Belum ada SPK diterbitkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('adminMap').setView([-6.3016, 106.8145], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const reports = @json($recentReports);
        reports.forEach(r => {
            if (r.latitude && r.longitude) {
                L.marker([r.latitude, r.longitude])
                    .addTo(map)
                    .bindPopup(`<b>${r.ticket_code}</b><br>${r.description}<br>Status: <b>${r.status.toUpperCase()}</b>`);
            }
        });
    });
</script>
@endsection
