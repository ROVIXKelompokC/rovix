@extends('layouts.app')

@section('title', 'Registrasi Warga — ROVIX')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#E9E5DA] p-6">
    <div class="max-w-md w-full bg-[#F4F1EA] rounded-lg border border-[#263B36]/20 p-8 text-[#353A37]">
        <div class="flex items-center gap-3 pb-5 mb-6 border-b border-[#263B36]/10">
            <div class="w-8 h-8 rounded bg-[#C76B45] text-white flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-base text-[#263B36] leading-none">Registrasi Pelapor Publik</h1>
                <p class="text-xs text-[#353A37]/70 mt-1">Pendaftaran akun warga untuk pelaporan jalan rusak</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-sm focus:border-[#C76B45] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#263B36] mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-sm font-mono-tech focus:border-[#C76B45] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Kata Sandi</label>
                    <input type="password" name="password" required
                        class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-sm focus:border-[#C76B45] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Konfirmasi Sandi</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-sm focus:border-[#C76B45] outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs uppercase tracking-wider transition">
                Daftarkan Akun
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-[#263B36]/10 flex items-center justify-between text-xs text-[#353A37]/80">
            <span>Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="text-[#C76B45] font-semibold hover:underline">Masuk ke Sistem</a>
        </div>
    </div>
</div>
@endsection
