@extends('layouts.app')

@section('title', 'Ubah Pengguna & RBAC — ROVIX')
@section('header', 'Pembaruan Data Akun & Otorisasi Peran')
@section('subheader', 'Perbarui profil pengguna, hak akses sistem, dan penugasan wilayah kecamatan')

@section('content')
<div class="max-w-3xl bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-5 sm:p-6">
    @if($errors->any())
        <div class="mb-6 p-4 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs space-y-1">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Nomor Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm font-mono-tech focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Otorisasi Peran (RBAC)</label>
                <select name="role" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white focus:border-[#C76B45] outline-none">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin Dinas PU</option>
                    <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                    <option value="warga" {{ old('role', $user->role) === 'warga' ? 'selected' : '' }}>Warga Pelapor</option>
                </select>
            </div>
        </div>

        <div class="p-4 rounded bg-[#E9E5DA] border border-[#263B36]/15">
            <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Wilayah Kecamatan</label>
            <select name="district_id" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 text-[#353A37] text-sm bg-white">
                <option value="">-- Lintas Wilayah / Tanpa Spesifikasi Kecamatan --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) old('district_id', $user->district_id) === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->city_code }} — {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Kata Sandi Baru (Opsional)</label>
                <input type="password" name="password" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-[#263B36]/10 flex flex-wrap items-center justify-end gap-2.5">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-[#E9E5DA] hover:bg-[#A7B5A0]/40 text-[#263B36] border border-[#263B36]/15 rounded text-xs font-semibold text-center flex-1 sm:flex-none">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs transition text-center flex-1 sm:flex-none">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
