@extends('layouts.app')

@section('title', 'Layar Kasir POS - ' . ($appBrandName ?? 'POS'))

@section('content')
<div x-data="posApp()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-4">

    <!-- ============================================================== -->
    <!-- BAR KONTROL SHIFT KASIR & PRINTER (STATUS CASH DRAWER)         -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <!-- Status Shift -->
        <div class="flex items-center space-x-3">
            <template x-if="activeShift">
                <div class="flex items-center space-x-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-black text-slate-900" x-text="'Shift #' + activeShift.id + ' AKTIF'"></span>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full" x-text="activeShift.cashier_name"></span>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Kas Awal: <strong class="text-slate-800" x-text="formatRupiah(activeShift.starting_cash)"></strong> • Buka sejak <span x-text="activeShift.opened_at ? activeShift.opened_at.substring(11, 16) : '-'"></span>
                        </p>
                    </div>
                </div>
            </template>

            <template x-if="!activeShift">
                <div class="flex items-center space-x-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <div>
                        <span class="text-xs font-black text-slate-900 block">Shift Kasir Belum Dibuka</span>
                        <p class="text-[11px] text-slate-500">Buka kasir untuk mencatat modal uang kembalian di laci kas.</p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Tombol Aksi Shift & Printer -->
        <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
            <!-- Tombol Buka Shift (Jika belum buka) -->
            <button 
                type="button" 
                x-show="!activeShift"
                @click="showOpenShiftModal = true"
                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl transition shadow-xs flex items-center space-x-1.5"
            >
                <i class="fa-solid fa-door-open"></i>
                <span>Buka Kasir Baru</span>
            </button>

            <!-- Tombol Cetak Kas Awal (Jika shift aktif) -->
            <button 
                type="button" 
                x-show="activeShift"
                @click="printOpeningReceipt()"
                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center space-x-1.5"
                title="Cetak Ulang Struk Kas Awal"
            >
                <i class="fa-solid fa-receipt text-amber-500"></i>
                <span class="hidden sm:inline">Kas Awal</span>
            </button>

            <!-- Tombol Closing Shift (Jika shift aktif) -->
            <button 
                type="button" 
                x-show="activeShift"
                @click="openCloseShiftModal()"
                class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition shadow-xs flex items-center space-x-1.5"
            >
                <i class="fa-solid fa-lock text-amber-400"></i>
                <span>Closing Shift (Tutup Kas)</span>
            </button>

            <!-- Tombol Kontrol Printer -->
            <button 
                type="button" 
                @click="showPrinterModal = true"
                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center space-x-1.5 border border-slate-200"
                title="Pengaturan Kertas & Tes Cetak Printer"
            >
                <i class="fa-solid fa-print text-slate-600"></i>
                <span class="hidden sm:inline">Printer: <strong x-text="printerPaperSize"></strong></span>
            </button>
        </div>
    </div>

    <!-- Grid Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ========================================== -->
        <!-- KOLOM KIRI: KATALOG MENU (7/8 Kolom)        -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            <!-- Filter & Search Bar -->
            <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            placeholder="Cari menu atau kode..." 
                            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition"
                        >
                        <button 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Total Menu Badge -->
                    <div class="text-xs font-semibold text-slate-500 hidden sm:block">
                        Menampilkan <span x-text="filteredProducts.length" class="text-amber-600 font-bold"></span> Menu
                    </div>
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-1 no-scrollbar">
                    <button 
                        @click="selectedCategory = 'all'" 
                        :class="selectedCategory === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center space-x-1.5"
                    >
                        <span>✨ Semua</span>
                    </button>
                    @foreach($categories as $cat)
                        <button 
                            @click="selectedCategory = '{{ $cat->id }}'" 
                            :class="selectedCategory === '{{ $cat->id }}' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center space-x-1.5"
                        >
                            <span>{{ $cat->icon }} {{ $cat->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="item in filteredProducts" :key="item.id">
                    <div 
                        @click="addToCart(item)"
                        class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md hover:border-amber-400 cursor-pointer transition transform active:scale-95 flex flex-col group"
                    >
                        <!-- Image Container -->
                        <div class="relative h-32 w-full bg-slate-100 overflow-hidden">
                            <img 
                                :src="item.image || 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=60'" 
                                :alt="item.name" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                loading="lazy"
                            >
                            <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md" x-text="item.code"></span>
                        </div>

                        <!-- Content -->
                        <div class="p-3 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-1 leading-snug" x-text="item.name"></h4>
                                <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5" x-text="item.description || 'Menu lezat siap saji'"></p>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs sm:text-sm font-extrabold text-amber-600" x-text="formatRupiah(item.price)"></span>
                                <div class="w-7 h-7 rounded-lg bg-amber-50 group-hover:bg-amber-500 text-amber-600 group-hover:text-white flex items-center justify-center transition">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State for Search -->
            <div x-show="filteredProducts.length === 0" class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 text-2xl mb-3">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Menu tidak ditemukan</h3>
                <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian lain atau pilih kategori Semua.</p>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: KERANJANG & CHECKOUT (4 Kolom) -->
        <!-- ========================================== -->
        <div class="lg:col-span-5 xl:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-4 sm:p-5 flex flex-col sticky top-20">

            <!-- Cart Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Pesanan Kasir</h3>
                        <span class="text-[11px] text-slate-400" x-text="cartTotalCount() + ' Item dipilih'"></span>
                    </div>
                </div>

                <button 
                    x-show="cart.length > 0"
                    @click="clearCart()" 
                    class="text-xs font-bold text-rose-500 hover:text-rose-700 transition flex items-center space-x-1"
                >
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Reset</span>
                </button>
            </div>

            <!-- Order Options (Pelanggan, Meja / Takeaway) -->
            <div class="py-3 border-b border-slate-100 space-y-2.5">
                <!-- Tipe Order: Dine-in vs Takeaway -->
                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl">
                    <button 
                        type="button"
                        @click="orderType = 'dine_in'"
                        :class="orderType === 'dine_in' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-500 font-semibold'"
                        class="py-1.5 text-xs rounded-lg transition flex items-center justify-center space-x-1.5"
                    >
                        <i class="fa-solid fa-chair text-[11px]"></i>
                        <span>Makan di Tempat</span>
                    </button>
                    <button 
                        type="button"
                        @click="orderType = 'takeaway'"
                        :class="orderType === 'takeaway' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-500 font-semibold'"
                        class="py-1.5 text-xs rounded-lg transition flex items-center justify-center space-x-1.5"
                    >
                        <i class="fa-solid fa-bag-shopping text-[11px]"></i>
                        <span>Bungkus</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Input Nama Pelanggan -->
                    <div>
                        <input 
                            type="text" 
                            x-model="customerName" 
                            placeholder="Nama Tamu (Opsional)" 
                            class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        >
                    </div>

                    <!-- Pilihan Meja (Hanya jika Dine-In) -->
                    <div x-show="orderType === 'dine_in'">
                        <select 
                            x-model="selectedTableId" 
                            class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold text-slate-700"
                        >
                            <option value="">-- Pilih Meja * --</option>
                            @foreach($tables as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} ({{ $t->status === 'available' ? 'Kosong' : 'Terisi' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="flex-1 overflow-y-auto py-3 space-y-3 min-h-[180px] max-h-[300px] pr-1">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="p-2.5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-800 text-xs line-clamp-1" x-text="item.name"></h4>
                                <span class="text-xs text-amber-600 font-extrabold" x-text="formatRupiah(item.price)"></span>
                            </div>

                            <!-- Quantity Counter -->
                            <div class="flex items-center space-x-1.5 bg-white border border-slate-200 rounded-xl p-1 shadow-2xs">
                                <button 
                                    @click="decreaseQty(index)" 
                                    class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold transition"
                                >
                                    -
                                </button>
                                <span class="w-6 text-center text-xs font-black text-slate-900" x-text="item.quantity"></span>
                                <button 
                                    @click="increaseQty(index)" 
                                    class="w-6 h-6 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 flex items-center justify-center text-xs font-black transition"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Notes Input per Item -->
                        <div>
                            <input 
                                type="text" 
                                x-model="item.notes" 
                                placeholder="Catatan: less sugar, pedas, dll..." 
                                class="w-full text-[11px] py-1.5 px-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-amber-500 text-slate-600"
                            >
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-cart-shopping text-3xl mb-2 block"></i>
                    <p class="text-xs font-semibold">Keranjang masih kosong</p>
                    <span class="text-[10px]">Klik menu di sebelah kiri untuk menambahkan.</span>
                </div>
            </div>

            <!-- Voucher & Discount Section -->
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-bold">Kupon Promo</span>
                    <button 
                        @click="showVoucherModal = true" 
                        class="text-amber-600 font-black hover:underline flex items-center space-x-1"
                    >
                        <i class="fa-solid fa-ticket text-[11px]"></i>
                        <span x-text="appliedCoupon ? appliedCoupon.code : 'Pilih Kupon'"></span>
                    </button>
                </div>

                <div x-show="appliedCoupon" class="flex items-center justify-between p-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <div>
                        <strong class="block" x-text="appliedCoupon ? appliedCoupon.name : ''"></strong>
                        <span class="text-[10px] text-amber-700" x-text="'Potongan ' + formatRupiah(discountAmount())"></span>
                    </div>
                    <button @click="appliedCoupon = null" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                        Hapus
                    </button>
                </div>
            </div>

            <!-- Bill Summary Calculations -->
            <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Subtotal Menu</span>
                    <span class="font-bold text-slate-800" x-text="formatRupiah(subtotal())"></span>
                </div>

                <template x-if="applyTax">
                    <div class="flex items-center justify-between text-slate-500">
                        <span x-text="'{{ $taxName }} (' + taxPercent + '%)'"></span>
                        <span class="font-bold text-slate-800" x-text="formatRupiah(taxAmount())"></span>
                    </div>
                </template>

                <template x-if="applyServiceCharge">
                    <div class="flex items-center justify-between text-slate-500">
                        <span x-text="'{{ $serviceChargeName }} (' + serviceChargePercent + '%)'"></span>
                        <span class="font-bold text-slate-800" x-text="formatRupiah(serviceChargeAmount())"></span>
                    </div>
                </template>

                <template x-if="appliedCoupon">
                    <div class="flex items-center justify-between text-emerald-600 font-bold">
                        <span>Diskon Promo</span>
                        <span x-text="'- ' + formatRupiah(discountAmount())"></span>
                    </div>
                </template>

                <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-base font-black text-slate-900">
                    <span>Total Tagihan</span>
                    <span class="text-amber-600 font-extrabold text-lg" x-text="formatRupiah(grandTotal())"></span>
                </div>
            </div>

            <!-- Checkout Action Buttons -->
            <div class="pt-4 mt-2">
                <button 
                    type="button"
                    @click="openPaymentModal()"
                    :disabled="cart.length === 0"
                    class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 disabled:opacity-40 text-slate-950 font-black rounded-2xl text-xs sm:text-sm transition shadow-md shadow-amber-500/25 flex items-center justify-center space-x-2"
                >
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Bayar Sekarang &rarr;</span>
                </button>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: BUKA SHIFT KASIR (INPUT KAS AWAL / MODAL LACI)        -->
    <!-- ============================================================== -->
    <div 
        x-show="showOpenShiftModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="showOpenShiftModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Buka Kasir Baru</h3>
                        <span class="text-[11px] text-slate-400">Input modal uang kembalian di laci kas</span>
                    </div>
                </div>
                <button type="button" @click="showOpenShiftModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Modal Kas Awal di Laci (Rp) *</label>
                    <input 
                        type="number" 
                        x-model.number="shiftStartingCash" 
                        class="w-full text-xl font-black p-3 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-slate-900"
                        placeholder="150000"
                    >
                </div>

                <!-- Tombol Cepat Pecahan Modal -->
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="shiftStartingCash = 100000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">100rb</button>
                    <button type="button" @click="shiftStartingCash = 150000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">150rb</button>
                    <button type="button" @click="shiftStartingCash = 200000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">200rb</button>
                    <button type="button" @click="shiftStartingCash = 300000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">300rb</button>
                    <button type="button" @click="shiftStartingCash = 500000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">500rb</button>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Tambahan (Opsional)</label>
                    <input 
                        type="text" 
                        x-model="shiftOpenNotes" 
                        placeholder="Contoh: Pecahan uang kecil 2rb, 5rb, 10rb lengkap" 
                        class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" @click="showOpenShiftModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="submitOpenShift()"
                    :disabled="isSubmittingShift || shiftStartingCash < 0"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl transition shadow-md shadow-emerald-600/20 flex items-center space-x-1.5"
                >
                    <i class="fa-solid fa-print"></i>
                    <span>Buka Kasir & Cetak Struk Awal</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: CLOSING SHIFT KASIR (TUTUP KAS & HITUNG SELISIH)      -->
    <!-- ============================================================== -->
    <div 
        x-show="showCloseShiftModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="showCloseShiftModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center font-black">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Closing Shift Kasir</h3>
                        <span class="text-[11px] text-slate-400">Rekap transaksi & pencocokan uang fisik laci</span>
                    </div>
                </div>
                <button type="button" @click="showCloseShiftModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Rekapitulasi Otomatis Shift -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Kas Awal:</span>
                    <strong class="text-slate-900" x-text="formatRupiah(activeShift ? activeShift.starting_cash : 0)"></strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total Penjualan Tunai:</span>
                    <strong class="text-emerald-600" x-text="formatRupiah(shiftSummary.cash_sales)"></strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total Non-Tunai (QRIS & Debit):</span>
                    <strong class="text-blue-600" x-text="formatRupiah(shiftSummary.qris_sales + shiftSummary.transfer_sales)"></strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Jumlah Transaksi:</span>
                    <strong class="text-slate-900" x-text="shiftSummary.trx_count + ' Bill'"></strong>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-black text-slate-900">
                    <span>Target Uang Fisik Laci:</span>
                    <span class="text-amber-600" x-text="formatRupiah(shiftSummary.expected_cash)"></span>
                </div>
            </div>

            <!-- Input Uang Fisik Hasil Hitungan Kasir -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-800 block">Uang Fisik Dihitung Kasir di Laci (Rp) *</label>
                <input 
                    type="number" 
                    x-model.number="shiftActualCash" 
                    class="w-full text-xl font-black p-3 bg-white border-2 border-slate-300 rounded-xl focus:outline-none focus:border-amber-500 text-slate-900"
                    placeholder="Masukkan jumlah uang fisik di laci"
                >

                <!-- Status Selisih Kas -->
                <div class="p-3 rounded-xl border flex items-center justify-between text-xs font-bold"
                     :class="shiftDifference() === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (shiftDifference() > 0 ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-rose-50 border-rose-200 text-rose-800')">
                    <span>Status Selisih Kas:</span>
                    <span class="text-sm font-black" x-text="shiftDifference() === 0 ? 'PAS (Rp 0)' : (shiftDifference() > 0 ? '+ ' + formatRupiah(shiftDifference()) + ' (LEBIH)' : '- ' + formatRupiah(Math.abs(shiftDifference())) + ' (KURANG)')"></span>
                </div>
            </div>

            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Closing (Opsional)</label>
                <input 
                    type="text" 
                    x-model="shiftCloseNotes" 
                    placeholder="Contoh: Sisa kas disetor ke brankas / owner" 
                    class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                >
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" @click="showCloseShiftModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="submitCloseShift()"
                    :disabled="isSubmittingShift || shiftActualCash < 0"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition shadow-md flex items-center space-x-1.5"
                >
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Closing & Cetak Struk Rekap</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 3: PENGATURAN KONTROL PRINTER THERMAL                    -->
    <!-- ============================================================== -->
    <div 
        x-show="showPrinterModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="showPrinterModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-black">
                        <i class="fa-solid fa-print"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Kontrol Printer Struk</h3>
                        <span class="text-[11px] text-slate-400">Atur ukuran kertas thermal & tes cetak</span>
                    </div>
                </div>
                <button type="button" @click="showPrinterModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <!-- Ukuran Kertas -->
                <div>
                    <label class="font-bold text-slate-700 block mb-2">Ukuran Lebar Kertas Printer:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button" 
                            @click="setPrinterPaperSize('58mm')"
                            :class="printerPaperSize === '58mm' ? 'bg-amber-500 text-slate-950 font-black border-amber-400 shadow-xs' : 'bg-slate-50 text-slate-700 font-bold border-slate-200'"
                            class="p-3 border rounded-2xl flex flex-col items-center justify-center gap-1 transition"
                        >
                            <span class="text-sm">58 mm</span>
                            <span class="text-[10px] opacity-80">Mini Portable / Bluetooth</span>
                        </button>
                        <button 
                            type="button" 
                            @click="setPrinterPaperSize('80mm')"
                            :class="printerPaperSize === '80mm' ? 'bg-amber-500 text-slate-950 font-black border-amber-400 shadow-xs' : 'bg-slate-50 text-slate-700 font-bold border-slate-200'"
                            class="p-3 border rounded-2xl flex flex-col items-center justify-center gap-1 transition"
                        >
                            <span class="text-sm">80 mm</span>
                            <span class="text-[10px] opacity-80">Desktop Thermal POS</span>
                        </button>
                    </div>
                </div>

                <!-- Auto-Print Toggle -->
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <div>
                        <strong class="text-slate-900 block">Auto-Print Struk Kasir</strong>
                        <span class="text-[10px] text-slate-400">Otomatis buka dialog cetak saat bayar selesai</span>
                    </div>
                    <button 
                        type="button" 
                        @click="printerAutoPrint = !printerAutoPrint; localStorage.setItem('pos_auto_print', printerAutoPrint ? '1' : '0')"
                        :class="printerAutoPrint ? 'bg-emerald-500' : 'bg-slate-300'"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                    >
                        <span :class="printerAutoPrint ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition duration-200"></span>
                    </button>
                </div>

                <!-- Tombol Test Print -->
                <div>
                    <a 
                        href="{{ route('pos.printer.test') }}" 
                        target="_blank" 
                        class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center justify-center space-x-2 transition shadow-xs"
                    >
                        <i class="fa-solid fa-receipt text-amber-400"></i>
                        <span>🖨️ Cetak Kertas Tes Printer Thermal</span>
                    </a>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 text-right">
                <button type="button" @click="showPrinterModal = false" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition">
                    Selesai & Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 4: PEMBAYARAN KASIR & FITUR SPLIT BILL (PISAH 2 BILL)    -->
    <!-- ============================================================== -->
    <div 
        x-show="showPaymentModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4" @click.away="showPaymentModal = false">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Pembayaran Kasir</h3>
                        <span class="text-[10px] text-slate-400" x-text="orderType === 'dine_in' ? 'Dine In (Meja ' + selectedTableId + ')' : 'Takeaway'"></span>
                    </div>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Tab Mode Pembayaran: 1 Bill Tunggal vs Split Bill (Pisah Bayar) -->
            <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-2xl">
                <button 
                    type="button" 
                    @click="isSplitBill = false" 
                    :class="!isSplitBill ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-slate-500 font-bold'"
                    class="py-2 text-xs rounded-xl transition flex items-center justify-center space-x-1.5"
                >
                    <i class="fa-solid fa-receipt text-[11px]"></i>
                    <span>Bill Tunggal (1 Struk)</span>
                </button>
                <button 
                    type="button" 
                    @click="setupSplitBill()" 
                    :class="isSplitBill ? 'bg-white text-amber-700 font-black shadow-xs' : 'text-slate-500 font-bold'"
                    class="py-2 text-xs rounded-xl transition flex items-center justify-center space-x-1.5"
                >
                    <i class="fa-solid fa-scissors text-[11px]"></i>
                    <span>Split Bill (Pisah 2 Struk)</span>
                </button>
            </div>

            <!-- ============================================== -->
            <!-- MODE A: BILL TUNGGAL (NORMAL)                  -->
            <!-- ============================================== -->
            <div x-show="!isSplitBill" class="space-y-4">
                <!-- Total Amount Banner -->
                <div class="bg-amber-50 border border-amber-200/70 rounded-2xl p-4 text-center">
                    <span class="text-[11px] text-amber-800 font-bold uppercase tracking-wider block">Total yang Harus Dibayar</span>
                    <span class="text-3xl font-black text-amber-900 mt-0.5 block" x-text="formatRupiah(grandTotal())"></span>
                </div>

                <!-- Metode Pembayaran Tabs -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 block">Metode Pembayaran:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button 
                            type="button"
                            @click="paymentMethod = 'cash'" 
                            :class="paymentMethod === 'cash' ? 'border-amber-500 bg-amber-50 text-amber-900 font-black shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                            class="py-2.5 px-3 border-2 rounded-2xl text-xs flex flex-col items-center justify-center space-y-1 transition"
                        >
                            <i class="fa-solid fa-money-bill-wave text-base text-emerald-600"></i>
                            <span>Tunai</span>
                        </button>
                        <button 
                            type="button"
                            @click="paymentMethod = 'qris'" 
                            :class="paymentMethod === 'qris' ? 'border-amber-500 bg-amber-50 text-amber-900 font-black shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                            class="py-2.5 px-3 border-2 rounded-2xl text-xs flex flex-col items-center justify-center space-y-1 transition"
                        >
                            <i class="fa-solid fa-qrcode text-base text-sky-600"></i>
                            <span>QRIS</span>
                        </button>
                        <button 
                            type="button"
                            @click="paymentMethod = 'transfer'" 
                            :class="paymentMethod === 'transfer' ? 'border-amber-500 bg-amber-50 text-amber-900 font-black shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                            class="py-2.5 px-3 border-2 rounded-2xl text-xs flex flex-col items-center justify-center space-y-1 transition"
                        >
                            <i class="fa-solid fa-credit-card text-base text-violet-600"></i>
                            <span>Debit / Transfer</span>
                        </button>
                    </div>
                </div>

                <!-- Input Tunai & Pecahan Cepat -->
                <div x-show="paymentMethod === 'cash'" class="space-y-3">
                    <!-- Tombol Pecahan Cepat persis screenshot -->
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="paidAmount = grandTotal()" class="px-3 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-black shadow-2xs">
                            Pas <span x-text="formatRupiah(grandTotal())"></span>
                        </button>
                        <button type="button" @click="paidAmount = 10000" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">10rb</button>
                        <button type="button" @click="paidAmount = 20000" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">20rb</button>
                        <button type="button" @click="paidAmount = 50000" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">50rb</button>
                        <button type="button" @click="paidAmount = 100000" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">100rb</button>
                        <button type="button" @click="paidAmount = 200000" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700">200rb</button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">INPUT TUNAI (RP)</span>
                            <input 
                                type="number" 
                                x-model.number="paidAmount" 
                                class="w-full text-lg font-black bg-transparent border-none p-0 focus:outline-none text-slate-900 mt-1"
                                placeholder="0"
                            >
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">KEMBALIAN</span>
                            <span class="text-lg font-black" :class="changeAmount() >= 0 ? 'text-emerald-600' : 'text-rose-500'" x-text="changeAmount() >= 0 ? formatRupiah(changeAmount()) : 'Kurang ' + formatRupiah(Math.abs(changeAmount()))"></span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bill Tunggal -->
                <div class="pt-2">
                    <button 
                        @click="submitOrder('paid')" 
                        :disabled="paymentMethod === 'cash' && changeAmount() < 0 || isSubmitting"
                        class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 disabled:opacity-40 text-slate-950 font-black rounded-2xl text-sm transition shadow-lg shadow-amber-500/25 flex items-center justify-center space-x-2"
                    >
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Selesaikan & Cetak Struk</span>
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- MODE B: SPLIT BILL (PISAH BAYAR 2 STRUK)       -->
            <!-- ============================================== -->
            <div x-show="isSplitBill" class="space-y-4">
                <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200 text-xs flex items-center justify-between">
                    <div>
                        <span class="text-slate-600 font-semibold">Total Pesanan:</span>
                        <strong class="text-amber-900 ml-1" x-text="formatRupiah(grandTotal())"></strong>
                    </div>
                    <button type="button" @click="splitEqually()" class="px-2.5 py-1 bg-amber-500 text-slate-950 font-black rounded-lg text-[10px]">
                        Bagi Rata (50 : 50)
                    </button>
                </div>

                <!-- Card Bill 1 -->
                <div class="p-4 rounded-2xl border-2 transition" :class="bill1Paid ? 'bg-emerald-50 border-emerald-300' : 'bg-slate-50 border-slate-200'">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase" :class="bill1Paid ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-white'">
                                Bill #1 (1/2)
                            </span>
                            <span class="text-xs font-bold" x-text="bill1Paid ? '✓ Lunas' : 'Belum Dibayar'"></span>
                        </div>
                        <input 
                            type="number" 
                            x-model.number="bill1Amount" 
                            :disabled="bill1Paid"
                            class="w-32 text-right text-xs font-black p-1.5 bg-white border border-slate-300 rounded-lg focus:outline-none"
                        >
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-200" x-show="!bill1Paid">
                        <select x-model="bill1Method" class="text-xs py-1.5 px-2 bg-white border border-slate-300 rounded-lg font-bold">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Debit/Transfer</option>
                        </select>
                        <button 
                            type="button" 
                            @click="paySplitBill(1)" 
                            :disabled="isSubmitting"
                            class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-xs"
                        >
                            Bayar & Cetak Struk 1
                        </button>
                    </div>

                    <div x-show="bill1Paid" class="text-xs text-emerald-700 font-bold flex items-center justify-between pt-1">
                        <span>Faktur: <span x-text="bill1Invoice"></span></span>
                        <a :href="bill1ReceiptUrl" target="_blank" class="underline text-emerald-800">Cetak Ulang Struk 1</a>
                    </div>
                </div>

                <!-- Card Bill 2 -->
                <div class="p-4 rounded-2xl border-2 transition" :class="bill2Paid ? 'bg-emerald-50 border-emerald-300' : 'bg-slate-50 border-slate-200'">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase" :class="bill2Paid ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-white'">
                                Bill #2 (2/2)
                            </span>
                            <span class="text-xs font-bold" x-text="bill2Paid ? '✓ Lunas' : 'Belum Dibayar'"></span>
                        </div>
                        <input 
                            type="number" 
                            x-model.number="bill2Amount" 
                            :disabled="bill2Paid"
                            class="w-32 text-right text-xs font-black p-1.5 bg-white border border-slate-300 rounded-lg focus:outline-none"
                        >
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-200" x-show="!bill2Paid">
                        <select x-model="bill2Method" class="text-xs py-1.5 px-2 bg-white border border-slate-300 rounded-lg font-bold">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Debit/Transfer</option>
                        </select>
                        <button 
                            type="button" 
                            @click="paySplitBill(2)" 
                            :disabled="isSubmitting"
                            class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-xs"
                        >
                            Bayar & Cetak Struk 2
                        </button>
                    </div>

                    <div x-show="bill2Paid" class="text-xs text-emerald-700 font-bold flex items-center justify-between pt-1">
                        <span>Faktur: <span x-text="bill2Invoice"></span></span>
                        <a :href="bill2ReceiptUrl" target="_blank" class="underline text-emerald-800">Cetak Ulang Struk 2</a>
                    </div>
                </div>

                <!-- Tombol Tutup Split Bill jika keduanya lunas -->
                <div class="pt-2 text-center" x-show="bill1Paid && bill2Paid">
                    <button type="button" @click="finishSplitBill()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-sm">
                        ✓ Kedua Bill Selesai Dibayar! Lanjut Transaksi Baru
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 5: KUPON PROMO                                           -->
    <!-- ============================================================== -->
    <div 
        x-show="showVoucherModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="showVoucherModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Pilih Kupon Diskon</h3>
                <button @click="showVoucherModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-2">
                @foreach($activeCoupons as $c)
                    <div class="p-3 rounded-2xl border border-slate-200 hover:border-amber-400 transition flex items-center justify-between">
                        <div>
                            <span class="font-mono font-black text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-md">{{ $c->code }}</span>
                            <h4 class="font-bold text-slate-900 text-xs mt-1">{{ $c->name }}</h4>
                            <p class="text-[10px] text-slate-400">Potongan {{ $c->type === 'percent' ? $c->value . '%' : 'Rp ' . number_format($c->value) }}</p>
                        </div>
                        <button 
                            type="button" 
                            @click="appliedCoupon = @json($c); showVoucherModal = false"
                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition"
                        >
                            Pakai
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 6: SUKSES TRANSAKSI NORMAL                               -->
    <!-- ============================================================== -->
    <div 
        x-show="showSuccessModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
    >
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border border-slate-100">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 text-3xl flex items-center justify-center mx-auto animate-bounce">
                <i class="fa-solid fa-check"></i>
            </div>

            <div>
                <h3 class="font-extrabold text-slate-900 text-lg">Transaksi Berhasil!</h3>
                <p class="text-xs text-slate-500 mt-1">Nomor Faktur: <strong class="text-slate-800 font-mono" x-text="lastInvoice"></strong></p>
            </div>

            <div class="space-y-2 pt-2">
                <a 
                    :href="lastReceiptUrl" 
                    target="_blank" 
                    class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl flex items-center justify-center space-x-2 transition shadow-xs"
                >
                    <i class="fa-solid fa-receipt"></i>
                    <span>Cetak Struk Kasir</span>
                </a>

                <a 
                    :href="lastKitchenUrl" 
                    target="_blank" 
                    class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl flex items-center justify-center space-x-2 transition"
                >
                    <i class="fa-solid fa-utensils text-slate-500"></i>
                    <span>Cetak Tiket Dapur / Bar</span>
                </a>

                <button 
                    @click="resetAllForNewOrder()" 
                    class="w-full py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition"
                >
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function posApp() {
        return {
            products: @json($products),
            categories: @json($categories),
            availableCoupons: @json($activeCoupons),
            selectedCategory: 'all',
            searchQuery: '',
            
            // Cart State
            cart: [],
            customerName: '',
            orderType: 'dine_in',
            selectedTableId: '',
            
            // Tax & Service Charge
            applyTax: {{ $taxEnabled ? 'true' : 'false' }},
            taxPercent: {{ $taxPercent }},
            applyServiceCharge: {{ $serviceChargeEnabled ? 'true' : 'false' }},
            serviceChargePercent: {{ $serviceChargePercent }},

            // Kupon
            appliedCoupon: null,
            showVoucherModal: false,

            // Payment State
            showPaymentModal: false,
            paymentMethod: 'cash',
            paidAmount: 0,
            isSubmitting: false,

            // Success State
            showSuccessModal: false,
            lastInvoice: '',
            lastReceiptUrl: '',
            lastKitchenUrl: '',

            // ==========================================
            // SHIFT KASIR & CASH DRAWER STATE
            // ==========================================
            activeShift: @json($activeShift),
            showOpenShiftModal: false,
            showCloseShiftModal: false,
            shiftStartingCash: 150000,
            shiftOpenNotes: '',
            shiftActualCash: 0,
            shiftCloseNotes: '',
            isSubmittingShift: false,
            shiftSummary: {
                cash_sales: 0,
                qris_sales: 0,
                transfer_sales: 0,
                total_sales: 0,
                expected_cash: 0,
                trx_count: 0
            },

            // ==========================================
            // PRINTER CONTROL SETTINGS
            // ==========================================
            showPrinterModal: false,
            printerPaperSize: localStorage.getItem('pos_receipt_size') || '58mm',
            printerAutoPrint: localStorage.getItem('pos_auto_print') === '1',

            // ==========================================
            // SPLIT BILL (PISAH 2 STRUK) STATE
            // ==========================================
            isSplitBill: false,
            bill1Amount: 0,
            bill1Method: 'cash',
            bill1Paid: false,
            bill1Invoice: '',
            bill1ReceiptUrl: '',

            bill2Amount: 0,
            bill2Method: 'qris',
            bill2Paid: false,
            bill2Invoice: '',
            bill2ReceiptUrl: '',

            init() {
                // Simpan printer size
                if (!localStorage.getItem('pos_receipt_size')) {
                    localStorage.setItem('pos_receipt_size', '58mm');
                }
            },

            get filteredProducts() {
                return this.products.filter(item => {
                    const matchCategory = this.selectedCategory === 'all' || item.category_id == this.selectedCategory;
                    const query = this.searchQuery.toLowerCase().trim();
                    const matchSearch = query === '' || 
                                        item.name.toLowerCase().includes(query) || 
                                        item.code.toLowerCase().includes(query);
                    return matchCategory && matchSearch;
                });
            },

            addToCart(product) {
                const existingIndex = this.cart.findIndex(i => i.id === product.id);
                if (existingIndex > -1) {
                    this.cart[existingIndex].quantity += 1;
                } else {
                    this.cart.push({
                        id: product.id,
                        code: product.code,
                        name: product.name,
                        price: product.price,
                        quantity: 1,
                        notes: ''
                    });
                }
            },

            increaseQty(index) { this.cart[index].quantity += 1; },
            decreaseQty(index) {
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity -= 1;
                } else {
                    this.removeFromCart(index);
                }
            },
            removeFromCart(index) { this.cart.splice(index, 1); },
            clearCart() {
                if (confirm('Kosongkan keranjang belanja?')) {
                    this.cart = [];
                    this.appliedCoupon = null;
                }
            },
            cartTotalCount() {
                return this.cart.reduce((total, item) => total + item.quantity, 0);
            },
            subtotal() {
                return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            },
            serviceChargeAmount() {
                return this.applyServiceCharge ? Math.round((this.subtotal() * this.serviceChargePercent) / 100) : 0;
            },
            taxAmount() {
                return this.applyTax ? Math.round((this.subtotal() * this.taxPercent) / 100) : 0;
            },
            discountAmount() {
                if (!this.appliedCoupon) return 0;
                const sub = this.subtotal();
                if (sub < this.appliedCoupon.min_order) return 0;
                if (this.appliedCoupon.type === 'percent') {
                    return Math.round((sub * this.appliedCoupon.value) / 100);
                }
                return Math.min(this.appliedCoupon.value, sub);
            },
            grandTotal() {
                const total = this.subtotal() + this.serviceChargeAmount() + this.taxAmount() - this.discountAmount();
                return Math.max(0, total);
            },
            changeAmount() {
                if (this.paymentMethod !== 'cash') return 0;
                return (this.paidAmount || 0) - this.grandTotal();
            },

            openPaymentModal() {
                if (this.orderType === 'dine_in' && !this.selectedTableId) {
                    alert('Harap pilih Meja terlebih dahulu untuk pesanan Makan di Tempat (Dine In)!');
                    return;
                }
                this.paidAmount = this.grandTotal();
                this.isSplitBill = false;
                this.showPaymentModal = true;
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number || 0);
            },

            // ==========================================
            // METODE SPLIT BILL
            // ==========================================
            setupSplitBill() {
                this.isSplitBill = true;
                this.splitEqually();
                this.bill1Paid = false;
                this.bill2Paid = false;
            },

            splitEqually() {
                const total = this.grandTotal();
                const half = Math.floor(total / 2);
                this.bill1Amount = half;
                this.bill2Amount = total - half;
            },

            async paySplitBill(billNumber) {
                if (this.cart.length === 0) return;
                this.isSubmitting = true;

                const amount = (billNumber === 1) ? this.bill1Amount : this.bill2Amount;
                const method = (billNumber === 1) ? this.bill1Method : this.bill2Method;

                const payload = {
                    customer_name: (this.customerName || 'Pelanggan') + ' (Bill ' + billNumber + '/2)',
                    order_type: this.orderType,
                    table_id: this.orderType === 'dine_in' ? this.selectedTableId : null,
                    payment_status: 'paid',
                    payment_method: method,
                    tax_percent: 0,
                    service_charge_percent: 0,
                    discount_amount: 0,
                    paid_amount: amount,
                    split_bill_code: 'BILL-' + billNumber + ' (Bagian ' + billNumber + '/2)',
                    items: this.cart.map(item => ({
                        product_id: item.id,
                        quantity: (billNumber === 1) ? item.quantity : 0, // catat stok di bill 1 saja agar tidak double
                        notes: item.notes || null
                    }))
                };

                try {
                    const response = await fetch("{{ route('pos.order.store', [], false) }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const res = await response.json();
                    if (res.success) {
                        if (billNumber === 1) {
                            this.bill1Paid = true;
                            this.bill1Invoice = res.invoice_number;
                            this.bill1ReceiptUrl = res.receipt_url;
                        } else {
                            this.bill2Paid = true;
                            this.bill2Invoice = res.invoice_number;
                            this.bill2ReceiptUrl = res.receipt_url;
                        }

                        // Buka pop-up cetak struk bill
                        window.open(res.receipt_url, '_blank', 'width=400,height=600');
                    } else {
                        alert(res.message || 'Gagal menyimpan transaksi split bill.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Gagal menghubungi server: ' + (e.message || ''));
                } finally {
                    this.isSubmitting = false;
                }
            },

            finishSplitBill() {
                this.showPaymentModal = false;
                this.resetAllForNewOrder();
                alert('Kedua struk berhasil dicetak dan lunas!');
            },

            // ==========================================
            // METODE TRANSAKSI NORMAL
            // ==========================================
            async submitOrder(paymentStatus) {
                if (this.cart.length === 0) return;
                this.isSubmitting = true;

                const payload = {
                    customer_name: this.customerName || 'Pelanggan',
                    order_type: this.orderType,
                    table_id: this.orderType === 'dine_in' ? this.selectedTableId : null,
                    payment_status: paymentStatus,
                    payment_method: paymentStatus === 'paid' ? this.paymentMethod : null,
                    tax_percent: this.applyTax ? this.taxPercent : 0,
                    service_charge_percent: this.applyServiceCharge ? this.serviceChargePercent : 0,
                    discount_amount: this.discountAmount(),
                    coupon_code: this.appliedCoupon ? this.appliedCoupon.code : null,
                    paid_amount: paymentStatus === 'paid' ? (this.paymentMethod === 'cash' ? this.paidAmount : this.grandTotal()) : 0,
                    items: this.cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity,
                        notes: item.notes || null
                    }))
                };

                try {
                    const response = await fetch("{{ route('pos.order.store', [], false) }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showPaymentModal = false;
                        this.lastInvoice = result.invoice_number;
                        this.lastReceiptUrl = result.receipt_url;
                        this.lastKitchenUrl = result.kitchen_url;
                        this.showSuccessModal = true;

                        // Auto-print jika diaktifkan di setting printer
                        if (this.printerAutoPrint && result.receipt_url) {
                            window.open(result.receipt_url, '_blank', 'width=400,height=600');
                        }
                    } else {
                        alert(result.message || 'Terjadi kesalahan saat memproses transaksi.');
                    }
                } catch (error) {
                    console.error('Error submitting order:', error);
                    alert('Gagal menghubungi server: ' + (error.message || ''));
                } finally {
                    this.isSubmitting = false;
                }
            },

            // ==========================================
            // METODE SHIFT KASIR (BUKA & CLOSING)
            // ==========================================
            async submitOpenShift() {
                this.isSubmittingShift = true;
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch("{{ route('shift.open', [], false) }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            starting_cash: this.shiftStartingCash,
                            notes: this.shiftOpenNotes
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.activeShift = data.shift;
                        this.showOpenShiftModal = false;
                        if (data.print_url) {
                            window.open(data.print_url, '_blank', 'width=400,height=600');
                        }
                    } else {
                        alert(data.message || (data.errors ? Object.values(data.errors).flat().join("\n") : 'Gagal membuka shift kasir.'));
                    }
                } catch (e) {
                    console.error('submitOpenShift error:', e);
                    alert('Gagal membuka shift kasir: ' + (e.message || 'Terjadi kesalahan'));
                } finally {
                    this.isSubmittingShift = false;
                }
            },

            printOpeningReceipt() {
                if (this.activeShift) {
                    const url = "/shifts/" + this.activeShift.id + "/print-open";
                    window.open(url, '_blank', 'width=400,height=600');
                }
            },

            async openCloseShiftModal() {
                try {
                    const res = await fetch("{{ route('shift.current', [], false) }}", {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.has_active_shift) {
                        this.shiftSummary = data.summary;
                        this.shiftActualCash = data.summary.expected_cash; // default ke target laci
                        this.showCloseShiftModal = true;
                    } else {
                        alert('Tidak ada shift kasir yang sedang aktif.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Gagal memuat status shift: ' + (e.message || ''));
                }
            },

            shiftDifference() {
                return (this.shiftActualCash || 0) - (this.shiftSummary.expected_cash || 0);
            },

            async submitCloseShift() {
                if (!this.activeShift) return;
                this.isSubmittingShift = true;
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch("/shifts/" + this.activeShift.id + "/close", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            actual_cash: this.shiftActualCash,
                            notes: this.shiftCloseNotes
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.activeShift = null;
                        this.showCloseShiftModal = false;
                        if (data.print_url) {
                            window.open(data.print_url, '_blank', 'width=400,height=600');
                        }
                        alert(data.message);
                    } else {
                        alert(data.message || (data.errors ? Object.values(data.errors).flat().join("\n") : 'Gagal menutup shift kasir.'));
                    }
                } catch (e) {
                    console.error('submitCloseShift error:', e);
                    alert('Gagal menutup shift kasir: ' + (e.message || 'Terjadi kesalahan'));
                } finally {
                    this.isSubmittingShift = false;
                }
            },

            // ==========================================
            // PRINTER CONTROL
            // ==========================================
            setPrinterPaperSize(size) {
                this.printerPaperSize = size;
                localStorage.setItem('pos_receipt_size', size);
            },

            resetAllForNewOrder() {
                this.cart = [];
                this.customerName = '';
                this.orderType = 'dine_in';
                this.selectedTableId = '';
                this.appliedCoupon = null;
                this.showSuccessModal = false;
                this.isSplitBill = false;
            }
        };
    }
</script>
@endpush
