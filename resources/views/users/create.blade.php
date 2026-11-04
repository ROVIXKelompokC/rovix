@extends('layouts.app')

@section('title', 'Tambah Pengguna & RBAC — ROVIX')
@section('header', 'Registrasi Akun Pengguna & Hak Akses (RBAC)')
@section('subheader', 'Pendaftaran akun Administrator Dinas, Petugas Teknis Lapangan, atau Warga Pelapor')

@section('content')
<div class="max-w-3xl bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-8">
    @if($errors->any())
        <div class="mb-6 p-4 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs space-y-1">
            <p class="font-bold">Periksa kembali isian formulir:</p>
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Nama Lengkap Pengguna</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Nama lengkap sesuai identitas"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@rovix.id"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                    placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm font-mono-tech focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Otorisasi Peran (Role RBAC)</label>
                <select name="role" required class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white focus:border-[#C76B45] outline-none">
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas Lapangan (Pelaksana SPK)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin Dinas PU (Pengelola Utama)</option>
                    <option value="warga" {{ old('role') === 'warga' ? 'selected' : '' }}>Warga Pelapor (Akses Publik)</option>
                </select>
            </div>
        </div>

        <div class="p-4 rounded bg-[#E9E5DA] border border-[#263B36]/15">
            <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Penugasan Wilayah Kecamatan</label>
            <select name="district_id" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white">
                <option value="">-- Lintas Wilayah / Tanpa Spesifikasi Kecamatan --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) old('district_id') === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->city_code }} — {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Kata Sandi</label>
                <input type="password" name="password" required
                    placeholder="Minimal 6 karakter"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Ulangi kata sandi"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-[#263B36]/10 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 rounded text-xs font-semibold">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition">
                Simpan Akun Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
