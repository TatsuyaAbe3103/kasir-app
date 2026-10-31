@extends('layouts.app')

@section('title', 'Pusat Panduan & Dokumentasi Sistem - ' . ($appBrandName ?? 'POS'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{
    searchQuery: '',
    activeTopic: 'kasir-pos',
    interactiveTour: false,
    whatsappUrl: 'https://wa.me/6281316972236?text=' + encodeURIComponent('Halo Admin, saya ingin konsultasi mengenai sistem POS ini. Bisa info detailnya?'),

    topics: [
        { id: 'kasir-pos', title: 'Kasir POS', group: 'KASIR & SHIFT', icon: 'fa-cash-register', readTime: '2 menit' },
        { id: 'shift-kasir', title: 'Shift Kasir & Tutup Buku', group: 'KASIR & SHIFT', icon: 'fa-clock-rotate-left', readTime: '2 menit' },
        { id: 'pisah-bayar', title: 'Pisah Bayar & Multi-Bill', group: 'KASIR & SHIFT', icon: 'fa-code-branch', readTime: '3 menit' },
        { id: 'meja-qr', title: 'Pesanan Meja & QR Mandiri', group: 'KASIR & SHIFT', icon: 'fa-qrcode', readTime: '3 menit' },
        { id: 'kds-dapur', title: 'Tiket Dapur & Bar', group: 'KASIR & SHIFT', icon: 'fa-utensils', readTime: '2 menit' },

        { id: 'riwayat-pesanan', title: 'Riwayat Pesanan & Void', group: 'RIWAYAT & OPERASIONAL', icon: 'fa-receipt', readTime: '2 menit' },
        { id: 'daftar-menu', title: 'Daftar Menu & Varian', group: 'RIWAYAT & OPERASIONAL', icon: 'fa-bowl-food', readTime: '3 menit' },
        { id: 'stok-dapur', title: 'Audit Stok & Bahan Baku', group: 'RIWAYAT & OPERASIONAL', icon: 'fa-boxes-stacked', readTime: '3 menit' },

        { id: 'laporan-penjualan', title: 'Laporan Penjualan & Laba', group: 'MANAJEMEN & LAPORAN', icon: 'fa-chart-line', readTime: '3 menit' },
        { id: 'kupon-promo', title: 'Kupon & Diskon Promo', group: 'MANAJEMEN & LAPORAN', icon: 'fa-ticket', readTime: '2 menit' },
        { id: 'kelola-karyawan', title: 'Kelola Karyawan & User ID', group: 'MANAJEMEN & LAPORAN', icon: 'fa-users-gear', readTime: '2 menit' },
        { id: 'pengaturan-toko', title: 'Pengaturan Toko & Pajak', group: 'MANAJEMEN & LAPORAN', icon: 'fa-gear', readTime: '2 menit' }
    ],

    matchesSearch(topic) {
        if (!this.searchQuery) return true;
        const q = this.searchQuery.toLowerCase();
        return topic.title.toLowerCase().includes(q) || topic.group.toLowerCase().includes(q);
    }
}">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-blue-500/10 text-blue-600 font-bold text-xs uppercase tracking-wider">
                    Knowledge Base & Panduan Sistem
                </span>
                <span class="text-xs text-slate-400">• Versi 2.4 Ready to Sell</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Pusat Panduan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Panduan transaksi kasir, shift kas, KDS layar dapur, pisah bayar, dan cek ketersediaan menu.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Tur Interaktif Toggle -->
            <div class="flex items-center space-x-2.5 bg-white border border-slate-200 px-3.5 py-2 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-slate-700">Tur Interaktif</span>
                <button 
                    type="button" 
                    @click="interactiveTour = !interactiveTour" 
                    :class="interactiveTour ? 'bg-amber-500' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                >
                    <span 
                        :class="interactiveTour ? 'translate-x-5' : 'translate-x-0'"
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    ></span>
                </button>
            </div>

            <!-- Tombol Beli / WhatsApp CTA -->
            <a 
                :href="whatsappUrl" 
                target="_blank" 
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-2xl transition shadow-md shadow-blue-500/20 flex items-center space-x-2"
            >
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Daftar / Beli Lisensi &nearr;</span>
            </a>
        </div>
    </div>

    <!-- Alert Tur Interaktif jika aktif -->
    <div x-show="interactiveTour" x-cloak class="mt-4 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm flex-shrink-0 animate-bounce">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div>
                <strong class="text-xs font-black block">Mode Tur Interaktif Diaktifkan!</strong>
                <span class="text-xs text-amber-700">Klik tombol di sebelah kanan untuk langsung mencoba alur transaksi kasir dengan panduan balon petunjuk.</span>
            </div>
        </div>
        <a href="{{ route('pos.index') }}?tour=1" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition whitespace-nowrap shadow-xs">
            Buka Layar Kasir &rarr;
        </a>
    </div>

    <!-- Main 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6 items-start">
        
        <!-- ============================================================== -->
        <!-- KOLOM KIRI: DAFTAR TOPIK & FILTER PENCARIAN                     -->
        <!-- ============================================================== -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Kotak Pencarian Topik -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari topik panduan..." 
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-xs transition"
                >
                <button 
                    type="button" 
                    x-show="searchQuery" 
                    @click="searchQuery = ''" 
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- List Menu Topik (Grouped) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-3 space-y-5 shadow-xs">
                
                <!-- Group 1: Kasir & Shift -->
                <div>
                    <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
                        KASIR & SHIFT
                    </span>
                    <div class="space-y-1">
                        <template x-for="t in topics.filter(x => x.group === 'KASIR & SHIFT')" :key="t.id">
                            <button 
                                type="button" 
                                x-show="matchesSearch(t)"
                                @click="activeTopic = t.id"
                                :class="activeTopic === t.id ? 'bg-blue-50 text-blue-700 font-extrabold border-blue-200 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold border-transparent'"
                                class="w-full text-left px-3 py-2.5 rounded-2xl text-xs flex items-center justify-between border transition"
                            >
                                <div class="flex items-center space-x-2.5">
                                    <i :class="'fa-solid ' + t.icon + ' text-xs w-4 text-center ' + (activeTopic === t.id ? 'text-blue-600' : 'text-slate-400')"></i>
                                    <span x-text="t.title"></span>
                                </div>
                                <i x-show="activeTopic === t.id" class="fa-solid fa-chevron-right text-[10px] text-blue-500"></i>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Group 2: Riwayat & Operasional -->
                <div>
                    <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
                        RIWAYAT & OPERASIONAL
                    </span>
                    <div class="space-y-1">
                        <template x-for="t in topics.filter(x => x.group === 'RIWAYAT & OPERASIONAL')" :key="t.id">
                            <button 
                                type="button" 
                                x-show="matchesSearch(t)"
                                @click="activeTopic = t.id"
                                :class="activeTopic === t.id ? 'bg-blue-50 text-blue-700 font-extrabold border-blue-200 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold border-transparent'"
                                class="w-full text-left px-3 py-2.5 rounded-2xl text-xs flex items-center justify-between border transition"
                            >
                                <div class="flex items-center space-x-2.5">
                                    <i :class="'fa-solid ' + t.icon + ' text-xs w-4 text-center ' + (activeTopic === t.id ? 'text-blue-600' : 'text-slate-400')"></i>
                                    <span x-text="t.title"></span>
                                </div>
                                <i x-show="activeTopic === t.id" class="fa-solid fa-chevron-right text-[10px] text-blue-500"></i>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Group 3: Manajemen & Laporan -->
                <div>
                    <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
                        MANAJEMEN & LAPORAN
                    </span>
                    <div class="space-y-1">
                        <template x-for="t in topics.filter(x => x.group === 'MANAJEMEN & LAPORAN')" :key="t.id">
                            <button 
                                type="button" 
                                x-show="matchesSearch(t)"
                                @click="activeTopic = t.id"
                                :class="activeTopic === t.id ? 'bg-blue-50 text-blue-700 font-extrabold border-blue-200 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold border-transparent'"
                                class="w-full text-left px-3 py-2.5 rounded-2xl text-xs flex items-center justify-between border transition"
                            >
                                <div class="flex items-center space-x-2.5">
                                    <i :class="'fa-solid ' + t.icon + ' text-xs w-4 text-center ' + (activeTopic === t.id ? 'text-blue-600' : 'text-slate-400')"></i>
                                    <span x-text="t.title"></span>
                                </div>
                                <i x-show="activeTopic === t.id" class="fa-solid fa-chevron-right text-[10px] text-blue-500"></i>
                            </button>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Kontak Penjual Box di Bawah Menu -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-3xl shadow-md space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black tracking-wide text-white">Ingin Pakai Sistem Ini di Resto Anda?</h4>
                        <span class="text-[10px] text-slate-400 block">Lisensi full source code / SaaS siap pakai</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-300">
                    Dapatkan sistem kasir lengkap, dukungan thermal printer, QR Meja mandiri, serta instalasi dibantu sampai siap jualan.
                </p>
                <a 
                    :href="whatsappUrl" 
                    target="_blank" 
                    class="w-full py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-xl flex items-center justify-center space-x-2 transition shadow-sm"
                >
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Hubungi WA: 0813-1697-2236</span>
                </a>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- KOLOM KANAN: KONTEN ARTIKEL DETAIL PANDUAN                     -->
        <!-- ============================================================== -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">

            <!-- ========================================== -->
            <!-- TOPIK 1: KASIR POS                         -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'kasir-pos'" class="space-y-6">
                <!-- Meta Breadcrumb -->
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Kasir & Shift</span>
                        <span>•</span>
                        <span>Penjualan & Kasir POS</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Kasir POS</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Alur transaksi kilat, memilih menu & varian, serta penerimaan multi-pembayaran.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Antarmuka Kasir POS dirancang untuk operasional berkecepatan tinggi pada jam ramai (rush hour). Kasir dapat memproses transaksi menggunakan layar sentuh (touchscreen) maupun tombol shortcut keyboard fisik tanpa perlu menggunakan mouse.
                </p>

                <!-- Tips Kecepatan Kasir Box -->
                <div class="bg-blue-50/80 border border-blue-200 p-4 rounded-2xl flex items-start space-x-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-blue-900 uppercase tracking-wide">Tips Kecepatan Kasir</h4>
                        <p class="text-xs text-blue-800 mt-1">
                            Gunakan pencarian kilat di atas katalog menu atau klik kategori untuk memfilter cepat. Sistem otomatis menghitung pajak PB1 10% dan kembalian tunai secara instan.
                        </p>
                    </div>
                </div>

                <!-- Langkah & Prosedur -->
                <div class="space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Langkah & Prosedur Transaksi</h3>

                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">1</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Pilih Menu & Atur Jumlah</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Klik menu yang diinginkan. Anda dapat menambah catatan khusus seperti "Less sugar", "Extra pedas", atau "Tanpa bawang".</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">2</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Tentukan Tipe Order (Dine-in / Takeaway)</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Jika makan di tempat, pilih nomor meja tamu. Status meja akan otomatis berubah menjadi "Terisi" (Occupied).</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">3</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Masukkan Kupon Promo (Jika Ada)</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Ketik kode voucher diskon untuk memotong total pembayaran secara otomatis.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">4</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Pilih Metode Pembayaran & Cetak Struk</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Pilih Tunai, QRIS Dinamis, atau Transfer Bank. Setelah selesai, langsung klik <strong>Cetak Struk Kasir</strong> atau <strong>Tiket Dapur</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Coba Langsung di Layar Kasir &rarr;</span>
                    </a>
                    <a :href="whatsappUrl" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition flex items-center space-x-2">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Tanya Harga Sistem Ini &nearr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 2: SHIFT KASIR & TUTUP BUKU          -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'shift-kasir'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Kasir & Shift</span>
                        <span>•</span>
                        <span>Shift Kasir & Cash Drawer</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Shift Kasir & Tutup Buku (Closing)</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Kontrol uang modal awal laci, pencatatan otomatis transaksi per kasir, dan audit selisih kas fisik.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Sistem Shift Kasir dirancang dengan format standar retail dan F&B modern (mengadopsi format struk <em>CODK, CODC, dan CODT</em>). Fitur ini mencegah terjadinya kebocoran kas, mencatat siapa staf yang bertugas, serta memverifikasi uang fisik sebelum serah terima shift.
                </p>

                <div class="bg-amber-500/10 border border-amber-300 p-4 rounded-2xl flex items-start space-x-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-amber-950 uppercase tracking-wide">Pencocokan Kas Otomatis</h4>
                        <p class="text-xs text-amber-900 mt-1">
                            Sistem menghitung target kas laci: <strong>Modal Awal + Total Penjualan Tunai</strong>. Saat closing, kasir tinggal memasukkan nominal uang fisik yang dihitung di laci, dan sistem akan langsung melaporkan apakah kas <em>Cocok (Pas)</em>, <em>Kurang (Minus)</em>, atau <em>Lebih (Surplus)</em>.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Tahapan Buka & Tutup Kasir</h3>

                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">1</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Buka Kasir Baru (Open Shift)</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Klik tombol <strong>Buka Kasir</strong> di bar atas POS. Input uang modal awal (misal Rp 150.000). Sistem otomatis mencetak struk modal kas awal (CODK).</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">2</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Operasional Transaksi Harian</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Semua penjualan tunai, QRIS, dan transfer otomatis terhubung ke ID shift aktif kasir yang bersangkutan.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">3</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Closing Shift & Cetak Struk Audit</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Di akhir jam kerja, kasir klik <strong>Closing Shift</strong>, menghitung uang fisik laci, dan mencetak struk rangkuman tutup kasir (CODC / CODT).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('shift.index') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lihat Riwayat & Audit Shift &rarr;</span>
                    </a>
                    <a href="{{ route('pos.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition">
                        Buka / Tutup Shift di Kasir
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 3: PISAH BAYAR (SPLIT BILL)          -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'pisah-bayar'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Kasir & Shift</span>
                        <span>•</span>
                        <span>Split Bill 2 Tagihan</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 3 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Pisah Bayar & Multi-Bill (Split Bill)</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Membagi tagihan untuk tamu rombongan yang ingin bayar terpisah dengan struk masing-masing.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pelanggan sering kali makan bersama dalam satu meja namun ingin membayar secara patungan (misal dibagi dua sama rata). Daripada kasir harus membatalkan atau menginput ulang keranjang belanja dari nol, sistem menyediakan tombol instan <strong>Split Bill (2 Bill)</strong>.
                </p>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl flex items-start space-x-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-code-branch"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-blue-900 uppercase tracking-wide">Struk Terpisah & Tidak Duplikasi Stok</h4>
                        <p class="text-xs text-blue-800 mt-1">
                            Sistem secara cerdas membagi total tagihan menjadi <strong>BILL 1</strong> dan <strong>BILL 2</strong>, menerbitkan 2 invoice dan 2 struk thermal cetak terpisah, dengan pengurangan inventaris stok yang tetap presisi tanpa dobel potong.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Cara Menggunakan Split Bill</h3>

                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">1</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Pilih Menu & Klik Bayar</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Masukkan semua menu pesanan meja ke keranjang, lalu klik tombol Bayar Sekarang.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">2</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Pilih Tab "Split Bill (2 Bill)"</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Di dalam pop-up pembayaran, beralih dari mode <em>Bayar Penuh</em> ke tab <em>Split Bill (2 Bill)</em>.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">3</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Bayar Bagian 1 & Cetak Struk 1</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tamu pertama membayar separuh tagihannya (bisa via QRIS atau Tunai). Struk Bagian 1 langsung tercetak.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start space-x-3.5">
                            <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center flex-shrink-0">4</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Bayar Bagian 2 & Selesaikan Pesanan</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tamu kedua membayar sisa tagihan. Struk Bagian 2 tercetak dan status transaksi dinyatakan lunas sepenuhnya.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-xl transition shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Coba Split Bill di Layar Kasir &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 4: PESANAN MEJA (QR SELF-ORDER)      -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'meja-qr'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Fitur Unggulan</span>
                        <span>•</span>
                        <span>QR Code Meja & Self-Ordering</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 3 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Pesanan Meja & QR Mandiri (e-Menu)</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Tamu memesan langsung dari smartphone tanpa perlu mengunduh aplikasi.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Setiap meja cafe/resto memiliki barcode QR unik. Tamu cukup memindai (scan) kamera HP untuk melihat daftar menu foto menarik, memilih item, memasukkan pesanan, dan langsung membayar via QRIS atau Transfer Bank.
                </p>

                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-start space-x-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-emerald-900 uppercase tracking-wide">Hemat Tenaga Waiter & Antrean Kasir</h4>
                        <p class="text-xs text-emerald-800 mt-1">
                            Pesanan dari meja tamu langsung masuk ke Layar Kasir dan Dapur secara realtime, memotong waktu tunggu hingga 70%!
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('guest.menu', 1) }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl transition shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-mobile-screen"></i>
                        <span>Buka Simulasi e-Menu Meja 1 &nearr;</span>
                    </a>
                    <a href="{{ route('pos.tables') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition">
                        Lihat Denah Meja & Cetak QR &rarr;
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 5: TIKET DAPUR & BAR (KDS)           -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'kds-dapur'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Kasir & Shift</span>
                        <span>•</span>
                        <span>Kitchen Display & Tiket Dapur</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Tiket Dapur & Bar (KDS Ticket)</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Format khusus untuk tim koki dapur dan barista tanpa memuat nominal harga kasir.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Setiap pesanan yang dibuat di kasir atau dari QR Meja dapat langsung dicetak sebagai <strong>Tiket Dapur (Kitchen Ticket)</strong>. Format tiket ini difokuskan pada nama menu, kuantitas yang besar dan jelas, nomor meja tamu, serta catatan kustom (misal: "Kurang manis, ekstra sambal").
                </p>

                <div class="bg-slate-900 text-white p-4 rounded-2xl flex items-start space-x-3.5 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-amber-400 uppercase tracking-wide">Bebas Salah Pesan (Zero Miss-Communication)</h4>
                        <p class="text-xs text-slate-300 mt-1">
                            Koki tidak perlu menebak atau bertanya ulang ke waiter. Tiket dapur mencantumkan jam pemesanan sehingga dapur dapat menerapkan prinsip *FIFO (First In, First Out)* dengan tertib.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center space-x-3">
                    <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition">
                        Coba Buat Order di Kasir &rarr;
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 6: RIWAYAT PESANAN & VOID            -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'riwayat-pesanan'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Riwayat & Operasional</span>
                        <span>•</span>
                        <span>Pelunasan & Void Order</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Riwayat Pesanan & Pembatalan (Void)</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Pencarian transaksi lampau, cetak ulang struk, dan audit pembatalan pesanan.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Menu Riwayat Pesanan menyimpan seluruh arsip transaksi. Anda dapat memfilter pesanan berdasarkan tanggal, status pembayaran (Paid / Unpaid), atau nomor invoice. Untuk pesanan tamu dine-in yang belum lunas, kasir dapat melakukan pelunasan langsung dari layar riwayat ini.
                </p>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <h4 class="text-xs font-bold text-slate-900">Fitur Penting di Riwayat Pesanan:</h4>
                    <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                        <li><strong>Reprint Struk Thermal:</strong> Cetak ulang struk jika kertas kasir macet atau tamu meminta bukti cetak tambahan.</li>
                        <li><strong>Pelunasan Meja (Pay Later):</strong> Tamu memesan diawal lalu membayar belakangan sebelum pulang.</li>
                        <li><strong>Keamanan Void Order:</strong> Pembatalan pesanan hanya dapat dilakukan oleh akun berwenang dan wajib mencantumkan alasan pembatalan untuk audit pemilik resto.</li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('pos.orders') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Buka Riwayat Pesanan &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 7: DAFTAR MENU & VARIAN              -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'daftar-menu'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Riwayat & Operasional</span>
                        <span>•</span>
                        <span>Manajemen Katalog Produk</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 3 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Menu & Varian Produk</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Atur foto makanan/minuman, kategori produk, harga modal (HPP), dan status ketersediaan.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Menambah dan mengubah menu di sistem POS sangat mudah dan instan. Setiap produk dapat dikelompokkan ke dalam kategori (Coffee, Non-Coffee, Makanan Utama, Snack, Dessert), memiliki kode SKU unik, serta foto beresolusi tinggi yang menarik bagi tamu e-Menu.
                </p>

                <div class="space-y-3">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                        <strong class="text-slate-900 block font-bold mb-1">Toggle Menu Habis (Sold Out) Seketika:</strong>
                        Jika bahan kopi tertentu habis di tengah operasional resto, Anda cukup menekan tombol toggle pada produk. Menu tersebut otomatis berstatus habis dan tidak bisa diorder oleh kasir maupun tamu meja, tanpa perlu menghapus master data menu.
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-bowl-food"></i>
                        <span>Kelola Katalog Menu &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 8: AUDIT STOK & BAHAN BAKU           -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'stok-dapur'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Gudang & Inventaris</span>
                        <span>•</span>
                        <span>Audit Stok Bahan Baku</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 3 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Audit Stok & Bahan Baku</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Kontrol sisa bahan kopi, susu, daging, dan packaging agar tidak pernah kehabisan atau bocor.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Sistem inventaris mencatat setiap pergerakan barang secara otomatis saat kasir menjual menu, serta menyediakan fitur stock opname, peringatan stok menipis (low-stock alert), dan log riwayat mutasi masuk/keluar.
                </p>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <h4 class="text-xs font-bold text-slate-900">Fitur Utama Inventaris:</h4>
                    <ul class="text-xs text-slate-600 space-y-1 list-disc list-inside">
                        <li><strong>Penyesuaian Stok (Opname):</strong> Tambah barang masuk dari supplier atau koreksi fisik.</li>
                        <li><strong>Toggle Menu Habis (Sold Out):</strong> Matikan menu seketika jika bahan habis tanpa perlu menghapus menu.</li>
                        <li><strong>Catatan Alasan Mutasi:</strong> Setiap barang keluar wajib mencantumkan alasan (Expired, Rusak, Penjualan).</li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-black text-xs rounded-xl transition shadow-sm inline-flex items-center space-x-2">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Buka Menu Audit Stok &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 9: LAPORAN PENJUALAN & LABA          -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'laporan-penjualan'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Admin & Owner</span>
                        <span>•</span>
                        <span>Laporan Finansial & Omzet</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 3 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Penjualan & Laba Bersih</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Pantau omzet harian, mingguan, bulanan, pajak resto PB1, dan margin keuntungan bersih.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pemilik resto mendapatkan rekapitulasi data akurat mengenai produk terlaris (best-seller), metode pembayaran favorit pelanggan, total pajak yang terkumpul, dan laporan siap cetak untuk keperluan pembukuan.
                </p>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-sm inline-flex items-center space-x-2">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Lihat Grafik Laporan Penjualan &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 10: KUPON & DISKON PROMO             -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'kupon-promo'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Manajemen & Laporan</span>
                        <span>•</span>
                        <span>Voucher Diskon & Promo</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Kupon Promo & Diskon Belanja</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Tingkatkan omzet dan loyalitas tamu dengan program potongan harga otomatis.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Anda dapat membuat berbagai jenis promo: potongan persentase (misal: Diskon 15%), potongan nominal tetap (misal: Potongan Rp 10.000), dengan syarat batas minimum belanja dan kuota pemakaian per voucher.
                </p>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 space-y-2">
                    <h4 class="text-xs font-bold text-amber-950">Cara Kerja Kupon di Kasir:</h4>
                    <p class="text-xs text-amber-900 leading-relaxed">
                        Kasir atau tamu tinggal memasukkan kode promo pada kolom Kupon Promo saat checkout. Sistem memvalidasi tanggal aktif, sisa kuota, dan minimal belanja, lalu memotong total tagihan secara otomatis di struk.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Kelola Kupon Promo &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 11: KELOLA KARYAWAN & USER ID        -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'kelola-karyawan'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Manajemen & Laporan</span>
                        <span>•</span>
                        <span>Hak Akses & Approval Tim</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Karyawan & User ID</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Pengaturan peran tugas: Admin/Owner, Kasir POS, dan Auditor Stok Gudang.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Setiap staf memiliki akun login tersendiri. Fitur ini menjamin bahwa setiap pembukaan laci kasir dan pesanan tercatat atas nama staf yang bertugas, serta membatasi akses menu rahasia (seperti laporan laba/keuangan) hanya untuk Owner / Manager Resto.
                </p>

                <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl space-y-2">
                    <h4 class="text-xs font-black text-slate-900 uppercase">Sistem Registrasi dengan Persetujuan Admin (Approval System):</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Staf baru dapat mendaftar mandiri melalui halaman registrasi. Namun, akun tersebut berstatus <em>Pending</em> dan baru bisa login setelah di-approve oleh Admin melalui menu <strong>Kelola Pengguna</strong>. Hal ini mencegah akses ilegal ke sistem resto Anda.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Kelola Tim & Hak Akses &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TOPIK 12: PENGATURAN TOKO & PRINTER        -->
            <!-- ========================================== -->
            <div x-show="activeTopic === 'pengaturan-toko'" class="space-y-6">
                <div class="flex items-center justify-between text-xs text-slate-400 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2 font-medium">
                        <span class="text-blue-600 font-bold">Manajemen & Laporan</span>
                        <span>•</span>
                        <span>Branding & Printer Thermal</span>
                    </div>
                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded-md"><i class="fa-regular fa-clock"></i> 2 menit baca</span>
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Toko, Pajak & Printer Thermal</h2>
                    <p class="text-sm text-slate-500 mt-1 font-medium">Ubah nama resto, logo, alamat struk, persentase pajak PB1, dan opsi printer kasir.</p>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Sesuaikan seluruh identitas bisnis Anda melalui menu Pengaturan Toko. Perubahan nama resto, nomor WhatsApp pesanan, dan logo akan langsung otomatis tercermin di halaman Login, Header Aplikasi, Struk Thermal Cetak, dan Menu Tamu QR.
                </p>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <h4 class="text-xs font-bold text-slate-900">Konfigurasi Printer Thermal POS:</h4>
                    <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                        <li><strong>Pilihan Lebar Kertas:</strong> Mendukung ukuran standar <strong>58mm</strong> (printer mini portabel/Bluetooth) dan <strong>80mm</strong> (printer desktop kasir USB/LAN).</li>
                        <li><strong>Auto-Print Toggle:</strong> Jika diaktifkan, dialog print browser akan otomatis terbuka sesaat setelah transaksi dinyatakan lunas.</li>
                        <li><strong>Tombol Test Print:</strong> Uji koneksi printer langsung dari tombol printer di kanan atas layar kasir.</li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-gear"></i>
                        <span>Buka Pengaturan Toko &rarr;</span>
                    </a>
                    <a :href="whatsappUrl" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center space-x-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Bantuan Setting & Lisensi</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
