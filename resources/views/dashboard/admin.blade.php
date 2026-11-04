@extends('layouts.app')

@section('title', 'Dasbor Admin Dinas PU — ROVIX')
@section('header', 'Management Dashboard — Dinas Pekerjaan Umum')
@section('subheader', 'Pemantauan Sebaran Titik Kerusakan Jalan, Triage AI, Disposisi SPK, & Master Data RBAC')

@section('content')
<!-- 4 Kartu Ringkasan Metrik Operasional -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-[#F5F2EB] rounded-2xl p-5 border border-[#A7B5A0]/50 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-[#263B36] uppercase">Total Laporan Warga</p>
            <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['total_reports'] }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">{{ $stats['submitted_reports'] }} menunggu verifikasi</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-[#C76B45]/15 text-[#C76B45] flex items-center justify-center text-2xl">📸</div>
    </div>

    <div class="bg-[#F5F2EB] rounded-2xl p-5 border border-[#A7B5A0]/50 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-[#263B36] uppercase">Sedang Diproses (SPK)</p>
            <p class="text-3xl font-extrabold text-[#C76B45] mt-1">{{ $stats['in_progress_reports'] }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">{{ $stats['total_officers'] }} petugas teknis aktif</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-[#263B36]/10 text-[#263B36] flex items-center justify-center text-2xl">🛠️</div>
    </div>

    <div class="bg-[#F5F2EB] rounded-2xl p-5 border border-[#A7B5A0]/50 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-[#263B36] uppercase">Perbaikan Selesai</p>
            <p class="text-3xl font-extrabold text-[#263B36] mt-1">{{ $stats['resolved_reports'] }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">Rata-rata Rating: ⭐ {{ $stats['avg_rating'] }}/5.0</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-[#A7B5A0]/40 text-[#263B36] flex items-center justify-center text-2xl">✅</div>
    </div>

    <div class="bg-[#F5F2EB] rounded-2xl p-5 border border-[#A7B5A0]/50 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-[#263B36] uppercase">Pengguna & Zona RBAC</p>
            <p class="text-3xl font-extrabold text-[#353A37] mt-1">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-[#353A37]/75 mt-1">{{ $stats['total_districts'] }} Zona • {{ $stats['total_categories'] }} Kategori</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-[#A7B5A0]/30 text-[#263B36] flex items-center justify-center text-2xl">👥</div>
    </div>
</div>

<!-- Peta Interaktif Sebaran Kerusakan Jalan (Leaflet.js) & Shortcut Navigasi -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#A7B5A0]/30 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-[#263B36] text-sm">🗺️ Peta Sebaran Titik Aduan Kerusakan Jalan (Web GIS)</h3>
                <p class="text-xs text-[#353A37]/75">Visualisasi Leaflet.js OpenStreetMap — Klaster Titik Aduan Aktif</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-[#A7B5A0]/30 text-[#263B36] rounded-lg border border-[#A7B5A0]">
                Live GPS Sync
            </span>
        </div>
        <div id="adminMap" class="h-80 w-full z-0"></div>
    </div>

    <!-- Panel Aksi Cepat Manajemen Operasional & RBAC -->
    <div id="master-data" class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-[#263B36] text-sm mb-1">🛡️ Akses Cepat Manajemen Sistem</h3>
            <p class="text-xs text-[#353A37]/75 mb-4">Pintasan pengelolaan pengguna RBAC, verifikasi aduan, dan master data operasional</p>

            <div class="space-y-2.5">
                <a href="{{ route('admin.users.index') }}" class="block p-3.5 rounded-xl bg-[#263B36] text-white hover:bg-[#1d2e2a] transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs">👥 Manajemen Pengguna & RBAC</span>
                        <span class="text-xs text-[#C76B45] font-bold">Kelola →</span>
                    </div>
                    <p class="text-[11px] text-[#E9E5DA]/80 mt-1">Lihat, filter, dan atur peran Warga, Petugas, serta Admin</p>
                </a>

                <a href="{{ route('admin.users.create') }}" class="block p-3.5 rounded-xl bg-[#C76B45] text-white hover:bg-[#b25c39] transition shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs">➕ Tambah Akun Pengguna / Petugas</span>
                        <span class="text-xs text-[#E9E5DA] font-bold">Tambah →</span>
                    </div>
                    <p class="text-[11px] text-white/90 mt-1">Registrasi personel baru & penugasan zona wilayah</p>
                </a>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('admin.verifications.index') }}" class="p-3 rounded-xl bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#A7B5A0]/50 transition">
                        <p class="font-semibold text-xs text-[#263B36]">🗺️ Verifikasi GIS</p>
                        <p class="text-[11px] text-[#353A37]/75 mt-0.5">Triage & Audit Log</p>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="p-3 rounded-xl bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#A7B5A0]/50 transition">
                        <p class="font-semibold text-xs text-[#263B36]">🛠️ Disposisi SPK</p>
                        <p class="text-[11px] text-[#353A37]/75 mt-0.5">Penugasan & SLA</p>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="p-3 rounded-xl bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#A7B5A0]/50 transition">
                        <p class="font-semibold text-xs text-[#263B36]">🏷️ Kategori</p>
                        <p class="text-[11px] text-[#353A37]/75 mt-0.5">Level Keparahan</p>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="p-3 rounded-xl bg-[#E9E5DA] hover:bg-[#A7B5A0]/30 border border-[#A7B5A0]/50 transition">
                        <p class="font-semibold text-xs text-[#263B36]">📍 Wilayah</p>
                        <p class="text-[11px] text-[#353A37]/75 mt-0.5">Zonasi Kecamatan</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Antrean Laporan Terbaru & SPK Aktif -->
<div id="antrean-tiket" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#A7B5A0]/30 flex items-center justify-between">
            <h3 class="font-bold text-[#263B36] text-sm">📋 Tiket Laporan Aduan Terbaru</h3>
            <span class="text-xs text-[#353A37]/70">5 Laporan Terakhir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] uppercase">
                    <tr>
                        <th class="px-4 py-3">ID Tiket</th>
                        <th class="px-4 py-3">Deskripsi & Kategori</th>
                        <th class="px-4 py-3">Skor AI</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#A7B5A0]/30">
                    @forelse($recentReports as $report)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-4 py-3 font-bold text-[#263B36]">{{ $report->ticket_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37] line-clamp-1">{{ $report->description }}</p>
                            <p class="text-[#353A37]/70">{{ $report->district->name ?? '-' }} • {{ $report->category->category_name ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/30 text-[#263B36] font-semibold">
                                {{ $report->ai_confidence }}% ({{ $report->ai_validation ? 'Valid' : 'Review' }})
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[10px]
                                {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                                {{ $report->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-[#353A37]/60">Belum ada laporan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-[#A7B5A0]/30 flex items-center justify-between">
            <h3 class="font-bold text-[#263B36] text-sm">🛠️ Disposisi SPK Digital Aktif</h3>
            <span class="text-xs text-[#353A37]/70">Monitoring Tim Teknis</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#263B36] text-[#E9E5DA] uppercase">
                    <tr>
                        <th class="px-4 py-3">No. SPK</th>
                        <th class="px-4 py-3">Petugas Lapangan</th>
                        <th class="px-4 py-3">Target SLA</th>
                        <th class="px-4 py-3">Status SPK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#A7B5A0]/30">
                    @forelse($activeWorkOrders as $wo)
                    <tr class="hover:bg-[#E9E5DA]/60">
                        <td class="px-4 py-3 font-bold text-[#263B36]">{{ $wo->spk_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-[#353A37]">{{ $wo->officer->name ?? '-' }}</p>
                            <p class="text-[#353A37]/70">{{ $wo->officer->phone_number ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-[#353A37]">{{ $wo->target_sla?->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[10px] bg-[#A7B5A0]/40 text-[#263B36]">
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
