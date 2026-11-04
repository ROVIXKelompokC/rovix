@extends('layouts.app')

@section('title', 'Registrasi Warga — ROVIX')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#E9E5DA] p-6">
    <div class="max-w-md w-full bg-[#F5F2EB] rounded-2xl shadow-xl border border-[#A7B5A0]/50 p-8 text-[#353A37]">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-[#C76B45] text-white font-extrabold flex items-center justify-center shadow">RV</div>
            <div>
                <h1 class="font-bold text-lg text-[#263B36]">Registrasi Warga Pelapor</h1>
                <p class="text-xs text-[#353A37]/75">Buat akun untuk melapor & memantau perbaikan jalan</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-[#C76B45]/15 border border-[#C76B45]/40 text-[#263B36] text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <div>• {{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Nomor Telepon / WhatsApp *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Kata Sandi *</label>
                    <input type="password" name="password" required
                        class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Ulangi Sandi *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3.5 py-2 rounded-lg border border-[#A7B5A0] bg-white text-sm focus:ring-2 focus:ring-[#C76B45] outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#C76B45] hover:bg-[#b25c39] text-white font-bold rounded-lg text-sm shadow transition">
                Daftar sebagai Warga
            </button>
        </form>

        <p class="text-center text-xs text-[#353A37] mt-5">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-[#C76B45] font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
