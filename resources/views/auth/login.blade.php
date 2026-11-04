@extends('layouts.app')

@section('title', 'Masuk — ROVIX Road Vigilance & Index')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#E9E5DA] p-4 sm:p-6">
    <div class="max-w-4xl w-full grid md:grid-cols-12 bg-[#F4F1EA] rounded-lg border border-[#263B36]/20 overflow-hidden shadow-sm">

        <!-- Kolom Kiri: Identitas Sistem (Forest Green #263B36) -->
        <div class="md:col-span-6 bg-[#263B36] text-[#E9E5DA] p-6 sm:p-9 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 rounded bg-[#C76B45] text-white flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base font-bold tracking-tight text-white leading-none">ROVIX</h1>
                        <p class="text-[11px] text-[#A7B5A0] font-mono-tech mt-1">ROAD VIGILANCE & INDEX</p>
                    </div>
                </div>

                <h2 class="text-xl font-bold leading-snug mb-3 text-white tracking-tight">
                    Sistem Informasi Pemantauan & Tindak Lanjut Kerusakan Jalan
                </h2>
                <p class="text-xs text-[#E9E5DA]/80 leading-relaxed mb-8">
                    Platform pengelolaan laporan infrastruktur jalan berbasis geospasial, penguncian koordinat GPS otomatis, serta disposisi Surat Perintah Kerja (SPK) terintegrasi.
                </p>

                <div class="border-t border-[#A7B5A0]/20 pt-5">
                    <p class="font-mono-tech text-[10px] text-[#A7B5A0] uppercase tracking-widest mb-3">Kredensial Akses Lingkungan Uji (RBAC)</p>
                    <div class="space-y-2 text-xs font-mono-tech">
                        <div class="flex justify-between items-center py-1.5 border-b border-[#A7B5A0]/10">
                            <span class="text-[#E9E5DA]/70">ADMIN DINAS</span>
                            <span class="text-white">admin@rovix.id / password</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-[#A7B5A0]/10">
                            <span class="text-[#E9E5DA]/70">PETUGAS PU</span>
                            <span class="text-white">petugas@rovix.id / password</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-[#E9E5DA]/70">WARGA</span>
                            <span class="text-white">warga@rovix.id / password</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-[#A7B5A0]/15 flex items-center justify-between text-[11px] font-mono-tech text-[#A7B5A0]/80">
                <span>DINAS PEKERJAAN UMUM</span>
                <span>SDG 11.2</span>
            </div>
        </div>

        <!-- Kolom Kanan: Form Otentikasi -->
        <div class="md:col-span-6 p-6 sm:p-9 flex flex-col justify-center text-[#353A37]">
            <div class="mb-6">
                <p class="font-mono-tech text-[10px] uppercase tracking-widest text-[#C76B45] font-semibold mb-1">Otentikasi Pengguna</p>
                <h3 class="text-xl font-bold text-[#263B36] tracking-tight">Masuk ke Portal Sistem</h3>
                <p class="text-xs text-[#353A37]/70 mt-1">Gunakan akun sesuai wewenang peran operasional Anda.</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-[#A7B5A0]/30 border-l-2 border-[#263B36] text-[#263B36] text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@rovix.id') }}" required
                        class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none transition"
                        placeholder="nama@instansi.go.id">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#263B36] mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" value="password" required
                        class="w-full px-3.5 py-2 rounded border border-[#263B36]/25 bg-white text-[#353A37] text-sm focus:border-[#C76B45] outline-none transition"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-[#353A37]/80 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-[#263B36]/30">
                        <span>Simpan sesi perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#C76B45] hover:bg-[#b25c39] text-white font-semibold rounded text-xs uppercase tracking-wider transition">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-[#263B36]/10 text-xs text-[#353A37]/80 flex items-center justify-between">
                <span>Belum terdaftar sebagai pelapor?</span>
                <a href="{{ route('register') }}" class="font-semibold text-[#C76B45] hover:underline">Registrasi Akun Warga</a>
            </div>
        </div>

    </div>
</div>
@endsection
