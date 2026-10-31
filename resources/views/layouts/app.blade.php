<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($appBrandName ?? 'POS') . ' - Sistem Kasir Cafe & Resto')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap');
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-receipt { font-family: 'Space Mono', monospace; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased flex flex-col min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900 text-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left Side: Tombol Toggle Sidebar (Menu) & Logo Resto -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    @auth
                        <!-- Tombol Buka Sidebar (Drawer) -->
                        <button 
                            type="button" 
                            @click="sidebarOpen = !sidebarOpen" 
                            class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 hover:text-white flex items-center space-x-2 border border-slate-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                            title="Buka Menu Navigasi"
                        >
                            <i class="fa-solid fa-bars-staggered text-amber-400 text-sm"></i>
                            <span class="text-xs font-black tracking-wider uppercase">Menu</span>
                        </button>
                    @endauth

                    <!-- Logo & Brand Header -->
                    <a href="{{ Auth::user() && Auth::user()->isAuditor() ? route('inventory.index') : route('pos.index') }}" class="flex items-center space-x-2.5 sm:space-x-3 group">
                        @if(!empty($appBrandLogo))
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden bg-slate-800 border border-slate-700/80 flex items-center justify-center shadow-lg group-hover:scale-105 transition">
                                <img src="{{ asset($appBrandLogo) }}" alt="{{ $appBrandName }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 group-hover:scale-105 transition">
                                <i class="fa-solid fa-mug-hot text-base sm:text-lg"></i>
                            </div>
                        @endif
                        <div>
                            <span class="font-black text-base sm:text-lg tracking-tight text-white block leading-tight">{{ $appBrandName }}</span>
                            <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-widest font-semibold block">{{ $appBrandTagline }}</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Quick Primary Shortcut (Akses Cepat Kasir) -->
                <div class="hidden md:flex items-center space-x-2">
                    @auth
                        @if(!Auth::user()->isAuditor())
                            <a href="{{ route('pos.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ request()->routeIs('pos.index') ? 'bg-amber-500 text-slate-950 font-black shadow-sm' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700' }}">
                                <i class="fa-solid fa-cash-register text-amber-400"></i>
                                <span>Layar Kasir</span>
                            </a>
                        @endif
                        <a href="{{ route('guide.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ request()->routeIs('guide.*') ? 'bg-blue-600 text-white font-black shadow-sm' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700' }}">
                            <i class="fa-solid fa-book-open text-sky-400"></i>
                            <span>Panduan</span>
                        </a>
                    @endauth
                </div>

                <!-- Right Side: User Profile Pill & Logout -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    @auth
                        <!-- User Role Badge -->
                        <div class="flex items-center space-x-2 bg-slate-800 border border-slate-700/80 rounded-full py-1 px-3">
                            <span class="w-2 h-2 rounded-full {{ Auth::user()->isAdmin() ? 'bg-amber-400 animate-pulse' : (Auth::user()->isAuditor() ? 'bg-sky-400' : 'bg-emerald-400') }}"></span>
                            <span class="text-xs font-medium text-slate-200">
                                <strong class="text-white">{{ Auth::user()->name }}</strong> 
                                <span class="text-[10px] text-slate-400 hidden sm:inline">({{ ucfirst(Auth::user()->role) }})</span>
                            </span>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar / Logout" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-700 flex items-center justify-center transition">
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 bg-amber-500 text-slate-950 font-black text-xs rounded-xl hover:bg-amber-600 transition shadow-sm">
                            Login
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    @auth
        <!-- ============================================================== -->
        <!-- COLLAPSIBLE SIDEBAR DRAWER (BISA BUKA & TUTUP DARI SAMPING)    -->
        <!-- ============================================================== -->
        
        <!-- Backdrop Blur Gelap -->
        <div 
            x-show="sidebarOpen" 
            x-cloak 
            @click="sidebarOpen = false" 
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50"
        ></div>

        <!-- Panel Sidebar Slide-In -->
        <aside 
            x-show="sidebarOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            @keydown.escape.window="sidebarOpen = false"
            class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-slate-900 text-white z-50 flex flex-col shadow-2xl border-r border-slate-800"
        >
            <!-- Header Sidebar -->
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if(!empty($appBrandLogo))
                        <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-800 border border-slate-700 flex items-center justify-center shadow">
                            <img src="{{ asset($appBrandLogo) }}" alt="{{ $appBrandName }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="font-black text-sm text-white leading-tight">{{ $appBrandName }}</h2>
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">{{ $appBrandTagline }}</span>
                    </div>
                </div>

                <!-- Tombol Tutup Sidebar (X) -->
                <button 
                    type="button" 
                    @click="sidebarOpen = false"
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition"
                    title="Tutup Menu"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Kartu Profil Pengguna di Sidebar -->
            <div class="p-4 bg-slate-950/60 border-b border-slate-800/80">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm {{ Auth::user()->isAdmin() ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : (Auth::user()->isAuditor() ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30') }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <strong class="text-xs font-bold text-white block truncate">{{ Auth::user()->name }}</strong>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ Auth::user()->isAdmin() ? 'bg-amber-400/20 text-amber-300' : (Auth::user()->isAuditor() ? 'bg-sky-400/20 text-sky-300' : 'bg-emerald-400/20 text-emerald-300') }}">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                            @if(Auth::user()->username)
                                <span class="text-[10px] font-mono text-slate-400 truncate">&#64;{{ Auth::user()->username }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Menu Navigasi (Bisa Di-scroll) -->
            <div class="flex-1 overflow-y-auto p-3 space-y-5">
                
                <!-- Kelompok 1: Operasional Kasir -->
                @if(!Auth::user()->isAuditor())
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
                            Operasional Kasir
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('pos.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('pos.index') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-cash-register text-sm w-5 text-center {{ request()->routeIs('pos.index') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Layar Kasir</span>
                            </a>
                            <a href="{{ route('pos.orders') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('pos.orders') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-receipt text-sm w-5 text-center {{ request()->routeIs('pos.orders') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Riwayat Pesanan</span>
                            </a>
                            <a href="{{ route('pos.tables') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('pos.tables') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chair text-sm w-5 text-center {{ request()->routeIs('pos.tables') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Denah & Status Meja</span>
                            </a>
                            <a href="{{ route('shift.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('shift.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-clock-rotate-left text-sm w-5 text-center {{ request()->routeIs('shift.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Shift Kasir & Rekap Kas</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Kelompok 2: Audit & Gudang Bahan -->
                @if(Auth::user()->canAudit())
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
                            Gudang & Inventaris
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('inventory.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('inventory.index') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-boxes-stacked text-sm w-5 text-center {{ request()->routeIs('inventory.index') ? 'text-slate-950' : 'text-sky-400' }}"></i>
                                <span>Audit Stok Bahan</span>
                            </a>
                            <a href="{{ route('inventory.logs') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('inventory.logs') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-right-left text-sm w-5 text-center {{ request()->routeIs('inventory.logs') ? 'text-slate-950' : 'text-sky-400' }}"></i>
                                <span>Laporan Masuk / Keluar</span>
                            </a>
                            <a href="{{ route('inventory.deleted_orders') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('inventory.deleted_orders') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-trash-can text-sm w-5 text-center {{ request()->routeIs('inventory.deleted_orders') ? 'text-slate-950' : 'text-rose-400' }}"></i>
                                <span>Order Dihapus / Void</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Kelompok 3: Manajemen Admin Resto -->
                @if(Auth::user()->isAdmin())
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase tracking-wider text-amber-400 block mb-1.5">
                            Area Manager & Admin
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('admin.products.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('admin.products.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-utensils text-sm w-5 text-center {{ request()->routeIs('admin.products.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Kelola Menu & Kategori</span>
                            </a>
                            <a href="{{ route('admin.coupons.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('admin.coupons.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-ticket text-sm w-5 text-center {{ request()->routeIs('admin.coupons.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Kupon & Diskon</span>
                            </a>
                            <a href="{{ route('admin.reports.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chart-line text-sm w-5 text-center {{ request()->routeIs('admin.reports.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Laporan Penjualan</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('admin.users.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-users-gear text-sm w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Kelola Pengguna & Akses</span>
                            </a>
                            <a href="{{ route('admin.settings.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center space-x-3 {{ request()->routeIs('admin.settings.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-gear text-sm w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Pengaturan Toko & Struk</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Kelompok Khusus: Bantuan & Pusat Panduan -->
                <div>
                    <span class="px-3 text-[10px] font-black uppercase tracking-wider text-sky-400 block mb-1.5">
                        Bantuan & Panduan
                    </span>
                    <div class="space-y-1">
                        <a href="{{ route('guide.index') }}" @click="sidebarOpen = false" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-between {{ request()->routeIs('guide.*') ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-book-open text-sm w-5 text-center {{ request()->routeIs('guide.*') ? 'text-white' : 'text-sky-400' }}"></i>
                                <span>Pusat Panduan</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                        </a>
                        <a href="https://wa.me/6281316972236" target="_blank" class="w-full px-3 py-2.5 rounded-xl text-xs font-bold text-emerald-400 hover:bg-emerald-500/10 transition flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fa-brands fa-whatsapp text-sm w-5 text-center"></i>
                                <span>Hubungi WhatsApp</span>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Footer Sidebar: Tombol Logout Jelas -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 hover:text-rose-200 border border-rose-500/30 font-bold text-xs flex items-center justify-center space-x-2 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar Sistem (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>
    @endauth

    <!-- Main Content Area -->
    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <span class="text-xs font-bold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                        <span class="text-xs font-bold">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
