@extends('layouts.app')

@section('title', 'Masuk — ROVIX Road Vigilance & Index')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6">
    <div class="max-w-4xl w-full grid md:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden">

        <!-- Kolom Kiri: Info Sistem & Akun Demo RBAC -->
        <div class="bg-slate-900 text-white p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-xl">
                        RV
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">ROVIX</h1>
                        <p class="text-xs text-amber-400 font-medium">Road Vigilance & Index • SDG 11</p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold leading-snug mb-3">
                    Sistem Informasi Pelaporan & Tindak Lanjut Kerusakan Jalan
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed mb-6">
                    Platform web monolitik terpadu dengan pendekatan <strong>Role-Based Access Control (RBAC)</strong>, penguncian koordinat GPS otomatis, serta validasi citra berbasis AI.
                </p>

                <div class="bg-slate-800/90 border border-slate-700 rounded-xl p-4 space-y-2.5 text-xs">
                    <p class="font-semibold text-amber-400 uppercase tracking-wider">🔑 Akun Uji Coba Multi-Role (RBAC):</p>
                    <div class="flex justify-between items-center border-b border-slate-700 pb-1.5">
                        <span><strong>Admin Dinas PU:</strong> <code class="text-indigo-300">admin@rovix.id</code></span>
                        <span class="text-slate-400">pass: <code>password</code></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-700 pb-1.5">
                        <span><strong>Petugas Lapangan:</strong> <code class="text-amber-300">petugas@rovix.id</code></span>
                        <span class="text-slate-400">pass: <code>password</code></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span><strong>Warga Pelapor:</strong> <code class="text-emerald-300">warga@rovix.id</code></span>
                        <span class="text-slate-400">pass: <code>password</code></span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-500 mt-6">
                Proyek Perangkat Lunak (PPL) • Kelompok C • Sistem Informasi Telkom University Jakarta
            </p>
        </div>

        <!-- Kolom Kanan: Form Login -->
        <div class="p-8 flex flex-col justify-center">
            <h3 class="text-2xl font-bold text-slate-800 mb-1">Masuk ke Akun Anda</h3>
            <p class="text-xs text-slate-500 mb-6">Sistem akan mengarahkan ke dasbor sesuai peran (Role) Anda</p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@rovix.id') }}" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none"
                        placeholder="nama@email.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" value="password" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300">
                        Ingat sesi saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm shadow transition">
                    Masuk Sekarang →
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-600">
                Warga belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-amber-600 hover:underline">Daftar Akun Warga</a>
            </div>
        </div>

    </div>
</div>
@endsection
