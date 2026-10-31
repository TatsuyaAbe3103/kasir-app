@extends('layouts.app')

@section('title', 'Riwayat Pesanan & Laporan - ' . ($appBrandName ?? 'Hadi Cafee'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{
    showDeleteModal: false,
    selectedOrder: { id: '', invoice: '', customer: '', total: '' },
    deleteReason: 'Salah input menu kasir',
    customNotes: ''
}">

    <!-- Header & Summary Cards -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $isDeletedView ? 'Histori Pesanan Dihapus (Void)' : 'Riwayat & Laporan Penjualan' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $isDeletedView ? 'Daftar audit pesanan yang dibatalkan kasir. Stok menu pada pesanan ini telah otomatis dikembalikan.' : 'Daftar seluruh transaksi kasir, open bill meja, dan status pesanan.' }}
            </p>
        </div>
        
        <div class="flex items-center gap-2">
            @if(Auth::user() && Auth::user()->isAdmin())
                <!-- Tab Toggle Khusus Admin: Pesanan Aktif vs Histori Dihapus -->
                <div class="bg-slate-200/80 p-1 rounded-xl flex items-center space-x-1 text-xs font-bold">
                    <a href="{{ route('pos.orders') }}" class="px-3 py-1.5 rounded-lg transition {{ !$isDeletedView ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-receipt mr-1"></i> Pesanan Aktif
                    </a>
                    <a href="{{ route('pos.orders', ['view' => 'deleted']) }}" class="px-3 py-1.5 rounded-lg transition {{ $isDeletedView ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:text-rose-600' }}">
                        <i class="fa-solid fa-trash-can mr-1"></i> Dihapus ({{ $deletedOrdersCount }})
                    </a>
                </div>
            @endif

            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20">
                <i class="fa-solid fa-plus"></i>
                <span>Transaksi Baru</span>
            </a>
        </div>
    </div>

    @if(!$isDeletedView)
        <!-- 3 Metrics Cards (Hanya di mode Pesanan Aktif) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Omzet Hari Ini -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Omzet Masuk Hari Ini</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">
                        Rp {{ number_format($todayTotalSales, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pesanan Hari Ini</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">
                        {{ $todayOrdersCount }} <span class="text-sm font-semibold text-slate-400">Order</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <!-- Open Bill / Belum Bayar -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Bill Aktif (Belum Lunas)</span>
                    <span class="text-2xl font-black {{ $unpaidCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1 block">
                        {{ $unpaidCount }} <span class="text-sm font-semibold text-slate-400">Meja</span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>
    @else
        <!-- Alert Box untuk Admin saat di mode Histori Dihapus -->
        <div class="bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-3xl flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider">Histori Khusus Pengawasan Admin & Audit</h4>
                    <p class="text-[11px] text-rose-700 mt-0.5">Seluruh pesanan di bawah ini telah dibatalkan kasir. Kasir biasa tidak memiliki akses untuk melihat histori ini.</p>
                </div>
            </div>
            <a href="{{ route('pos.orders') }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition">
                Kembali ke Pesanan Aktif
            </a>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('pos.orders') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @if($isDeletedView)
                <input type="hidden" name="view" value="deleted">
            @endif

            <!-- Search Invoice / Pelanggan -->
            <div>
                <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Cari Invoice / Tamu</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Contoh: INV- atau Budi" 
                    class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500 font-semibold"
                >
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            @if(!$isDeletedView)
                <!-- Filter Status Bayar -->
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Status Bayar</label>
                    <select name="payment_status" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">Semua Status Bayar</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                    </select>
                </div>
            @else
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Status Data</label>
                    <input type="text" readonly value="Pesanan Void / Dihapus" class="w-full text-xs py-2 px-3 bg-slate-100 border border-slate-200 rounded-xl font-bold text-rose-600">
                </div>
            @endif

            <!-- Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition">
                    Filter Data
                </button>
                <a href="{{ route('pos.orders', $isDeletedView ? ['view' => 'deleted'] : []) }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Invoice</th>
                        <th class="py-3.5 px-4">{{ $isDeletedView ? 'Waktu Dihapus' : 'Waktu Transaksi' }}</th>
                        <th class="py-3.5 px-4">Tipe / Meja</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Rincian Menu</th>
                        <th class="py-3.5 px-4">Total</th>
                        @if(!$isDeletedView)
                            <th class="py-3.5 px-4">Status Dapur</th>
                            <th class="py-3.5 px-4">Pembayaran</th>
                            <th class="py-3.5 px-4 text-center">Aksi & Kontrol</th>
                        @else
                            <th class="py-3.5 px-4">Dihapus Oleh</th>
                            <th class="py-3.5 px-4">Alasan Penghapusan</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-50/80 transition {{ $isDeletedView ? 'bg-rose-50/10' : '' }}">
                            <!-- Invoice -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <span class="{{ $isDeletedView ? 'line-through text-slate-400' : '' }}">{{ $ord->invoice_number }}</span>
                                @if($isDeletedView)
                                    <span class="block text-[10px] text-rose-600 font-bold font-sans">DIBATALKAN</span>
                                @endif
                            </td>

                            <!-- Time -->
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                @if($isDeletedView)
                                    <span class="text-rose-600 font-bold block">{{ $ord->deleted_at ? $ord->deleted_at->format('d M, H:i') : '-' }}</span>
                                    <span class="text-[10px] text-slate-400">Pesan: {{ $ord->created_at->format('d M, H:i') }}</span>
                                @else
                                    {{ $ord->created_at->format('d M, H:i') }}
                                @endif
                            </td>

                            <!-- Table -->
                            <td class="py-3.5 px-4">
                                @if($ord->order_type === 'dine_in')
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 font-bold px-2 py-0.5 rounded-lg border border-amber-200/60">
                                        🍽️ {{ $ord->table ? $ord->table->name : 'Dine In' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 font-bold px-2 py-0.5 rounded-lg">
                                        🥡 Takeaway
                                    </span>
                                @endif
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $ord->customer_name }}
                            </td>

                            <!-- Items -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="font-bold text-slate-700">{{ $ord->items->sum('quantity') }} Item:</span>
                                <span class="text-slate-500 text-[11px] line-clamp-1">
                                    {{ $ord->items->pluck('product_name')->join(', ') }}
                                </span>
                                @if($isDeletedView)
                                    <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">
                                        <i class="fa-solid fa-check"></i> Stok menu telah dikembalikan
                                    </span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                <span class="{{ $isDeletedView ? 'line-through text-slate-400' : '' }}">
                                    Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            @if(!$isDeletedView)
                                <!-- Status Pesanan (Dapur / Barista) -->
                                <td class="py-3.5 px-4">
                                    @if($ord->status === 'cooking')
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-amber-100 text-amber-800 font-extrabold px-2.5 py-1 rounded-full border border-amber-300 shadow-sm animate-pulse">
                                            <i class="fa-solid fa-fire-burner text-amber-600"></i> Dimasak
                                        </span>
                                    @elseif($ord->status === 'completed')
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                                            <i class="fa-solid fa-check-double text-emerald-600"></i> Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-slate-100 text-slate-700 font-bold px-2.5 py-1 rounded-full">
                                            <i class="fa-solid fa-hourglass-start"></i> Menunggu
                                        </span>
                                    @endif
                                </td>

                                <!-- Payment Status -->
                                <td class="py-3.5 px-4">
                                    @if($ord->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                            <i class="fa-solid fa-check text-[10px]"></i> Lunas ({{ strtoupper($ord->payment_method) }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-full border border-rose-200 animate-pulse">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Belum Bayar
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        @if($ord->status === 'cooking')
                                            <!-- Tombol Sajikan -->
                                            <form method="POST" action="{{ route('pos.orders.update', $ord->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="p-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-lg text-xs font-black transition flex items-center gap-1 shadow-sm" title="Tandai Sudah Selesai Disajikan">
                                                    <i class="fa-solid fa-check"></i> Sajikan
                                                </button>
                                            </form>
                                        @endif

                                        @if($ord->payment_status === 'unpaid')
                                            <!-- Bayar Sekarang -->
                                            <form method="POST" action="{{ route('pos.orders.update', $ord->id) }}" onsubmit="return confirm('Tandai tagihan ini sebagai Lunas?')">
                                                @csrf
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button type="submit" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1" title="Lunasi Tagihan">
                                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cetak Struk -->
                                        <a href="{{ route('pos.receipt', $ord->id) }}" target="_blank" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs transition" title="Cetak Struk Kasir">
                                            <i class="fa-solid fa-print"></i>
                                        </a>

                                        <!-- Cetak Dapur -->
                                        <a href="{{ route('pos.kitchen', $ord->id) }}" target="_blank" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs transition" title="Cetak Tiket Dapur">
                                            <i class="fa-solid fa-kitchen-set"></i>
                                        </a>

                                        <!-- Tombol Hapus / Batalkan Order (Bisa dilakukan Kasir jika ada kesalahan order) -->
                                        <button 
                                            type="button" 
                                            @click="selectedOrder = { id: '{{ $ord->id }}', invoice: '{{ $ord->invoice_number }}', customer: '{{ addslashes($ord->customer_name) }}', total: 'Rp {{ number_format($ord->total_amount, 0, ',', '.') }}' }; showDeleteModal = true"
                                            class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition flex items-center gap-1" 
                                            title="Batalkan / Hapus Pesanan Ini"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            @else
                                <!-- Dihapus Oleh (Khusus Admin View) -->
                                <td class="py-3.5 px-4">
                                    <strong class="text-slate-900 block font-bold text-xs">{{ $ord->deleted_by ?? 'Kasir' }}</strong>
                                </td>

                                <!-- Alasan Penghapusan (Khusus Admin View) -->
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="inline-block bg-rose-100 text-rose-800 font-bold px-2.5 py-1 rounded-lg text-xs border border-rose-200">
                                        {{ $ord->delete_reason ?? 'Kesalahan input kasir' }}
                                    </span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isDeletedView ? 7 : 9 }}" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                                {{ $isDeletedView ? 'Tidak ada data pesanan yang dihapus.' : 'Belum ada riwayat transaksi aktif.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MODAL KONFIRMASI HAPUS / BATALKAN PESANAN (KASIR & ADMIN)      -->
    <!-- ============================================================== -->
    <div 
        x-show="showDeleteModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md p-6 space-y-4"
            @click.away="showDeleteModal = false"
        >
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0 border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Batalkan & Hapus Pesanan?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Invoice: <strong class="text-slate-900 font-mono" x-text="selectedOrder.invoice"></strong> (<span x-text="selectedOrder.customer"></span> - <span class="font-bold text-slate-700" x-text="selectedOrder.total"></span>)
                    </p>
                </div>
            </div>

            <form method="POST" :action="'{{ url('orders') }}/' + selectedOrder.id" class="space-y-3 pt-2">
                @csrf
                @method('DELETE')

                <!-- Pilih Alasan Pembatalan / Kesalahan -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Pilih Alasan Kesalahan / Pembatalan *</label>
                    <select 
                        name="delete_reason" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 font-semibold"
                    >
                        <option value="Salah input menu oleh kasir">Salah input menu oleh kasir</option>
                        <option value="Pelanggan membatalkan pesanan">Pelanggan membatalkan pesanan</option>
                        <option value="Pesanan ganda / double input">Pesanan ganda / double input</option>
                        <option value="Salah pilih nomor meja">Salah pilih nomor meja</option>
                        <option value="Pelanggan ganti metode pembayaran / pesanan ulang">Pelanggan ganti metode pembayaran / pesanan ulang</option>
                        <option value="Kendala teknis / printer struk error">Kendala teknis / printer struk error</option>
                    </select>
                </div>

                <!-- Info Audit Stok & Meja -->
                <div class="bg-amber-50 p-3 rounded-2xl border border-amber-200/60 text-[11px] text-amber-800 space-y-1">
                    <div class="flex items-center space-x-1 font-bold">
                        <i class="fa-solid fa-boxes-stacked text-amber-600"></i>
                        <span>Pengembalian Stok Otomatis</span>
                    </div>
                    <p class="text-[10px] text-amber-700">
                        * Porsi menu yang dipesan akan otomatis dikembalikan ke inventaris gudang dan dicatat dalam audit stok resto.
                    </p>
                    <p class="text-[10px] text-amber-700">
                        * Catatan penghapusan ini akan tercatat dalam log audit Admin & Auditor, namun disembunyikan dari riwayat kasir.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="showDeleteModal = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Tutup
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl transition shadow-md shadow-rose-600/20"
                    >
                        Ya, Hapus Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
