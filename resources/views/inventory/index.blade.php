@extends('layouts.app')

@section('title', 'Audit Stok & Inventaris - Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ 
    showAdjustModal: false, 
    modalType: 'in', 
    modalTitle: 'Catat Stok Masuk (+)',
    selectedProduct: '', 
    selectedProductName: '',
    currentStock: 0,
    openAdjust(type, prodId = '', prodName = '', stock = 0) {
        this.modalType = type;
        this.selectedProduct = prodId;
        this.selectedProductName = prodName;
        this.currentStock = stock;
        if(type === 'in') this.modalTitle = 'Catat Penambahan Stok Masuk (+)';
        else if(type === 'out') this.modalTitle = 'Catat Pengurangan Stok Keluar (-)';
        else this.modalTitle = 'Stock Opname (Penyesuaian Fisik Gudang)';
        this.showAdjustModal = true;
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-sky-500/10 text-sky-600 font-bold text-xs uppercase tracking-wider">Audit & Gudang</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Stok & Audit Barang</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pantau stok menu cafe, catat barang masuk/keluar, dan lakukan opname fisik secara berkala.</p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Tambah Stok Masuk (+) -->
            <button 
                type="button" 
                @click="openAdjust('in')" 
                class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm"
            >
                <i class="fa-solid fa-arrow-down-long"></i>
                <span>Stok Masuk (+)</span>
            </button>

            <!-- Tombol Stok Keluar / Rusak (-) -->
            <button 
                type="button" 
                @click="openAdjust('out')" 
                class="px-3.5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm"
            >
                <i class="fa-solid fa-arrow-up-long"></i>
                <span>Stok Keluar / Rusak (-)</span>
            </button>

            <!-- Tombol Opname Fisik -->
            <button 
                type="button" 
                @click="openAdjust('adjustment')" 
                class="px-3.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm"
            >
                <i class="fa-solid fa-scale-balanced"></i>
                <span>Stock Opname</span>
            </button>

            <!-- Link ke Laporan Barang Masuk / Keluar -->
            <a 
                href="{{ route('inventory.logs') }}" 
                class="px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm"
            >
                <i class="fa-solid fa-right-left text-amber-400"></i>
                <span>Laporan Masuk / Keluar</span>
            </a>

            <!-- Link ke Histori Order Dihapus / Void -->
            <a 
                href="{{ route('inventory.deleted_orders') }}" 
                class="px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm border border-rose-200"
            >
                <i class="fa-solid fa-trash-can"></i>
                <span>Order Dihapus</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Menu -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Menu</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalProducts }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">{{ $totalStockUnits }} Total Porsi/Pcs</span>
            </div>
        </div>

        <!-- Stok Aman -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Stok Aman (>10)</span>
                <span class="text-2xl font-black text-emerald-600">{{ $totalProducts - $lowStockCount - $outOfStockCount }}</span>
                <span class="text-[10px] text-emerald-600 block font-semibold">Siap Jual</span>
            </div>
        </div>

        <!-- Stok Menipis -->
        <a href="{{ route('inventory.index', ['filter' => 'low']) }}" class="bg-white p-5 rounded-3xl border border-amber-200 shadow-sm flex items-center space-x-3.5 hover:bg-amber-50/30 transition group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-[10px] text-amber-700 font-bold uppercase tracking-wider block">Stok Menipis (<=10)</span>
                <span class="text-2xl font-black text-amber-600">{{ $lowStockCount }}</span>
                <span class="text-[10px] text-amber-600 block font-semibold">Perlu Restock</span>
            </div>
        </a>

        <!-- Stok Habis -->
        <a href="{{ route('inventory.index', ['filter' => 'out']) }}" class="bg-white p-5 rounded-3xl border border-rose-200 shadow-sm flex items-center space-x-3.5 hover:bg-rose-50/30 transition group">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <span class="text-[10px] text-rose-700 font-bold uppercase tracking-wider block">Stok Habis / Kosong</span>
                <span class="text-2xl font-black text-rose-600">{{ $outOfStockCount }}</span>
                <span class="text-[10px] text-rose-600 block font-semibold">Tidak Tersedia</span>
            </div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-4 shadow-sm space-y-3">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <!-- Search Input -->
            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari menu atau kode SKU..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"
                >
            </div>

            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 flex-wrap w-full sm:w-auto">
                <a href="{{ route('inventory.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !request('filter') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('inventory.index', ['filter' => 'ready']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('filter') === 'ready' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Ready
                </a>
                <a href="{{ route('inventory.index', ['filter' => 'low']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('filter') === 'low' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Menipis (≤10)
                </a>
                <a href="{{ route('inventory.index', ['filter' => 'out']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('filter') === 'out' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Habis (0)
                </a>
            </div>
        </form>
    </div>

    <!-- Main Stock Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4">Menu & Kategori</th>
                        <th class="py-3.5 px-4">Harga Jual</th>
                        <th class="py-3.5 px-4 text-center">Status Stok</th>
                        <th class="py-3.5 px-4 text-center">Sisa Stok</th>
                        <th class="py-3.5 px-4 text-center">Aksi Cepat Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Menu Info -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                        @if($p->image)
                                            <img src="{{ Str::startsWith($p->image, 'http') ? $p->image : asset($p->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fa-solid fa-mug-hot"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block leading-tight text-xs">{{ $p->name }}</span>
                                        <div class="flex items-center space-x-2 mt-0.5">
                                            <span class="font-mono text-[10px] text-slate-400">{{ $p->code }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-[10px] text-amber-600 font-semibold">{{ $p->category->name ?? 'Menu' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </td>

                            <!-- Availability Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($p->is_available && $p->stock > 0)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full text-[10px] border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Ready
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 font-bold px-2.5 py-1 rounded-full text-[10px] border border-rose-200 animate-pulse">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Habis (Sold Out)
                                    </span>
                                @endif
                            </td>

                            <!-- Sisa Stok -->
                            <td class="py-3.5 px-4 text-center">
                                @if($p->stock <= 0)
                                    <span class="inline-block px-3 py-1 rounded-xl bg-rose-100 text-rose-800 font-black text-sm">
                                        0 Porsi
                                    </span>
                                @elseif($p->stock <= 10)
                                    <span class="inline-block px-3 py-1 rounded-xl bg-amber-100 text-amber-800 font-black text-sm animate-pulse">
                                        {{ $p->stock }} Porsi
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-xl bg-slate-100 text-slate-800 font-black text-sm">
                                        {{ $p->stock }} Porsi
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Tambah Stok (+) -->
                                    <button 
                                        type="button" 
                                        @click="openAdjust('in', '{{ $p->id }}', '{{ addslashes($p->name) }}', {{ $p->stock }})"
                                        class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition flex items-center space-x-1" 
                                        title="Tambah Stok Masuk"
                                    >
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>Masuk</span>
                                    </button>

                                    <!-- Kurangi Stok (-) -->
                                    <button 
                                        type="button" 
                                        @click="openAdjust('out', '{{ $p->id }}', '{{ addslashes($p->name) }}', {{ $p->stock }})"
                                        class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition flex items-center space-x-1" 
                                        title="Kurangi Stok / Basi / Rusak"
                                    >
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                        <span>Keluar</span>
                                    </button>

                                    <!-- Opname Langsung -->
                                    <button 
                                        type="button" 
                                        @click="openAdjust('adjustment', '{{ $p->id }}', '{{ addslashes($p->name) }}', {{ $p->stock }})"
                                        class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold transition flex items-center space-x-1" 
                                        title="Stock Opname Fisik"
                                    >
                                        <i class="fa-solid fa-scale-balanced text-[10px]"></i>
                                        <span>Opname</span>
                                    </button>

                                    <!-- Toggle Habis / Tersedia -->
                                    <form method="POST" action="{{ route('inventory.toggle', $p->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="p-1.5 rounded-lg text-xs transition {{ $p->is_available ? 'bg-slate-100 hover:bg-slate-200 text-slate-600' : 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold' }}" 
                                            title="{{ $p->is_available ? 'Tandai Kosong (Sold Out)' : 'Tandai Tersedia (Ready)' }}"
                                        >
                                            <i class="fa-solid {{ $p->is_available ? 'fa-ban' : 'fa-check' }} text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300 block"></i>
                                Tidak ada data menu yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Audit Logs Preview -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500 text-base"></i>
                <h3 class="font-extrabold text-sm text-slate-900">10 Catatan Audit Stok Terakhir</h3>
            </div>
            <a href="{{ route('inventory.logs') }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center gap-1">
                <span>Buka Laporan Masuk / Keluar</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentAudits as $log)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-start space-x-3">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5 {{ $log->type === 'in' ? 'bg-emerald-100 text-emerald-700' : ($log->type === 'out' ? 'bg-rose-100 text-rose-700' : ($log->type === 'adjustment' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700')) }}">
                            @if($log->type === 'in') <i class="fa-solid fa-plus"></i>
                            @elseif($log->type === 'out') <i class="fa-solid fa-minus"></i>
                            @elseif($log->type === 'adjustment') <i class="fa-solid fa-scale-balanced"></i>
                            @else <i class="fa-solid fa-cart-shopping"></i> @endif
                        </span>
                        <div>
                            <div class="flex items-center space-x-2">
                                <strong class="text-slate-900">{{ $log->product->name ?? 'Produk Dihapus' }}</strong>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d M, H:i') }}</span>
                            </div>
                            <span class="text-[11px] text-slate-600 block mt-0.5">{{ $log->reason }} @if($log->notes) - <em>{{ $log->notes }}</em> @endif</span>
                            <span class="text-[10px] text-slate-400">Oleh: <strong class="text-slate-600">{{ $log->user_name ?? ($log->user->name ?? 'Sistem') }}</strong></span>
                        </div>
                    </div>

                    <div class="text-right sm:text-right self-end sm:self-auto font-mono">
                        <span class="font-black text-sm {{ $log->quantity > 0 ? 'text-emerald-600' : ($log->quantity < 0 ? 'text-rose-600' : 'text-slate-700') }}">
                            {{ $log->quantity > 0 ? '+' . $log->quantity : $log->quantity }} Porsi
                        </span>
                        <div class="text-[10px] text-slate-400">
                            Stok: {{ $log->stock_before }} &rarr; <strong class="text-slate-700">{{ $log->stock_after }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs">
                    Belum ada riwayat audit stok tercatat.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL PENYESUAIAN STOK (IN, OUT, OPNAME)                       -->
    <!-- ============================================================== -->
    <div 
        x-show="showAdjustModal" 
        @click.self="showAdjustModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm" :class="modalType === 'in' ? 'bg-emerald-100 text-emerald-700' : (modalType === 'out' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')">
                        <i :class="modalType === 'in' ? 'fa-solid fa-arrow-down-long' : (modalType === 'out' ? 'fa-solid fa-arrow-up-long' : 'fa-solid fa-scale-balanced')"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900" x-text="modalTitle"></h3>
                        <p class="text-[11px] text-slate-500">Pencatatan langsung terekam ke log audit</p>
                    </div>
                </div>
                <button @click="showAdjustModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('inventory.adjust') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" :value="modalType">

                <!-- Pilih Menu / Produk -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Pilih Menu / Produk *</label>
                    <select 
                        name="product_id" 
                        x-model="selectedProduct" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                        <option value="">-- Pilih Menu --</option>
                        @foreach(App\Models\Product::orderBy('name')->get() as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} (Sisa Stok: {{ $item->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah / Quantity -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">
                        <span x-show="modalType === 'in'">Jumlah Barang Masuk (Pcs/Porsi) *</span>
                        <span x-show="modalType === 'out'">Jumlah Barang Keluar / Rusak (Pcs/Porsi) *</span>
                        <span x-show="modalType === 'adjustment'">Jumlah Stok Fisik Riil Hasil Hitung Opname *</span>
                    </label>
                    <input 
                        type="number" 
                        name="quantity" 
                        min="0" 
                        required 
                        placeholder="Contoh: 20" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono font-bold text-sm"
                    >
                    <p class="text-[10px] text-slate-400 mt-1" x-show="modalType === 'adjustment'">
                        * Pada Stock Opname, masukkan angka fisik akhir riil yang ada di gudang/etalase.
                    </p>
                </div>

                <!-- Alasan / Kategori Mutasi -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Alasan / Jenis Audit *</label>
                    <select 
                        name="reason" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                        <template x-if="modalType === 'in'">
                            <optgroup label="Alasan Stok Masuk">
                                <option value="Pembelian Bahan Baku / Restock Supplier">Pembelian Bahan Baku / Restock Supplier</option>
                                <option value="Koreksi Penambahan Stok">Koreksi Penambahan Stok</option>
                                <option value="Retur Pelanggan / Dibatalkan">Retur Pelanggan / Dibatalkan</option>
                            </optgroup>
                        </template>

                        <template x-if="modalType === 'out'">
                            <optgroup label="Alasan Stok Keluar">
                                <option value="Bahan Basi / Kadaluarsa (Expired)">Bahan Basi / Kadaluarsa (Expired)</option>
                                <option value="Barang Rusak / Pecah / Tumpah">Barang Rusak / Pecah / Tumpah</option>
                                <option value="Koreksi Pengurangan / Hilang">Koreksi Pengurangan / Hilang</option>
                                <option value="Konsumsi Internal / Trial Resep Barista">Konsumsi Internal / Trial Resep Barista</option>
                            </optgroup>
                        </template>

                        <template x-if="modalType === 'adjustment'">
                            <optgroup label="Alasan Opname">
                                <option value="Stok Opname Rutin Mingguan">Stok Opname Rutin Mingguan</option>
                                <option value="Stok Opname Akhir Bulan">Stok Opname Akhir Bulan</option>
                                <option value="Penyesuaian Fisik Selisih Kasir">Penyesuaian Fisik Selisih Kasir</option>
                            </optgroup>
                        </template>
                    </select>
                </div>

                <!-- Catatan Tambahan (Opsional) -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Catatan Tambahan (Opsional)</label>
                    <input 
                        type="text" 
                        name="notes" 
                        placeholder="Contoh: No. Nota Supplier #1029 / Botol sirup hazelnut pecah" 
                        class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="showAdjustModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 text-white font-bold text-xs rounded-xl transition shadow-md"
                        :class="modalType === 'in' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20' : (modalType === 'out' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-500/20' : 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-amber-500/20')"
                    >
                        Simpan Catatan Audit
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
