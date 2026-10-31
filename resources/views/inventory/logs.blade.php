@extends('layouts.app')

@section('title', 'Laporan Barang Masuk & Keluar - Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('inventory.index') }}" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center text-xs transition shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Audit Gudang</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Barang Masuk & Keluar</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Rekap pergerakan stok: barang masuk dari supplier, barang keluar/rusak/basi, hasil opname fisik, dan penjualan kasir/QR.</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan</span>
            </button>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Kelola Stok</span>
            </a>
        </div>
    </div>

    <!-- Quick Tab Filter Mutasi -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1 no-print">
        <a href="{{ route('inventory.logs') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap {{ !request('type') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-layer-group"></i>
            <span>Semua Mutasi</span>
        </a>
        <a href="{{ route('inventory.logs', array_merge(request()->query(), ['type' => 'in'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap {{ request('type') === 'in' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-arrow-down-long text-emerald-500 {{ request('type') === 'in' ? 'text-white' : '' }}"></i>
            <span>Barang Masuk (+)</span>
        </a>
        <a href="{{ route('inventory.logs', array_merge(request()->query(), ['type' => 'out'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap {{ request('type') === 'out' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-arrow-up-long text-rose-500 {{ request('type') === 'out' ? 'text-white' : '' }}"></i>
            <span>Barang Keluar / Rusak (-)</span>
        </a>
        <a href="{{ route('inventory.logs', array_merge(request()->query(), ['type' => 'sale'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap {{ request('type') === 'sale' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-cart-shopping text-blue-500 {{ request('type') === 'sale' ? 'text-white' : '' }}"></i>
            <span>Terjual (Kasir & QR)</span>
        </a>
        <a href="{{ route('inventory.logs', array_merge(request()->query(), ['type' => 'adjustment'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 whitespace-nowrap {{ request('type') === 'adjustment' ? 'bg-amber-500 text-slate-950 font-black shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-scale-balanced text-amber-500 {{ request('type') === 'adjustment' ? 'text-slate-950' : '' }}"></i>
            <span>Hasil Opname Fisik</span>
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Masuk -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-100">
                <i class="fa-solid fa-arrow-down-long"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Barang Masuk</span>
                <span class="text-2xl font-black text-emerald-600">+{{ number_format($totalInQty) }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Porsi / Pcs Masuk</span>
            </div>
        </div>

        <!-- Total Keluar / Rusak -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0 border border-rose-100">
                <i class="fa-solid fa-arrow-up-long"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Barang Keluar / Rusak</span>
                <span class="text-2xl font-black text-rose-600">-{{ number_format($totalOutQty) }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Porsi Basi / Rusak / Expired</span>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Terjual Kasir & QR</span>
                <span class="text-2xl font-black text-blue-600">-{{ number_format($totalSaleQty) }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Porsi Berhasil Dijual</span>
            </div>
        </div>

        <!-- Hasil Opname -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 border border-amber-100">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Riwayat Opname</span>
                <span class="text-2xl font-black text-amber-600">{{ number_format($totalAdjustmentCount) }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Kali Kalibrasi Stok</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm no-print">
        <form method="GET" action="{{ route('inventory.logs') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Filter Jenis Mutasi -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Jenis Pergerakan</label>
                <select name="type" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
                    <option value="">-- Semua Jenis --</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stok Masuk (+)</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stok Keluar / Rusak (-)</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Stock Opname Fisik</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Penjualan Kasir / QR</option>
                </select>
            </div>

            <!-- Filter Menu -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Pilih Menu</label>
                <select name="product_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
                    <option value="">-- Semua Menu --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                    Filter Laporan
                </button>
                <a href="{{ route('inventory.logs') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-amber-500"></i>
                <span>Tabel Rincian Riwayat Mutasi Stok</span>
            </h2>
            <span class="text-xs text-slate-400 font-medium">Menampilkan {{ $audits->count() }} dari {{ $audits->total() }} riwayat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Menu & Kode</th>
                        <th class="py-3.5 px-4 text-center">Jenis Mutasi</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Porsi</th>
                        <th class="py-3.5 px-4 text-center">Perubahan Stok</th>
                        <th class="py-3.5 px-4">Alasan / Keterangan</th>
                        <th class="py-3.5 px-4">Auditor / Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($audits as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Waktu -->
                            <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Menu -->
                            <td class="py-3.5 px-4">
                                <strong class="text-slate-900 block font-bold">{{ $log->product->name ?? 'Produk Dihapus' }}</strong>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->product->code ?? '-' }}</span>
                            </td>

                            <!-- Jenis -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($log->type === 'in')
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full text-[10px] border border-emerald-200">
                                        <i class="fa-solid fa-plus text-[9px]"></i> Stok Masuk
                                    </span>
                                @elseif($log->type === 'out')
                                    <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-full text-[10px] border border-rose-200">
                                        <i class="fa-solid fa-minus text-[9px]"></i> Stok Keluar / Rusak
                                    </span>
                                @elseif($log->type === 'adjustment')
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 font-bold px-2 py-0.5 rounded-full text-[10px] border border-amber-200">
                                        <i class="fa-solid fa-scale-balanced text-[9px]"></i> Opname Fisik
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded-full text-[10px] border border-blue-200">
                                        <i class="fa-solid fa-cart-shopping text-[9px]"></i> Penjualan
                                    </span>
                                @endif
                            </td>

                            <!-- Quantity -->
                            <td class="py-3.5 px-4 text-center font-mono font-black text-sm whitespace-nowrap {{ $log->quantity > 0 ? 'text-emerald-600' : ($log->quantity < 0 ? 'text-rose-600' : 'text-slate-700') }}">
                                {{ $log->quantity > 0 ? '+' . $log->quantity : $log->quantity }}
                            </td>

                            <!-- Stock Before & After -->
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $log->stock_before }} &rarr; <strong class="text-slate-900">{{ $log->stock_after }}</strong>
                            </td>

                            <!-- Reason & Notes -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="font-semibold text-slate-800 block">{{ $log->reason }}</span>
                                @if($log->notes)
                                    <span class="text-[11px] text-slate-500 italic block">{{ $log->notes }}</span>
                                @endif
                            </td>

                            <!-- Auditor -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-700 text-xs">{{ $log->user_name ?? ($log->user->name ?? 'Sistem') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada riwayat mutasi barang masuk/keluar yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($audits->hasPages())
            <div class="p-4 border-t border-slate-100 no-print">
                {{ $audits->links() }}
            </div>
        @endif
    </div>

</div>

<style>
    @media print {
        header, .no-print {
            display: none !important;
        }
        body {
            background: white !important;
        }
        .max-w-7xl {
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
