@extends('layouts.app')

@section('title', 'Ikhtisar Operasional — ROVIX')
@section('header', 'Ikhtisar Operasional & Pemantauan Geospasial')
@section('subheader', 'Dinas Pekerjaan Umum • Pemantauan Titik Kerusakan Jalan, Validasi AI, & Disposisi SPK')

@section('content')
<!-- Metrik Operasional Utama (Tanpa Dekorasi Emoji) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#F4F1EA] rounded-lg p-5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[11px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Total Laporan Masuk</span>
            <span class="w-2 h-2 rounded-full bg-[#263B36]"></span>
        </div>
        <p class="text-3xl font-bold font-mono-tech text-[#263B36] mt-2">{{ $stats['total_reports'] }}</p>
        <p class="text-xs text-[#353A37]/75 mt-2 pt-2 border-t border-[#263B36]/10">
            <span class="font-mono-tech font-semibold text-[#C76B45]">{{ $stats['submitted_reports'] }}</span> tiket menunggu verifikasi
        </p>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg p-5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[11px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Penanganan Lapangan</span>
            <span class="w-2 h-2 rounded-full bg-[#C76B45]"></span>
        </div>
        <p class="text-3xl font-bold font-mono-tech text-[#C76B45] mt-2">{{ $stats['in_progress_reports'] }}</p>
        <p class="text-xs text-[#353A37]/75 mt-2 pt-2 border-t border-[#263B36]/10">
            <span class="font-mono-tech font-semibold text-[#263B36]">{{ $stats['total_officers'] }}</span> personel teknis aktif
        </p>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg p-5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[11px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Perbaikan Selesai</span>
            <span class="w-2 h-2 rounded-full bg-[#A7B5A0]"></span>
        </div>
        <p class="text-3xl font-bold font-mono-tech text-[#263B36] mt-2">{{ $stats['resolved_reports'] }}</p>
        <p class="text-xs text-[#353A37]/75 mt-2 pt-2 border-t border-[#263B36]/10">
            Indeks Kepuasan: <span class="font-mono-tech font-semibold text-[#263B36]">{{ $stats['avg_rating'] }} / 5.0</span>
        </p>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg p-5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[11px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Cakupan & Akun RBAC</span>
            <span class="w-2 h-2 rounded-full bg-[#353A37]"></span>
        </div>
        <p class="text-3xl font-bold font-mono-tech text-[#353A37] mt-2">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-[#353A37]/75 mt-2 pt-2 border-t border-[#263B36]/10">
            <span class="font-mono-tech font-semibold">{{ $stats['total_districts'] }}</span> Kecamatan • <span class="font-mono-tech font-semibold">{{ $stats['total_categories'] }}</span> Kategori
        </p>
    </div>
</div>

<!-- Peta Interaktif Sebaran Kerusakan Jalan (Leaflet.js) & Pintasan Modul -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[#263B36]/15 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Peta Sebaran Titik Kerusakan Jalan (Web GIS)</h3>
                <p class="text-[11px] text-[#353A37]/70 mt-0.5">OpenStreetMap • Koordinat Geotagging Laporan Aktif</p>
            </div>
            <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase bg-[#A7B5A0]/30 text-[#263B36] rounded border border-[#263B36]/20">
                GPS AKTIF
            </span>
        </div>
        <div id="adminMap" class="h-80 w-full z-0"></div>
    </div>

    <!-- Panel Aksi Cepat Manajemen Sistem -->
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 flex flex-col justify-between">
        <div>
            <div class="pb-3 mb-4 border-b border-[#263B36]/10">
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Manajemen & Otorisasi Sistem</h3>
                <p class="text-[11px] text-[#353A37]/70 mt-0.5">Pintasan pengelolaan pengguna, wilayah, dan operasional</p>
            </div>

            <div class="space-y-2.5">
                <a href="{{ route('admin.users.index') }}" class="block p-3.5 rounded bg-[#263B36] text-white hover:bg-[#1d2e2a] transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs">Manajemen Pengguna & RBAC</span>
                        <span class="text-[11px] font-mono-tech text-[#C76B45]">BUKA &rarr;</span>
                    </div>
                    <p class="text-[11px] text-[#E9E5DA]/75 mt-1">Kelola hak akses Warga, Petugas Lapangan, dan Admin Dinas</p>
                </a>

                <a href="{{ route('admin.users.create') }}" class="block p-3.5 rounded bg-[#C76B45] text-white hover:bg-[#b25c39] transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs">Registrasi Akun Personel / Pengguna</span>
                        <span class="text-[11px] font-mono-tech text-white">TAMBAH +</span>
                    </div>
                    <p class="text-[11px] text-white/85 mt-1">Buat akun petugas teknis baru & penugasan zona wilayah</p>
                </a>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('admin.verifications.index') }}" class="p-3 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Verifikasi GIS</p>
                        <p class="text-[11px] text-[#353A37]/70 mt-0.5">Validasi & Audit</p>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="p-3 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Disposisi SPK</p>
                        <p class="text-[11px] text-[#353A37]/70 mt-0.5">Penugasan & SLA</p>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="p-3 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Master Kategori</p>
                        <p class="text-[11px] text-[#353A37]/70 mt-0.5">Klasifikasi Rusak</p>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="p-3 rounded bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#263B36]/15 transition">
                        <p class="font-semibold text-xs text-[#263B36]">Master Wilayah</p>
                        <p class="text-[11px] text-[#353A37]/70 mt-0.5">Zona Kecamatan</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Antrean Laporan Terbaru & SPK Aktif -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[#263B36]/15 flex items-center justify-between">
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Tiket Laporan Kerusakan Terbaru</h3>
            <span class="text-[11px] font-mono-tech text-[#353A37]/70">5 TIKET TERAKHIR</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[11px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Kode Tiket</th>
                        <th class="px-4 py-2.5">Deskripsi & Lokasi</th>
                        <th class="px-4 py-2.5">Validasi AI</th>
                        <th class="px-4 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($recentReports as $report)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-4 py-3 font-mono-tech font-semibold text-[#263B36]">{{ $report->ticket_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37] line-clamp-1">{{ $report->description }}</p>
                            <p class="text-[11px] text-[#353A37]/70 mt-0.5">{{ $report->district->name ?? '-' }} • {{ $report->category->category_name ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 font-mono-tech">
                            <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/30 text-[#263B36] font-medium text-[11px]">
                                {{ $report->ai_confidence }}%
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px]
                                {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                                {{ $report->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-[#353A37]/60">Belum ada data laporan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[#263B36]/15 flex items-center justify-between">
            <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Surat Perintah Kerja (SPK) Aktif</h3>
            <span class="text-[11px] font-mono-tech text-[#353A37]/70">PENUGASAN LAPANGAN</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[11px] uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Nomor SPK</th>
                        <th class="px-4 py-2.5">Personel Pelaksana</th>
                        <th class="px-4 py-2.5">Batas SLA</th>
                        <th class="px-4 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#263B36]/10">
                    @forelse($activeWorkOrders as $wo)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-4 py-3 font-mono-tech font-semibold text-[#263B36]">{{ $wo->spk_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37]">{{ $wo->officer->name ?? '-' }}</p>
                            <p class="text-[11px] font-mono-tech text-[#353A37]/70 mt-0.5">{{ $wo->officer->phone_number ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 font-mono-tech text-[#353A37]">{{ $wo->target_sla?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#A7B5A0]/40 text-[#263B36]">
                                {{ $wo->work_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-[#353A37]/60">Belum ada SPK diterbitkan.</td>
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
