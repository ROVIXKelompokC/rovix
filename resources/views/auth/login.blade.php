@extends('layouts.app')

@section('title', 'Masuk — ROVIX Road Vigilance & Index')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#E9E5DA] p-6">
    <div class="max-w-4xl w-full grid md:grid-cols-2 bg-[#F5F2EB] rounded-2xl shadow-2xl border border-[#A7B5A0]/50 overflow-hidden">

        <!-- Kolom Kiri: Identitas Utama (Forest Green #263B36) -->
        <div class="bg-[#263B36] text-[#E9E5DA] p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-[#C76B45] text-white font-extrabold flex items-center justify-center text-xl shadow">
                        RV
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-white">ROVIX</h1>
                        <p class="text-xs text-[#A7B5A0] font-medium">Road Vigilance & Index • SDG 11</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold leading-snug mb-3 text-white">
                    Sistem Informasi Pelaporan & Tindak Lanjut Kerusakan Jalan
                </h2>
                <p class="text-xs text-[#E9E5DA]/85 leading-relaxed mb-6">
                    Platform web monolitik terpadu dengan pendekatan <strong>Role-Based Access Control (RBAC)</strong>, penguncian koordinat GPS otomatis, serta validasi citra berbasis AI Vision.
                </p>

                <div class="bg-[#1d2e2a] border border-[#A7B5A0]/30 rounded-xl p-4 space-y-2.5 text-xs">
                    <p class="font-semibold text-[#C76B45] uppercase tracking-wider">🔑 Akun Uji Coba Multi-Role (RBAC):</p>
                    <div class="flex justify-between items-center border-b border-[#A7B5A0]/20 pb-1.5">
                        <span><strong>Admin Dinas PU:</strong> <code class="text-[#A7B5A0]">admin@rovix.id</code></span>
                        <span class="text-[#E9E5DA]/70">pass: <code>password</code></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-[#A7B5A0]/20 pb-1.5">
                        <span><strong>Petugas Lapangan:</strong> <code class="text-[#A7B5A0]">petugas@rovix.id</code></span>
                        <span class="text-[#E9E5DA]/70">pass: <code>password</code></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span><strong>Warga Pelapor:</strong> <code class="text-[#A7B5A0]">warga@rovix.id</code></span>
                        <span class="text-[#E9E5DA]/70">pass: <code>password</code></span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-[#A7B5A0] mt-6">
                ROVIX — Citizen-Led Road Monitoring & Infrastructure Vigilance
            </p>
        </div>

        <!-- Kolom Kanan: Form Login -->
        <div class="p-8 flex flex-col justify-center text-[#353A37]">
            <h3 class="text-2xl font-bold text-[#263B36] mb-1">Masuk ke Akun Anda</h3>
            <p class="text-xs text-[#353A37]/75 mb-6">Sistem akan mengarahkan ke dasbor sesuai peran (Role) Anda</p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-[#C76B45]/15 border border-[#C76B45]/40 text-[#263B36] text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-[#A7B5A0]/30 border border-[#263B36]/30 text-[#263B36] text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@rovix.id') }}" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] focus:border-[#C76B45] outline-none"
                        placeholder="nama@email.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1">Kata Sandi</label>
                    <input type="password" name="password" value="password" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-[#A7B5A0] bg-white text-[#353A37] text-sm focus:ring-2 focus:ring-[#C76B45] focus:border-[#C76B45] outline-none"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-[#353A37]">
                        <input type="checkbox" name="remember" class="rounded border-[#A7B5A0]">
                        Ingat sesi saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#C76B45] hover:bg-[#b25c39] text-white font-bold rounded-lg text-sm shadow transition">
                    Masuk Sekarang →
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-[#A7B5A0]/40 text-center text-xs text-[#353A37]">
                Warga belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-[#C76B45] hover:underline">Daftar Akun Warga</a>
            </div>
        </div>

    </div>
</div>
@endsection
