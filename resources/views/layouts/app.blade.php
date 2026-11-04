<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ROVIX — Road Vigilance & Index')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    @auth
    <div class="flex min-h-screen">
        <!-- Sidebar Navigasi Berdasarkan Role RBAC -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col shrink-0">
            <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center font-extrabold text-slate-900 text-lg shadow">
                    RV
                </div>
                <div>
                    <h1 class="font-bold text-lg tracking-tight leading-none">ROVIX</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Road Vigilance & Index</p>
                </div>
            </div>

            <!-- Badge Role Pengguna Aktif -->
            <div class="px-5 py-3 bg-slate-800/70 border-b border-slate-800 flex items-center justify-between">
                <span class="text-xs text-slate-400">Hak Akses (RBAC):</span>
                @if(auth()->user()->role === 'admin')
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">ADMIN DINAS</span>
                @elseif(auth()->user()->role === 'petugas')
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">PETUGAS PU</span>
                @else
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">WARGA</span>
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-1.5 text-sm overflow-y-auto">
                @if(auth()->user()->role === 'admin')
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 py-1">Operasional & GIS</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>📊</span> <span>Dasbor Peta & Metrik</span>
                    </a>
                    <a href="{{ route('admin.verifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.verifications.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🗺️</span> <span>Verifikasi & Peta GIS</span>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.work-orders.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🛠️</span> <span>Disposisi SPK Digital</span>
                    </a>
                    <a href="{{ route('admin.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.ratings.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>⭐</span> <span>Evaluasi & Rating Warga</span>
                    </a>

                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 pt-4 pb-1">Master Data & RBAC</div>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.categories.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🏷️</span> <span>Master Kategori</span>
                    </a>
                    <a href="{{ route('admin.districts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.districts.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>📍</span> <span>Master Wilayah</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>👥</span> <span>Pengguna & RBAC</span>
                    </a>
                @elseif(auth()->user()->role === 'petugas')
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 py-1">Field View Petugas</div>
                    <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('petugas.dashboard') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🛠️</span> <span>Daftar Tugas SPK Aktif</span>
                    </a>
                    <a href="{{ route('petugas.work-orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('petugas.work-orders.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>📷</span> <span>Update Progres & Foto</span>
                    </a>
                @else
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 py-1">Portal Warga</div>
                    <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.dashboard') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🏠</span> <span>Beranda & Riwayat Aduan</span>
                    </a>
                    <a href="{{ route('warga.reports.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.reports.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>📸</span> <span>Buat Laporan Baru</span>
                    </a>
                    <a href="{{ route('warga.ratings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('warga.ratings.*') ? 'bg-amber-500 text-slate-950 font-semibold' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>⭐</span> <span>Ulasan & Rating Saya</span>
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-sm text-amber-400">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 rounded-lg text-xs font-semibold transition">
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">@yield('header', 'Dasbor Sistem ROVIX')</h2>
                    <p class="text-xs text-slate-500">@yield('subheader', 'SDG 11 — Citizen-Led Road Monitoring & Infrastructure Vigilance')</p>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->district)
                        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                            📍 Zona: {{ auth()->user()->district->name }}
                        </span>
                    @endif
                    <span class="text-xs text-slate-400">{{ now()->format('d M Y') }}</span>
                </div>
            </header>

            <main class="flex-1 p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                        <span>✅ {{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
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
