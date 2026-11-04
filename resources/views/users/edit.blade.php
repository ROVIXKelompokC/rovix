@extends('layouts.app')

@section('title', 'Edit Pengguna & RBAC — ROVIX')
@section('header', 'Ubah Data Akun Pengguna & Hak Akses (RBAC)')
@section('subheader', 'Perbarui informasi profil, peran pengguna, dan zona wilayah tugas')

@section('content')
<div class="max-w-3xl bg-[#F5F2EB] rounded-2xl border border-[#A7B5A0]/50 shadow-sm p-8">
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-[#C76B45]/15 border border-[#C76B45]/40 text-[#263B36] text-xs space-y-1">
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
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Nomor Telepon *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Peran Hak Akses (RBAC) *</label>
                <select name="role" class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] text-[#353A37] text-sm bg-white focus:ring-2 focus:ring-[#C76B45] outline-none">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin Dinas PU</option>
                    <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas Lapangan</option>
                    <option value="warga" {{ old('role', $user->role) === 'warga' ? 'selected' : '' }}>Warga Pelapor</option>
                </select>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-[#E9E5DA] border border-[#A7B5A0]/50">
            <label class="block text-xs font-bold text-[#263B36] mb-1.5">Zona Wilayah Tugas</label>
            <select name="district_id" class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] text-[#353A37] text-sm bg-white">
                <option value="">-- Tanpa Zona Khusus --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) old('district_id', $user->district_id) === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->city_code }} — {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#263B36] mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-[#A7B5A0]/30 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-[#A7B5A0]/30 hover:bg-[#A7B5A0]/50 text-[#263B36] rounded-lg text-xs font-semibold">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-[#C76B45] hover:bg-[#b25c39] text-white font-bold rounded-lg text-xs shadow transition">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
