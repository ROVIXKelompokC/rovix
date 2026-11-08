@extends('layouts.app')

@section('title', 'Terbitkan SPK Baru — ROVIX')
@section('header', 'Penerbitan SPK Digital Baru')
@section('subheader', 'Pilih laporan terverifikasi, tentukan petugas pelaksana, target SLA, dan catatan perbaikan')

@section('content')
<div class="w-full bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 sm:p-6">
    @if($errors->any())
        <div class="mb-6 p-4 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs space-y-1">
            <p class="font-bold">Periksa kembali isian formulir:</p>
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    @if($reports->isEmpty())
        <div class="p-6 text-center text-xs text-[#353A37]/75">
            <p class="font-semibold text-[#263B36] mb-1">Belum ada laporan yang siap dibuatkan SPK.</p>
            <p>Laporan harus berstatus <strong>VERIFIED</strong> dan belum memiliki SPK. Verifikasi laporan terlebih dahulu di menu Verifikasi Laporan.</p>
            <a href="{{ route('admin.verification.index') }}" class="inline-block mt-4 px-4 py-2 bg-[#263B36] text-white rounded text-xs font-semibold">Ke Verifikasi Laporan</a>
        </div>
    @else
    <form action="{{ route('admin.work-orders.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Nomor SPK</label>
                <input type="text" name="spk_number" value="{{ old('spk_number', $nextSpkNumber) }}" required maxlength="50"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm font-mono-tech focus:border-[#C76B45] outline-none">
                <p class="text-[11px] text-[#353A37]/60 mt-1">Terisi otomatis, boleh diubah selama unik.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Laporan Terverifikasi</label>
                <select name="report_id" id="report_id" required class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white focus:border-[#C76B45] outline-none">
                    @foreach($reports as $r)
                        <option value="{{ $r->id }}" data-severity="{{ $r->category->severity_level ?? 1 }}" {{ (string) old('report_id', $selectedReportId) === (string) $r->id ? 'selected' : '' }}>
                            {{ $r->ticket_code }} — {{ $r->district->name ?? '-' }} — {{ \Illuminate\Support\Str::limit($r->description, 60) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-4 rounded bg-[#E9E5DA] border border-[#263B36]/15 flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-xs font-semibold text-[#263B36]">Prioritas Penanganan</p>
                <p class="text-[11px] text-[#353A37]/60">Otomatis dari tingkat keparahan kategori kerusakan pada laporan yang dipilih.</p>
            </div>
            <span id="priority_badge" class="px-3 py-1 rounded text-[11px] font-mono-tech font-bold uppercase bg-[#263B36] text-white">-</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Petugas Pelaksana</label>
                <select name="officer_id" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white focus:border-[#C76B45] outline-none">
                    <option value="">-- Otomatis (beban kerja paling ringan) --</option>
                    @foreach($officers as $i => $o)
                        <option value="{{ $o->id }}" {{ (string) old('officer_id') === (string) $o->id ? 'selected' : '' }}>
                            #{{ $i + 1 }} {{ $o->name }} — {{ $o->district->name ?? 'Lintas Wilayah' }}
                            ({{ $o->active_spk_count }} aktif, {{ $o->completed_spk_count }} selesai{{ $o->has_schedule_conflict ? ', jadwal terisi' : '' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-[#353A37]/60 mt-1">Urutan #1 = rekomendasi sistem (jadwal kosong, beban terendah).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Target SLA (Batas Penyelesaian)</label>
                <input type="date" name="target_sla" value="{{ old('target_sla', $defaultSla) }}" min="{{ now()->toDateString() }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm font-mono-tech focus:border-[#C76B45] outline-none">
                <p class="text-[11px] text-[#353A37]/60 mt-1">Jika bentrok dengan jadwal petugas, tanggal otomatis digeser ke hari kosong terdekat.</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Catatan Perbaikan / Instruksi Kerja</label>
            <textarea name="repair_notes" rows="4" maxlength="1000"
                placeholder="Contoh: Lakukan penambalan cold-mix aspal, utamakan jam non-puncak."
                class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">{{ old('repair_notes') }}</textarea>
        </div>

        <div class="pt-4 border-t border-[#263B36]/10 flex flex-wrap items-center justify-end gap-2.5">
            <a href="{{ route('admin.work-orders.index') }}" class="px-4 py-2 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 rounded text-xs font-semibold text-center flex-1 sm:flex-none">Batal</a>
            <button type="submit" class="px-5 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition text-center flex-1 sm:flex-none">
                Terbitkan SPK
            </button>
        </div>
    </form>
    @endif
</div>

<script>
    (function () {
        var labels = {5: 'Kritis', 4: 'Tinggi', 3: 'Sedang', 2: 'Rendah', 1: 'Sangat Rendah'};
        var select = document.getElementById('report_id');
        var badge = document.getElementById('priority_badge');
        if (!select || !badge) return;
        function update() {
            var opt = select.options[select.selectedIndex];
            var sev = opt ? parseInt(opt.getAttribute('data-severity'), 10) : 0;
            badge.textContent = sev ? labels[sev] + ' (' + sev + '/5)' : '-';
        }
        select.addEventListener('change', update);
        update();
    })();
</script>
@endsection
