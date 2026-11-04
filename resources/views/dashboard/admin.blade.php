@extends('layouts.app')

@section('title', 'Dasbor Admin Dinas PU — ROVIX')
@section('header', 'Management Dashboard — Dinas Pekerjaan Umum')
@section('subheader', 'Pemantauan Sebaran Titik Kerusakan Jalan, Triage AI, Disposisi SPK, & Master Data RBAC')

@section('content')
<!-- 4 Kartu Ringkasan Metrik Operasional (Sesuai Gambar 4.1 Proposal) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Total Laporan Warga</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total_reports'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['submitted_reports'] }} menunggu verifikasi</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl">📸</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Sedang Diproses (SPK)</p>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $stats['in_progress_reports'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['total_officers'] }} petugas teknis aktif</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-2xl">🛠️</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Perbaikan Selesai</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['resolved_reports'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Rata-rata Rating: ⭐ {{ $stats['avg_rating'] }}/5.0</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl">✅</div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase">Pengguna & Zona RBAC</p>
            <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['total_districts'] }} Zona • {{ $stats['total_categories'] }} Kategori</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-2xl">👥</div>
    </div>
</div>

<!-- Peta Interaktif Sebaran Kerusakan Jalan (Leaflet.js) & Shortcut RBAC -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">🗺️ Peta Sebaran Titik Aduan Kerusakan Jalan (Web GIS)</h3>
                <p class="text-xs text-slate-500">Visualisasi Leaflet.js OpenStreetMap — Klaster Titik Aduan Aktif</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200">
                Live GPS Sync
            </span>
        </div>
        <div id="adminMap" class="h-80 w-full z-0"></div>
    </div>

    <!-- Panel Aksi Cepat Manajemen RBAC & Master Data -->
    <div id="master-data" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-slate-800 text-sm mb-1">🛡️ Pusat Kontrol RBAC & Modul Tim</h3>
            <p class="text-xs text-slate-500 mb-4">Kelola hak akses pengguna, regu lapangan, dan modul CRUD anggota tim</p>

            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="block p-3.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs">👥 Daftar Pengguna & Hak Akses (RBAC)</span>
                        <span class="text-xs text-amber-400 font-bold">Buka →</span>
                    </div>
                    <p class="text-[11px] text-slate-300 mt-1">SCRUM-110 • Lihat & filter akun Warga, Petugas, Admin</p>
                </a>

                <a href="{{ route('admin.users.create') }}" class="block p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 transition">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs text-slate-900">➕ Tambah Akun Pengguna / Petugas Baru</span>
                        <span class="text-xs text-amber-700 font-bold">Tambah →</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-1">SCRUM-106 • Registrasi akun baru & penugasan zona wilayah</p>
                </a>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <p class="font-semibold text-slate-700">Status Integrasi Modul Kelompok C:</p>
                    <p class="text-slate-500">• <strong>Users & RBAC (Angel):</strong> <span class="text-emerald-600 font-semibold">Aktif (rovix_db)</span></p>
                    <p class="text-slate-500">• <strong>Laporan Warga (Dodo):</strong> Siap diintegrasikan</p>
                    <p class="text-slate-500">• <strong>Verifikasi & GIS (Hafis):</strong> Siap diintegrasikan</p>
                    <p class="text-slate-500">• <strong>Master Kategori (Tipa):</strong> Siap diintegrasikan</p>
                    <p class="text-slate-500">• <strong>Master Wilayah (Guntur):</strong> Siap diintegrasikan</p>
                    <p class="text-slate-500">• <strong>SPK Lapangan (Ersa):</strong> Siap diintegrasikan</p>
                    <p class="text-slate-500">• <strong>Rating Warga (Naswa):</strong> Siap diintegrasikan</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Antrean Laporan Terbaru & SPK Aktif -->
<div id="antrean-tiket" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">📋 Tiket Laporan Aduan Terbaru</h3>
            <span class="text-xs text-slate-400">5 Laporan Terakhir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">ID Tiket</th>
                        <th class="px-4 py-3">Deskripsi & Kategori</th>
                        <th class="px-4 py-3">Skor AI</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentReports as $report)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-bold text-slate-800">{{ $report->ticket_code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800 line-clamp-1">{{ $report->description }}</p>
                            <p class="text-slate-400">{{ $report->district->name ?? '-' }} • {{ $report->category->category_name ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">
                                {{ $report->ai_confidence }}% ({{ $report->ai_validation ? 'Valid' : 'Review' }})
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[10px]
                                {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($report->status === 'in_progress' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $report->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada laporan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">🛠️ Disposisi SPK Digital Aktif</h3>
            <span class="text-xs text-slate-400">Monitoring Tim Teknis</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">No. SPK</th>
                        <th class="px-4 py-3">Petugas Lapangan</th>
                        <th class="px-4 py-3">Target SLA</th>
                        <th class="px-4 py-3">Status SPK</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activeWorkOrders as $wo)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-bold text-slate-800">{{ $wo->spk_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $wo->officer->name ?? '-' }}</p>
                            <p class="text-slate-400">{{ $wo->officer->phone_number ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $wo->target_sla?->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full font-semibold uppercase text-[10px] bg-indigo-100 text-indigo-700">
                                {{ $wo->work_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada SPK diterbitkan.</td>
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
