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
                        terracotta: '#C76B45',
                        sage: '#A7B5A0',
                        charcoal: '#353A37',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #E9E5DA;
            color: #353A37;
        }
    </style>
</head>
<body class="bg-[#E9E5DA] text-[#353A37] min-h-screen flex flex-col">

    @auth
    <div class="flex min-h-screen">
        <!-- Sidebar Navigasi Berdasarkan Role RBAC (Forest Green #263B36) -->
        <aside class="w-64 bg-[#263B36] text-[#E9E5DA] flex flex-col shrink-0 shadow-xl">
            <div class="p-5 border-b border-[#A7B5A0]/20 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#C76B45] flex items-center justify-center font-extrabold text-white text-lg shadow">
                    RV
                </div>
                <div>
                    <h1 class="font-bold text-lg tracking-tight leading-none text-white">ROVIX</h1>
                    <p class="text-xs text-[#A7B5A0] mt-0.5">Road Vigilance & Index</p>
                </div>
            </div>

            <!-- Badge Role Pengguna Aktif -->
            <div class="px-5 py-3 bg-[#1d2e2a] border-b border-[#A7B5A0]/20 flex items-center justify-between">
                <span class="text-xs text-[#A7B5A0]">Hak Akses (RBAC):</span>
                @if(auth()->user()->role === 'admin')
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#C76B45]/25 text-[#E9E5DA] border border-[#C76B45]/50">ADMIN DINAS</span>
                @elseif(auth()->user()->role === 'petugas')
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#A7B5A0]/25 text-[#E9E5DA] border border-[#A7B5A0]/50">PETUGAS PU</span>
                @else
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#A7B5A0]/20 text-[#A7B5A0] border border-[#A7B5A0]/40">WARGA</span>
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-1.5 text-sm overflow-y-auto">
                @if(auth()->user()->role === 'admin')
                    <div class="text-xs font-semibold text-[#A7B5A0] uppercase tracking-wider px-3 py-1">Operasional & GIS</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>📊</span> <span>Dasbor Peta & Metrik</span>
                    </a>
                    <a href="{{ route('admin.verifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.verifications.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>🗺️</span> <span>Verifikasi & Peta GIS</span>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.work-orders.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>🛠️</span> <span>Disposisi SPK Digital</span>
                    </a>
                    <a href="{{ route('admin.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.ratings.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>⭐</span> <span>Evaluasi & Rating Warga</span>
                    </a>

                    <div class="text-xs font-semibold text-[#A7B5A0] uppercase tracking-wider px-3 pt-4 pb-1">Master Data & RBAC</div>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>🏷️</span> <span>Master Kategori</span>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.districts.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>📍</span> <span>Master Wilayah</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>👥</span> <span>Pengguna & RBAC</span>
                    </a>
                @elseif(auth()->user()->role === 'petugas')
                    <div class="text-xs font-semibold text-[#A7B5A0] uppercase tracking-wider px-3 py-1">Field View Petugas</div>
                    <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('petugas.dashboard') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>🛠️</span> <span>Daftar Tugas SPK Aktif</span>
                    </a>
                    <a href="{{ route('petugas.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('petugas.work-orders.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>📷</span> <span>Update Progres & Foto</span>
                    </a>
                @else
                    <div class="text-xs font-semibold text-[#A7B5A0] uppercase tracking-wider px-3 py-1">Portal Warga</div>
                    <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.dashboard') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>🏠</span> <span>Beranda & Riwayat Aduan</span>
                    </a>
                    <a href="{{ route('warga.reports.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.reports.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>📸</span> <span>Buat Laporan Baru</span>
                    </a>
                    <a href="{{ route('warga.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.ratings.*') ? 'bg-[#C76B45] text-white font-semibold shadow-sm' : 'text-[#E9E5DA]/85 hover:bg-[#353A37]/60' }}">
                        <span>⭐</span> <span>Ulasan & Rating Saya</span>
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-[#A7B5A0]/20 bg-[#1d2e2a]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-[#C76B45] flex items-center justify-center font-bold text-sm text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold truncate text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-[#A7B5A0] truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 bg-[#C76B45]/20 hover:bg-[#C76B45] text-[#E9E5DA] hover:text-white border border-[#C76B45]/40 rounded-lg text-xs font-semibold transition">
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama (Warm Ivory #E9E5DA & Charcoal #353A37) -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#E9E5DA]">
            <header class="bg-[#F5F2EB] border-b border-[#A7B5A0]/40 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-[#263B36]">@yield('header', 'Dasbor Sistem ROVIX')</h2>
                    <p class="text-xs text-[#353A37]/75">@yield('subheader', 'SDG 11 — Citizen-Led Road Monitoring & Infrastructure Vigilance')</p>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->district)
                        <span class="px-3 py-1.5 bg-[#A7B5A0]/30 text-[#263B36] border border-[#A7B5A0]/50 rounded-lg text-xs font-semibold">
                            📍 Zona: {{ auth()->user()->district->name }}
                        </span>
                    @endif
                    <span class="text-xs font-medium text-[#353A37]/70">{{ now()->format('d M Y') }}</span>
                </div>
            </header>

            <main class="flex-1 p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-[#A7B5A0]/30 border border-[#263B36]/30 text-[#263B36] text-sm font-medium flex items-center justify-between">
                        <span>✅ {{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-[#C76B45]/20 border border-[#C76B45]/50 text-[#263B36] text-sm font-medium flex items-center justify-between">
                        <span>⚠️ {{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @else
        @yield('content')
    @endauth

</body>
</html>
