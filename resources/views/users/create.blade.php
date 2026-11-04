@extends('layouts.app')

@section('title', 'Tambah Pengguna & Atur Peran RBAC — ROVIX')
@section('header', 'Tambah Akun Pengguna Baru & Pengaturan Hak Akses (RBAC)')
@section('subheader', 'SCRUM-106 • Buat akun untuk Admin Dinas, Petugas Teknis Lapangan, atau Warga Pelapor')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <p class="font-bold">Terdapat kesalahan input:</p>
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Pengguna *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email Resmi (Unik) *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="budi@rovix.id"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                    placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Peran Hak Akses (Role RBAC) *</label>
                <select name="role" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-amber-500 outline-none">
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>🛠️ Petugas Lapangan (Field View & SPK)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>🛡️ Admin Dinas PU (Full Management Dashboard)</option>
                    <option value="warga" {{ old('role') === 'warga' ? 'selected' : '' }}>🏠 Warga Pelapor (Public Citizen Portal)</option>
                </select>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Zona Wilayah Tugas (Kecamatan)</label>
            <select name="district_id" class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                <option value="">-- Pilih Zona Wilayah --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) old('district_id') === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->city_code }} — {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Akun *</label>
                <input type="password" name="password" required
                    placeholder="Minimal 6 karakter"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi *</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Ulangi kata sandi"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs shadow">
                💾 Simpan Akun Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
