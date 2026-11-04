<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ROVIX — Road Vigilance & Index')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: '#263B36',
                        ivory: '#E9E5DA',
                        surface: '#F4F1EA',
                        terracotta: '#C76B45',
                        sage: '#A7B5A0',
                        charcoal: '#353A37',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #E9E5DA;
            color: #353A37;
            -webkit-font-smoothing: antialiased;
        }
        .font-mono-tech {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body class="bg-[#E9E5DA] text-[#353A37] min-h-screen flex flex-col selection:bg-[#C76B45] selection:text-white">

    @auth
    <!-- Top Bar Khusus Layar Mobile / Tablet (< lg) Sesuai TR-06 Mobile-First -->
    <div class="lg:hidden sticky top-0 z-40 bg-[#263B36] text-[#E9E5DA] px-4 py-3 border-b border-[#A7B5A0]/20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button" id="mobileMenuBtn" aria-label="Buka Navigasi"
                class="p-2 -ml-1 rounded text-[#E9E5DA] hover:bg-white/10 focus:outline-none">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-[#C76B45] flex items-center justify-center text-white shrink-0">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-tight text-white">ROVIX</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->role === 'admin')
                <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase rounded bg-[#C76B45] text-white">ADMIN</span>
            @elseif(auth()->user()->role === 'petugas')
                <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase rounded bg-[#A7B5A0]/25 text-[#E9E5DA] border border-[#A7B5A0]/40">PETUGAS</span>
            @else
                <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase rounded bg-[#A7B5A0]/15 text-[#A7B5A0] border border-[#A7B5A0]/30">WARGA</span>
            @endif
        </div>
    </div>

    <!-- Backdrop Overlay Drawer Mobile -->
    <div id="mobileMenuBackdrop" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <div class="flex min-h-screen relative">
        <!-- Sidebar Navigasi Utama (Off-Canvas Drawer di Mobile, Static Sidebar di Desktop) -->
        <aside id="sidebarDrawer"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-[#263B36] text-[#E9E5DA] flex flex-col shrink-0 border-r border-[#1a2925] transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-200 ease-in-out">
            <div class="px-6 py-5 border-b border-[#A7B5A0]/15 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded bg-[#C76B45] flex items-center justify-center text-white shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19L9 5"/><path d="M15 5L20 19"/><path d="M12 6V8"/><path d="M12 11V13"/><path d="M12 16V18"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-base tracking-tight leading-none text-white">ROVIX</h1>
                        <p class="text-[11px] font-mono-tech text-[#A7B5A0] mt-1 tracking-wide">ROAD VIGILANCE INDEX</p>
                    </div>
                </div>
                <button type="button" id="closeMobileMenuBtn" aria-label="Tutup Navigasi"
                    class="lg:hidden p-1.5 rounded text-[#A7B5A0] hover:text-white hover:bg-white/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Bar Status Otorisasi Peran -->
            <div class="px-6 py-2.5 bg-[#1e302b] border-b border-[#A7B5A0]/15 flex items-center justify-between">
                <span class="text-[11px] font-mono-tech uppercase tracking-wider text-[#A7B5A0]/80">Otorisasi</span>
                @if(auth()->user()->role === 'admin')
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase tracking-wider rounded bg-[#C76B45] text-white">ADMIN DINAS</span>
                @elseif(auth()->user()->role === 'petugas')
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase tracking-wider rounded bg-[#A7B5A0]/25 text-[#E9E5DA] border border-[#A7B5A0]/40">PETUGAS PU</span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-mono-tech font-semibold uppercase tracking-wider rounded bg-[#A7B5A0]/15 text-[#A7B5A0] border border-[#A7B5A0]/30">WARGA</span>
                @endif
            </div>

            <nav class="flex-1 px-3 py-5 space-y-1 text-[13px] overflow-y-auto">
                @if(auth()->user()->role === 'admin')
                    <div class="text-[10px] font-mono-tech font-semibold text-[#A7B5A0]/70 uppercase tracking-widest px-3 pb-2">Operasional & GIS</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>Ikhtisar & Peta GIS</span>
                    </a>
                    <a href="{{ route('admin.verifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.verifications.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                        <span>Verifikasi Laporan</span>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.work-orders.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span>Disposisi SPK Digital</span>
                    </a>
                    <a href="{{ route('admin.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.ratings.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Evaluasi & Mutu Layanan</span>
                    </a>

                    <div class="text-[10px] font-mono-tech font-semibold text-[#A7B5A0]/70 uppercase tracking-widest px-3 pt-5 pb-2">Data Induk & Otorisasi</div>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        <span>Kategori Kerusakan</span>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.districts.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Wilayah Kecamatan</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.users.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Pengguna & RBAC</span>
                    </a>
                @elseif(auth()->user()->role === 'petugas')
                    <div class="text-[10px] font-mono-tech font-semibold text-[#A7B5A0]/70 uppercase tracking-widest px-3 pb-2">Operasional Lapangan</div>
                    <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('petugas.dashboard') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Daftar Tugas SPK</span>
                    </a>
                    <a href="{{ route('petugas.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('petugas.work-orders.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <span>Pembaruan Bukti Fisik</span>
                    </a>
                @else
                    <div class="text-[10px] font-mono-tech font-semibold text-[#A7B5A0]/70 uppercase tracking-widest px-3 pb-2">Layanan Warga</div>
                    <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('warga.dashboard') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Riwayat Laporan Saya</span>
                    </a>
                    <a href="{{ route('warga.reports.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('warga.reports.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Buat Laporan Baru</span>
                    </a>
                    <a href="{{ route('warga.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('warga.ratings.*') ? 'bg-[#C76B45] text-white font-semibold' : 'text-[#E9E5DA]/80 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0 opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <span>Penilaian Hasil Perbaikan</span>
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-[#A7B5A0]/15 bg-[#1e302b]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded bg-[#263B36] border border-[#A7B5A0]/30 flex items-center justify-center font-mono-tech font-semibold text-xs text-[#E9E5DA] shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold truncate text-white">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] font-mono-tech text-[#A7B5A0] truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 bg-transparent hover:bg-[#C76B45] text-[#E9E5DA]/80 hover:text-white border border-[#A7B5A0]/25 hover:border-[#C76B45] rounded text-xs font-medium transition">
                        Keluar Sesi
                    </button>
                </form>
            </div>
        </aside>

        <!-- Area Konten Utama Responsif (Mobile-First TR-06) -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#E9E5DA]">
            <header class="bg-[#F4F1EA] border-b border-[#263B36]/15 px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#263B36] tracking-tight">@yield('header', 'Dasbor Sistem ROVIX')</h2>
                    <p class="text-xs text-[#353A37]/70 mt-0.5">@yield('subheader', 'Sistem Pemantauan & Tindak Lanjut Infrastruktur Jalan')</p>
                </div>
                <div class="flex items-center gap-3 self-start sm:self-auto">
                    @if(auth()->user()->district)
                        <span class="px-2.5 py-1 bg-[#E9E5DA] text-[#263B36] border border-[#263B36]/20 rounded text-[11px] sm:text-xs font-medium">
                            Wilayah: {{ auth()->user()->district->name }}
                        </span>
                    @endif
                    <span class="text-xs font-mono-tech text-[#353A37]/70">{{ now()->format('d/m/Y') }}</span>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
                @if(session('success'))
                    <div class="mb-6 px-4 py-3 rounded bg-[#A7B5A0]/25 border-l-2 border-[#263B36] text-[#263B36] text-xs font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 px-4 py-3 rounded bg-[#C76B45]/15 border-l-2 border-[#C76B45] text-[#263B36] text-xs font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bottom Navigation Bar Khusus Mobile (< lg) untuk Kemudahan Operasional Lapangan (TR-06) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-[#263B36] text-[#E9E5DA] border-t border-[#A7B5A0]/20 grid grid-cols-3 text-[11px]">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.dashboard') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dasbor GIS</span>
            </a>
            <a href="{{ route('admin.verifications.index') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.verifications.*') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/></svg>
                <span>Verifikasi</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.users.*') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span>Akun RBAC</span>
            </a>
        @elseif(auth()->user()->role === 'petugas')
            <a href="{{ route('petugas.dashboard') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('petugas.dashboard') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                <span>Tugas SPK</span>
            </a>
            <a href="{{ route('petugas.work-orders.index') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('petugas.work-orders.*') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <span>Unggah Foto</span>
            </a>
            <button type="button" onclick="document.getElementById('mobileMenuBtn').click()" class="py-2.5 flex flex-col items-center justify-center gap-1 text-[#E9E5DA]/75">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <span>Menu</span>
            </button>
        @else
            <a href="{{ route('warga.dashboard') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('warga.dashboard') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('warga.reports.create') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('warga.reports.*') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Lapor Jalan</span>
            </a>
            <a href="{{ route('warga.ratings.index') }}" class="py-2.5 flex flex-col items-center justify-center gap-1 {{ request()->routeIs('warga.ratings.*') ? 'text-[#C76B45] font-semibold' : 'text-[#E9E5DA]/75' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Penilaian</span>
            </a>
        @endif
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('mobileMenuBtn');
            const closeBtn = document.getElementById('closeMobileMenuBtn');
            const drawer = document.getElementById('sidebarDrawer');
            const backdrop = document.getElementById('mobileMenuBackdrop');

            function toggleDrawer(show) {
                if (!drawer || !backdrop) return;
                if (show) {
                    drawer.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    drawer.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            }

            if (openBtn) openBtn.addEventListener('click', () => toggleDrawer(true));
            if (closeBtn) closeBtn.addEventListener('click', () => toggleDrawer(false));
            if (backdrop) backdrop.addEventListener('click', () => toggleDrawer(false));
        });
    </script>
    @else
        @yield('content')
    @endauth

</body>
</html>
