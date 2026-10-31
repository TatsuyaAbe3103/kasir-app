@extends('layouts.app')

@section('title', 'Histori Pesanan Dihapus / Void - ' . ($appBrandName ?? 'Hadi Cafee'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-rose-500/10 text-rose-600 font-bold text-xs uppercase tracking-wider">Audit & Kontrol</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Histori Pesanan Dihapus (Void)</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Audit log seluruh pesanan yang dibatalkan / dihapus oleh kasir. Pesanan ini disembunyikan dari layar kasir dan stok otomatis dikembalikan.</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Rekap Void</span>
            </button>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Stok Menu</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Pesanan Dihapus -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0 border border-rose-100">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Pesanan Dihapus</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalDeletedCount }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Transaksi Dibatalkan/Void</span>
            </div>
        </div>

        <!-- Total Nominal Void -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 border border-amber-100">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Nominal Dibatalkan</span>
                <span class="text-2xl font-black text-amber-600">Rp {{ number_format($totalDeletedAmount, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Tidak Masuk ke Omzet Toko</span>
            </div>
        </div>

        <!-- Dihapus Hari Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Dihapus Hari Ini</span>
                <span class="text-2xl font-black text-blue-600">{{ $todayDeletedCount }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Transaksi Void Tanggal {{ date('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm no-print">
        <form method="GET" action="{{ route('inventory.deleted_orders') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Cari Invoice / Kasir / Alasan</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Contoh: INV- atau Budi atau Salah Menu" 
                        class="w-full text-xs py-2.5 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Tanggal Dihapus</label>
                <input 
                    type="date" 
                    name="date" 
                    value="{{ request('date') }}" 
                    class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                >
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Filter Data Void
                </button>
                <a href="{{ route('inventory.deleted_orders') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Deleted Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-trash-can text-rose-500"></i>
                <span>Daftar Pesanan yang Dihapus / Void</span>
            </h2>
            <span class="text-xs text-slate-400 font-medium">Menampilkan {{ $deletedOrders->count() }} dari {{ $deletedOrders->total() }} pesanan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4">Invoice & Waktu Pesan</th>
                        <th class="py-3.5 px-4">Waktu Dihapus</th>
                        <th class="py-3.5 px-4">Dihapus Oleh (Kasir)</th>
                        <th class="py-3.5 px-4">Alasan Pembatalan</th>
                        <th class="py-3.5 px-4">Tipe / Meja</th>
                        <th class="py-3.5 px-4">Item & Pengembalian Stok</th>
                        <th class="py-3.5 px-4 text-right">Nominal Void</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($deletedOrders as $order)
                        <tr class="hover:bg-slate-50/80 transition bg-rose-50/10">
                            <!-- Invoice & Waktu Pesan -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-slate-900 block text-sm">{{ $order->invoice_number }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Dibuat: {{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>

                            <!-- Waktu Dihapus -->
                            <td class="py-3.5 px-4 font-mono text-rose-600 font-semibold text-[11px] whitespace-nowrap">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i>
                                {{ $order->deleted_at ? $order->deleted_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Dihapus Oleh -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-1.5">
                                    <span class="w-6 h-6 rounded-lg bg-slate-800 text-white flex items-center justify-center text-[10px] font-black">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </span>
                                    <strong class="text-slate-800 text-xs">{{ $order->deleted_by ?? 'Kasir' }}</strong>
                                </div>
                            </td>

                            <!-- Alasan Pembatalan -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="inline-block bg-rose-50 text-rose-800 font-bold px-2 py-0.5 rounded-lg text-xs border border-rose-200">
                                    {{ $order->delete_reason ?? 'Kesalahan input kasir' }}
                                </span>
                            </td>

                            <!-- Tipe / Meja -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($order->order_type === 'dine_in')
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 font-bold px-2 py-0.5 rounded-lg border border-amber-200">
                                        🍽️ {{ $order->table ? $order->table->name : 'Dine In' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 font-bold px-2 py-0.5 rounded-lg">
                                        🥡 Takeaway
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $order->customer_name }}</span>
                            </td>

                            <!-- Item & Pengembalian Stok -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-slate-700 font-medium">
                                                {{ $item->quantity }}x {{ $item->product_name }}
                                            </span>
                                            <span class="font-mono text-emerald-600 font-bold text-[10px] bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                                +{{ $item->quantity }} Stok Kembali
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Nominal -->
                            <td class="py-3.5 px-4 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                                <span class="line-through text-slate-400 text-xs block">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                <span class="text-rose-600 text-xs font-bold">Dibatalkan</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400 block"></i>
                                Tidak ada data pesanan yang dihapus / dibatalkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deletedOrders->hasPages())
            <div class="p-4 border-t border-slate-100 no-print">
                {{ $deletedOrders->links() }}
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
    }
</style>
@endsection
