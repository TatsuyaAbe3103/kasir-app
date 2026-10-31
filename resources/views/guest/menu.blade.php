<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $storeName }} - Menu Meja {{ $table->name }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
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
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen pb-28 antialiased selection:bg-amber-500 selection:text-slate-950" x-data="guestMenuApp()">

    <!-- Header Meja & Cafe (Sticky Glassmorphic) -->
    <header class="sticky top-0 z-30 bg-slate-900/90 backdrop-blur-md border-b border-slate-800/80 px-4 py-3 shadow-lg">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-amber-500/20">
                    <i class="fa-solid fa-mug-hot text-base"></i>
                </div>
                <div>
                    <h1 class="text-sm font-black text-white leading-tight tracking-tight">{{ $storeName }}</h1>
                    <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider block">Pesan dari Meja</span>
                </div>
            </div>

            <!-- Table Number Pill -->
            <div class="bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/40 text-amber-300 px-3.5 py-1.5 rounded-2xl flex items-center space-x-1.5 shadow-sm">
                <i class="fa-solid fa-chair text-xs text-amber-400"></i>
                <span class="text-xs font-black tracking-wide">{{ $table->name }}</span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto px-4 pt-4 space-y-4">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-800/60 p-4 rounded-3xl border border-slate-700/60 shadow-md relative overflow-hidden">
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400">Digital Self-Order</span>
                <h2 class="text-base font-black text-white">Duduk Santai, Pesan Langsung ☕</h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Pilih menu favoritmu, tambahkan catatan khusus, dan pesananmu akan langsung diolah oleh barista kami!
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 w-28 h-28 rounded-full bg-amber-500/10 blur-xl"></div>
        </div>

        <!-- Search Bar -->
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari kopi, makanan, dessert..." 
                class="w-full bg-slate-800/90 border border-slate-700/80 rounded-2xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-amber-500 transition shadow-inner"
            >
            <button 
                x-show="searchQuery.length > 0" 
                @click="searchQuery = ''" 
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
            >
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Category Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <button 
                @click="selectedCategory = 'all'" 
                :class="selectedCategory === 'all' ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'bg-slate-800 text-slate-300 font-bold hover:bg-slate-700'"
                class="px-4 py-2 rounded-2xl text-xs transition whitespace-nowrap flex items-center space-x-1.5"
            >
                <span>✨ Semua</span>
            </button>
            @foreach($categories as $cat)
                <button 
                    @click="selectedCategory = '{{ $cat->id }}'" 
                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' : 'bg-slate-800 text-slate-300 font-bold hover:bg-slate-700'"
                    class="px-4 py-2 rounded-2xl text-xs transition whitespace-nowrap flex items-center space-x-1.5"
                >
                    <span>{{ $cat->icon }} {{ $cat->name }}</span>
                </button>
            @endforeach
        </div>

        <!-- Menu Item Cards -->
        <div class="space-y-3 pt-1">
            <template x-for="item in filteredProducts" :key="item.id">
                <div class="bg-slate-800/90 border border-slate-700/60 rounded-3xl p-3.5 flex gap-3.5 items-center shadow-sm hover:border-amber-500/50 transition">
                    
                    <!-- Food Image -->
                    <div class="relative w-24 h-24 rounded-2xl overflow-hidden bg-slate-900 flex-shrink-0">
                        <img 
                            :src="item.image || 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=300'" 
                            :alt="item.name" 
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0 flex flex-col justify-between h-24 py-0.5">
                        <div>
                            <h3 class="font-extrabold text-sm text-white line-clamp-1" x-text="item.name"></h3>
                            <p class="text-[11px] text-slate-400 line-clamp-2 mt-0.5" x-text="item.description || 'Menu lezat pilihan resto'"></p>
                        </div>

                        <!-- Price & Add Action -->
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-black text-amber-400" x-text="formatRupiah(item.price)"></span>
                            
                            <!-- Add to Cart / Stepper -->
                            <div>
                                <template x-if="!isInCart(item.id)">
                                    <button 
                                        @click="addToCart(item)" 
                                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 active:scale-95 text-slate-950 text-xs font-black rounded-xl transition flex items-center space-x-1 shadow-sm"
                                    >
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>Tambah</span>
                                    </button>
                                </template>

                                <template x-if="isInCart(item.id)">
                                    <div class="flex items-center space-x-1.5 bg-slate-900 border border-slate-700 rounded-xl p-0.5">
                                        <button 
                                            @click="decreaseQty(item.id)" 
                                            class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center text-[10px]"
                                        >
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <span class="text-xs font-black px-1.5 text-amber-400" x-text="getCartQty(item.id)"></span>
                                        <button 
                                            @click="addToCart(item)" 
                                            class="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-[10px]"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </main>

    <!-- FLOATING CART BAR -->
    <div 
        x-show="cartTotalCount() > 0" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-4 inset-x-4 max-w-md mx-auto z-40"
        style="display: none;"
    >
        <button 
            @click="showCheckoutSheet = true" 
            class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 p-3.5 rounded-3xl shadow-2xl shadow-amber-500/30 flex items-center justify-between transition active:scale-98"
        >
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-2xl bg-slate-950/20 flex items-center justify-center font-black text-xs">
                    <span x-text="cartTotalCount()"></span>
                </div>
                <div class="text-left">
                    <span class="text-[10px] font-bold uppercase tracking-wider block opacity-80">Keranjang Pesanan</span>
                    <span class="text-sm font-black" x-text="formatRupiah(grandTotal())"></span>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 font-black text-xs pr-1">
                <span>Lihat Pesanan</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </div>
        </button>
    </div>

    <!-- BOTTOM SHEET: REVIEW PESANAN & CHECKOUT -->
    <div 
        x-show="showCheckoutSheet" 
        @click.self="showCheckoutSheet = false"
        class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/80 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            class="bg-slate-900 border-t border-slate-700/80 rounded-t-3xl max-w-md w-full max-h-[88vh] flex flex-col p-5 shadow-2xl relative"
        >
            <!-- Header Sheet -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="font-black text-base text-white">Pesanan Anda</h3>
                    <span class="text-xs text-amber-400 font-bold">Meja: {{ $table->name }}</span>
                </div>
                <button @click="showCheckoutSheet = false" class="text-slate-400 hover:text-white text-lg p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Scrollable Items List -->
            <div class="flex-1 overflow-y-auto py-3 space-y-3 pr-1">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-slate-800/80 p-3 rounded-2xl border border-slate-700/60 space-y-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-white" x-text="item.name"></h4>
                                <span class="text-[11px] text-amber-400 font-semibold" x-text="formatRupiah(item.price)"></span>
                            </div>
                            <span class="text-xs font-black text-white" x-text="formatRupiah(item.price * item.quantity)"></span>
                        </div>

                        <!-- Stepper & Notes -->
                        <div class="flex items-center justify-between pt-1 border-t border-slate-700/40">
                            <input 
                                type="text" 
                                x-model="item.notes" 
                                placeholder="+ Catatan (misal: less sugar / no ice)" 
                                class="flex-1 text-[11px] bg-slate-900/90 border border-slate-700 rounded-xl px-2.5 py-1 text-slate-300 placeholder-slate-500 mr-2 focus:outline-none focus:border-amber-500"
                            >

                            <div class="flex items-center space-x-1.5 bg-slate-900 border border-slate-700 rounded-xl p-0.5">
                                <button @click="decreaseQty(item.id)" class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <span class="text-xs font-black px-1.5 text-amber-400" x-text="item.quantity"></span>
                                <button @click="increaseQty(index)" class="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Input Nama Tamu -->
                <div class="pt-2">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1">Nama Pemesan / Panggilan *</label>
                    <input 
                        type="text" 
                        x-model="customerName" 
                        placeholder="Contoh: Kak Dimas" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-amber-500"
                    >
                </div>

                <!-- BAGIAN KUPON & PROMO CERDAS -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Voucher & Promo</label>
                        <button 
                            type="button"
                            @click="showVoucherModal = true"
                            class="text-xs text-amber-400 font-bold hover:text-amber-300 flex items-center space-x-1"
                        >
                            <i class="fa-solid fa-ticket"></i>
                            <span>Lihat Semua Promo (<span x-text="availableCoupons.length"></span>)</span>
                        </button>
                    </div>

                    <!-- Jika ada kupon terpasang -->
                    <template x-if="appliedCoupon">
                        <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 p-2.5 rounded-2xl text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                                    <i class="fa-solid fa-tag"></i>
                                </span>
                                <div>
                                    <span class="font-mono font-black" x-text="appliedCoupon.code"></span>
                                    <span class="block text-[10px] text-emerald-400 font-medium" x-text="appliedCoupon.name + ' (Hemat ' + formatRupiah(discountAmount()) + ')'"></span>
                                </div>
                            </div>
                            <button @click="appliedCoupon = null" class="text-rose-400 hover:text-rose-300 font-bold text-xs px-2 py-1 bg-slate-900 rounded-lg">
                                Ganti
                            </button>
                        </div>
                    </template>

                    <!-- Tombol Cepat Buka Daftar Voucher jika belum ada yg dipasang -->
                    <template x-if="!appliedCoupon">
                        <button 
                            type="button" 
                            @click="showVoucherModal = true"
                            class="w-full py-2.5 px-3 bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 rounded-2xl text-left flex items-center justify-between text-xs transition"
                        >
                            <div class="flex items-center space-x-2 text-slate-300">
                                <i class="fa-solid fa-ticket text-amber-400"></i>
                                <span>Gunakan Voucher Promo Resto</span>
                            </div>
                            <span class="text-amber-400 font-bold text-[11px] flex items-center space-x-1">
                                <span>Pilih</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </span>
                        </button>
                    </template>
                </div>

                <!-- Perhitungan Biaya -->
                <div class="bg-slate-800/60 p-3.5 rounded-2xl border border-slate-700/40 space-y-1 text-xs text-slate-400 mt-2">
                    <div class="flex justify-between">
                        <span>Subtotal Menu</span>
                        <span class="font-bold text-white" x-text="formatRupiah(subtotal())"></span>
                    </div>

                    @if($serviceChargeEnabled)
                        <div class="flex justify-between">
                            <span>Service Charge ({{ $serviceChargePercent }}%)</span>
                            <span class="font-semibold text-slate-300" x-text="formatRupiah(serviceChargeAmount())"></span>
                        </div>
                    @endif

                    @if($taxEnabled)
                        <div class="flex justify-between">
                            <span>Pajak Restoran (PB1 {{ $taxPercent }}%)</span>
                            <span class="font-semibold text-slate-300" x-text="formatRupiah(taxAmount())"></span>
                        </div>
                    @endif

                    <div x-show="discountAmount() > 0" class="flex justify-between text-emerald-400 font-bold pt-0.5">
                        <span class="flex items-center space-x-1">
                            <i class="fa-solid fa-tag text-[10px]"></i>
                            <span>Diskon Voucher</span>
                        </span>
                        <span x-text="'-' + formatRupiah(discountAmount())"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-700 flex justify-between items-center text-sm font-black text-white">
                        <span>Total Pembayaran</span>
                        <span class="text-base text-amber-400 font-black" x-text="formatRupiah(grandTotal())"></span>
                    </div>
                </div>

                <!-- Pilihan Cara Bayar di Meja -->
                <div class="space-y-1.5 mt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-credit-card text-amber-400"></i> Pilihan Pembayaran:
                        </span>
                        <span class="text-[10px] text-amber-400 font-bold" x-show="paymentMethod !== 'cash'">
                            ⚡ Langsung Dimasak
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <!-- QRIS -->
                        <button 
                            type="button" 
                            @click="paymentMethod = 'qris'" 
                            class="p-2.5 rounded-2xl border text-center transition flex flex-col items-center justify-center space-y-1"
                            :class="paymentMethod === 'qris' ? 'bg-amber-500/20 border-amber-500 text-amber-300 shadow-md shadow-amber-500/10' : 'bg-slate-800/80 border-slate-700/80 text-slate-400 hover:text-white'"
                        >
                            <i class="fa-solid fa-qrcode text-base"></i>
                            <span class="text-[11px] font-black leading-tight">QRIS</span>
                            <span class="text-[9px] text-emerald-400 font-bold">Instan</span>
                        </button>

                        <!-- Virtual Account -->
                        <button 
                            type="button" 
                            @click="paymentMethod = 'va'" 
                            class="p-2.5 rounded-2xl border text-center transition flex flex-col items-center justify-center space-y-1"
                            :class="paymentMethod === 'va' ? 'bg-amber-500/20 border-amber-500 text-amber-300 shadow-md shadow-amber-500/10' : 'bg-slate-800/80 border-slate-700/80 text-slate-400 hover:text-white'"
                        >
                            <i class="fa-solid fa-building-columns text-base"></i>
                            <span class="text-[11px] font-black leading-tight">Virtual Acc</span>
                            <span class="text-[9px] text-emerald-400 font-bold">BCA/Mandiri</span>
                        </button>

                        <!-- Cash Kasir -->
                        <button 
                            type="button" 
                            @click="paymentMethod = 'cash'" 
                            class="p-2.5 rounded-2xl border text-center transition flex flex-col items-center justify-center space-y-1"
                            :class="paymentMethod === 'cash' ? 'bg-amber-500/20 border-amber-500 text-amber-300 shadow-md shadow-amber-500/10' : 'bg-slate-800/80 border-slate-700/80 text-slate-400 hover:text-white'"
                        >
                            <i class="fa-solid fa-cash-register text-base"></i>
                            <span class="text-[11px] font-black leading-tight">Ke Kasir</span>
                            <span class="text-[9px] text-slate-500 font-semibold">Bayar Nanti</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-slate-800">
                <button 
                    @click="submitGuestOrder()" 
                    :disabled="isSubmitting || cart.length === 0"
                    class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 disabled:opacity-40 text-slate-950 font-black text-sm rounded-2xl transition shadow-xl shadow-amber-500/20 flex items-center justify-center space-x-2"
                >
                    <i :class="paymentMethod === 'cash' ? 'fa-solid fa-paper-plane' : 'fa-solid fa-bolt'"></i>
                    <span x-text="isSubmitting ? 'Memproses Pesanan...' : (paymentMethod === 'cash' ? 'Kirim Pesanan (Bayar Nanti di Kasir)' : 'Lanjut Bayar di Meja (' + (paymentMethod === 'qris' ? 'QRIS' : 'VA') + ')')"></span>
                </button>
                <p class="text-[10px] text-center text-slate-400 mt-2">
                    <span x-show="paymentMethod !== 'cash'">⚡ Bayar via QRIS/VA di meja, setelah bayar pesanan <strong class="text-amber-400">langsung dimasak</strong> oleh Dapur!</span>
                    <span x-show="paymentMethod === 'cash'">* Pembayaran dapat diselesaikan di meja kasir setelah Anda selesai menikmati hidangan.</span>
                </p>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL PILIH VOUCHER PROMO (DENGAN LOGIKA SESUAI NILAI PESANAN) -->
    <!-- ============================================================== -->
    <div 
        x-show="showVoucherModal" 
        @click.self="showVoucherModal = false"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-sm w-full p-5 shadow-2xl flex flex-col max-h-[80vh] relative"
        >
            <!-- Header Modal Voucher -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-white">Voucher Promo Tersedia</h3>
                        <span class="text-[10px] text-slate-400">Pilih voucher yang sesuai pesananmu</span>
                    </div>
                </div>
                <button @click="showVoucherModal = false" class="text-slate-400 hover:text-white text-base">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Status Subtotal Saat Ini -->
            <div class="bg-slate-800/80 p-3 rounded-2xl border border-slate-700/60 my-3 flex items-center justify-between text-xs">
                <span class="text-slate-400">Subtotal Pesananmu:</span>
                <span class="font-black text-amber-400 text-sm" x-text="formatRupiah(subtotal())"></span>
            </div>

            <!-- List Voucher -->
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                <template x-for="v in availableCoupons" :key="v.id">
                    @php // Tampilan Card Voucher @endphp
                    <div 
                        class="p-3.5 rounded-2xl border transition duration-200"
                        :class="isEligible(v) ? 'bg-slate-800/90 border-amber-500/40 shadow-sm' : 'bg-slate-900/60 border-slate-800 opacity-60'"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="font-mono font-black text-xs px-2 py-0.5 rounded-md" :class="isEligible(v) ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-800 text-slate-500'" x-text="v.code"></span>
                                <h4 class="font-bold text-white text-xs mt-1.5" x-text="v.name"></h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Potongan <strong class="text-slate-200" x-text="v.type === 'percent' ? v.value + '%' : formatRupiah(v.value)"></strong>
                                    <span x-show="v.min_order > 0"> (Min. belanja <span x-text="formatRupiah(v.min_order)"></span>)</span>
                                </p>
                            </div>

                            <!-- Action Button / Lock Status -->
                            <div class="text-right">
                                <template x-if="isEligible(v)">
                                    <button 
                                        @click="selectVoucher(v)" 
                                        class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs transition active:scale-95 shadow-sm"
                                    >
                                        Gunakan
                                    </button>
                                </template>

                                <template x-if="!isEligible(v)">
                                    <div class="text-right">
                                        <span class="text-[9px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-1 rounded-lg block">
                                            Kurang <span x-text="formatRupiah(v.min_order - subtotal())"></span>
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input Manual Kode Kupon Tambahan -->
            <div class="pt-3 border-t border-slate-800 mt-2">
                <div class="flex gap-1.5">
                    <input 
                        type="text" 
                        x-model="manualCode" 
                        placeholder="Punya kode rahasia lain?" 
                        class="flex-1 uppercase font-mono text-xs py-2 px-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-amber-500"
                    >
                    <button 
                        @click="applyManualCode()" 
                        class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition"
                    >
                        Cek
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine App Script -->
    <script>
        function guestMenuApp() {
            return {
                products: @json($products),
                availableCoupons: @json($activeCoupons),
                selectedCategory: 'all',
                searchQuery: '',
                cart: [],
                customerName: '',
                paymentMethod: 'qris',
                appliedCoupon: null,
                manualCode: '',
                showCheckoutSheet: false,
                showVoucherModal: false,
                isSubmitting: false,

                // Biaya Restoran
                serviceChargePercent: {{ $serviceChargeEnabled ? $serviceChargePercent : 0 }},
                taxPercent: {{ $taxEnabled ? $taxPercent : 0 }},

                init() {
                    // WATCHER ANTI-ABUSE: Jika item dikurangi/dihapus sehingga subtotal turun di bawah min_order, kupon otomatis dicabut!
                    this.$watch('cart', () => {
                        if (this.appliedCoupon && this.subtotal() < this.appliedCoupon.min_order) {
                            alert(`Kupon ${this.appliedCoupon.code} dicabut otomatis karena nilai pesanan Anda turun di bawah syarat minimum (${this.formatRupiah(this.appliedCoupon.min_order)})`);
                            this.appliedCoupon = null;
                        }
                    });
                },

                get filteredProducts() {
                    return this.products.filter(item => {
                        const matchCat = this.selectedCategory === 'all' || item.category_id == this.selectedCategory;
                        const q = this.searchQuery.toLowerCase().trim();
                        const matchSearch = q === '' || item.name.toLowerCase().includes(q) || item.code.toLowerCase().includes(q);
                        return matchCat && matchSearch;
                    });
                },

                isInCart(productId) {
                    return this.cart.some(i => i.id === productId);
                },

                getCartQty(productId) {
                    const found = this.cart.find(i => i.id === productId);
                    return found ? found.quantity : 0;
                },

                addToCart(product) {
                    const idx = this.cart.findIndex(i => i.id === product.id);
                    if (idx > -1) {
                        this.cart[idx].quantity += 1;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            quantity: 1,
                            notes: ''
                        });
                    }
                },

                increaseQty(index) {
                    this.cart[index].quantity += 1;
                },

                decreaseQty(productId) {
                    const idx = this.cart.findIndex(i => i.id === productId);
                    if (idx > -1) {
                        if (this.cart[idx].quantity > 1) {
                            this.cart[idx].quantity -= 1;
                        } else {
                            this.cart.splice(idx, 1);
                        }
                    }
                },

                cartTotalCount() {
                    return this.cart.reduce((total, i) => total + i.quantity, 0);
                },

                subtotal() {
                    return this.cart.reduce((total, i) => total + (i.price * i.quantity), 0);
                },

                serviceChargeAmount() {
                    return Math.round((this.subtotal() * this.serviceChargePercent) / 100);
                },

                taxAmount() {
                    return Math.round((this.subtotal() * this.taxPercent) / 100);
                },

                isEligible(voucher) {
                    return this.subtotal() >= (voucher.min_order || 0);
                },

                selectVoucher(voucher) {
                    if (!this.isEligible(voucher)) {
                        alert(`Syarat minimal belanja untuk voucher ${voucher.code} adalah ${this.formatRupiah(voucher.min_order)}`);
                        return;
                    }
                    this.appliedCoupon = voucher;
                    this.showVoucherModal = false;
                    this.showCheckoutSheet = true;
                },

                applyManualCode() {
                    const code = this.manualCode.trim().toUpperCase();
                    if (!code) return;

                    const found = this.availableCoupons.find(c => c.code.toUpperCase() === code);
                    if (!found) {
                        alert('Kode voucher tidak ditemukan atau sudah tidak aktif.');
                        return;
                    }

                    if (!this.isEligible(found)) {
                        alert(`Syarat minimal belanja untuk voucher ${found.code} adalah ${this.formatRupiah(found.min_order)}. Tambah menu senilai ${this.formatRupiah(found.min_order - this.subtotal())} lagi.`);
                        return;
                    }

                    this.appliedCoupon = found;
                    this.manualCode = '';
                    this.showVoucherModal = false;
                    this.showCheckoutSheet = true;
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
                    return Math.max(0, this.subtotal() + this.serviceChargeAmount() + this.taxAmount() - this.discountAmount());
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(number);
                },

                async submitGuestOrder() {
                    if (this.cart.length === 0) return;

                    if (!this.customerName.trim()) {
                        alert('Harap isi Nama Anda terlebih dahulu agar barista dapat memanggil nama Anda!');
                        return;
                    }

                    this.isSubmitting = true;

                    const payload = {
                        customer_name: this.customerName,
                        payment_choice: this.paymentMethod,
                        coupon_code: this.appliedCoupon ? this.appliedCoupon.code : null,
                        items: this.cart.map(i => ({
                            product_id: i.id,
                            quantity: i.quantity,
                            notes: i.notes || null
                        }))
                    };

                    try {
                        const response = await fetch("{{ route('guest.order', $table->id) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.href = result.redirect_url;
                        } else {
                            alert(result.message || 'Terjadi kesalahan saat memproses pesanan.');
                        }
                    } catch (e) {
                        console.error('Error:', e);
                        alert('Gagal mengirim pesanan. Silakan periksa koneksi internet Anda.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>

</body>
</html>
