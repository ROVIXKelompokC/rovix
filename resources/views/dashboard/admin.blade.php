@extends('layouts.app')

@section('title', 'Dashboard Admin — ROVIX')
@section('header', 'Dashboard Admin & Pemantauan Peta GIS')
@section('subheader', 'Dinas Pekerjaan Umum • Pemantauan Titik Kerusakan Jalan, Validasi AI, & Penugasan SPK')

@section('content')
<!-- 4 Kartu Metrik Ringkas -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
    <div class="bg-[#F4F1EA] rounded-lg px-3.5 py-2.5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Total Laporan</span>
            <span class="w-2 h-2 rounded-full bg-[#263B36] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-xl font-bold font-mono-tech text-[#263B36]">{{ $stats['total_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech text-[#C76B45]">{{ $stats['submitted_reports'] }}</strong> menunggu</span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-3.5 py-2.5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>SPK Diproses</span>
            <span class="w-2 h-2 rounded-full bg-[#C76B45] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-xl font-bold font-mono-tech text-[#C76B45]">{{ $stats['in_progress_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech text-[#263B36]">{{ $stats['total_officers'] }}</strong> petugas</span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-3.5 py-2.5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Perbaikan Selesai</span>
            <span class="w-2 h-2 rounded-full bg-[#A7B5A0] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-xl font-bold font-mono-tech text-[#263B36]">{{ $stats['resolved_reports'] }}</p>
            <span class="text-[11px] text-[#353A37]/75">Rating: <strong class="font-mono-tech text-[#263B36]">{{ $stats['avg_rating'] }}/5.0</strong></span>
        </div>
    </div>

    <div class="bg-[#F4F1EA] rounded-lg px-3.5 py-2.5 border border-[#263B36]/15">
        <div class="flex items-center justify-between text-[10px] font-mono-tech uppercase text-[#353A37]/70">
            <span>Pengguna & Role</span>
            <span class="w-2 h-2 rounded-full bg-[#353A37] shrink-0"></span>
        </div>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-xl font-bold font-mono-tech text-[#353A37]">{{ $stats['total_users'] }}</p>
            <span class="text-[11px] text-[#353A37]/75"><strong class="font-mono-tech">{{ $stats['total_districts'] }}</strong> Wilayah</span>
        </div>
    </div>
</div>

<!-- 1 Baris Grid Utama: Kiri = Peta GIS + Beban Kerja Petugas, Kanan = Tabel Laporan & Tabel SPK -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
    <!-- Kolom Kiri (6 Kolom): Peta GIS & Pembagian Beban Kerja Petugas -->
    <div class="lg:col-span-6 flex flex-col gap-3">
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
            <div class="px-3.5 py-2 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Peta Sebaran Titik Kerusakan Jalan (Web GIS)</h3>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase bg-[#A7B5A0]/30 text-[#263B36] rounded border border-[#263B36]/20">
                    GPS AKTIF
                </span>
            </div>
            <div id="adminMap" class="h-48 sm:h-52 w-full z-0"></div>
        </div>

        <!-- Panel Pembagian Beban Kerja Petugas & Anti-Bentrok Jadwal -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
            <div class="px-3.5 py-2 border-b border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Distribusi Beban Kerja & Jadwal Petugas</h3>
                <span class="text-[10px] font-mono-tech text-[#263B36] bg-[#A7B5A0]/30 px-2 py-0.5 rounded font-semibold">ANTI-BENTROK JADWAL</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                        <tr>
                            <th class="px-3 py-1.5">Prioritas</th>
                            <th class="px-3 py-1.5">Nama Petugas & Wilayah</th>
                            <th class="px-3 py-1.5">Beban SPK</th>
                            <th class="px-3 py-1.5">Status Jadwal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#263B36]/10">
                        @forelse($rankedOfficers as $index => $officer)
                        <tr class="hover:bg-[#E9E5DA]/60">
                            <td class="px-3 py-2 font-mono-tech font-bold text-[#263B36]">
                                #{{ $index + 1 }}
                                @if($index === 0)
                                    <span class="ml-1 px-1.5 py-0.5 rounded bg-[#C76B45] text-white text-[9px] uppercase">Direkomendasikan</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <p class="font-semibold text-[#353A37]">{{ $officer->name }}</p>
                                <p class="text-[10px] text-[#353A37]/70">{{ $officer->district->name ?? 'Lintas Wilayah' }}</p>
                            </td>
                            <td class="px-3 py-2 font-mono-tech text-[11px]">
                                <span class="font-bold text-[#263B36]">{{ $officer->active_spk_count }}</span> Aktif •
                                <span class="text-[#353A37]/70">{{ $officer->completed_spk_count }} Selesai</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if($officer->has_schedule_conflict)
                                    <span class="px-2 py-0.5 rounded bg-[#C76B45]/20 text-[#C76B45] font-mono-tech font-semibold text-[10px]">
                                        Terjadwal ({{ $officer->conflict_spk_number }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-[#A7B5A0]/40 text-[#263B36] font-mono-tech font-semibold text-[10px]">
                                        Tersedia (Bebas Bentrok)
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-[#353A37]/60">Belum ada petugas terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan (6 Kolom): Tiket Laporan Terbaru & SPK Aktif -->
    <div class="lg:col-span-6 flex flex-col gap-3">
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
            <div class="px-3.5 py-2 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Tiket Laporan Kerusakan Terbaru</h3>
                <span class="text-[10px] font-mono-tech text-[#353A37]/70 shrink-0">5 TIKET TERAKHIR</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                        <tr>
                            <th class="px-3 py-1.5">Kode Tiket</th>
                            <th class="px-3 py-1.5">Deskripsi & Lokasi</th>
                            <th class="px-3 py-1.5">Skor AI</th>
                            <th class="px-3 py-1.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#263B36]/10">
                        @forelse($recentReports as $report)
                        <tr class="hover:bg-[#E9E5DA]/60">
                            <td class="px-3 py-2 font-mono-tech font-semibold text-[#263B36] whitespace-nowrap">{{ $report->ticket_code }}</td>
                            <td class="px-3 py-2">
                                <p class="font-semibold text-[#353A37] line-clamp-1">{{ $report->description }}</p>
                                <p class="text-[10px] text-[#353A37]/70">{{ $report->district->name ?? '-' }} • {{ $report->category->category_name ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-2 font-mono-tech whitespace-nowrap">
                                <span class="px-1.5 py-0.5 rounded bg-[#A7B5A0]/30 text-[#263B36] font-medium text-[10px]">
                                    {{ $report->ai_confidence }}%
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px]
                                    {{ $report->status === 'resolved' ? 'bg-[#A7B5A0]/40 text-[#263B36]' : ($report->status === 'in_progress' ? 'bg-[#263B36] text-white' : 'bg-[#C76B45]/20 text-[#C76B45]') }}">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-[#353A37]/60">Belum ada data laporan masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 overflow-hidden">
            <div class="px-3.5 py-2 border-b border-[#263B36]/15 flex items-center justify-between gap-2">
                <h3 class="font-bold text-[#263B36] text-xs uppercase tracking-wider">Surat Perintah Kerja (SPK) Aktif</h3>
                <span class="text-[10px] font-mono-tech text-[#353A37]/70 shrink-0">PENUGASAN LAPANGAN</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#263B36] text-[#E9E5DA] font-mono-tech text-[10px] uppercase">
                        <tr>
                            <th class="px-3 py-1.5">Nomor SPK</th>
                            <th class="px-3 py-1.5">Petugas Pelaksana</th>
                            <th class="px-3 py-1.5">Target SLA</th>
                            <th class="px-3 py-1.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#263B36]/10">
                        @forelse($activeWorkOrders as $wo)
                        <tr class="hover:bg-[#E9E5DA]/60">
                            <td class="px-3 py-2 font-mono-tech font-semibold text-[#263B36] whitespace-nowrap">{{ $wo->spk_number }}</td>
                            <td class="px-3 py-2">
                                <p class="font-semibold text-[#353A37]">{{ $wo->officer->name ?? '-' }}</p>
                                <p class="text-[10px] font-mono-tech text-[#353A37]/70">{{ $wo->officer->phone_number ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-2 font-mono-tech text-[#353A37] whitespace-nowrap">{{ $wo->target_sla?->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-mono-tech font-semibold uppercase text-[10px] bg-[#A7B5A0]/40 text-[#263B36]">
                                    {{ $wo->work_status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-[#353A37]/60">Belum ada SPK diterbitkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
