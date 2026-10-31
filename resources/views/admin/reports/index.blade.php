@extends('layouts.app')

@section('title', 'Laporan & Analitik Penjualan - Admin Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Area Admin</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Penjualan & Laba</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Rekap omzet, keuntungan bersih, dan rincian produk yang paling banyak terjual.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a 
                href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                target="_blank" 
                class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center space-x-2 transition shadow-sm"
            >
                <i class="fa-solid fa-print"></i>
                <span>Cetak / PDF Laporan</span>
            </a>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.reports.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition text-center">
                    Hari Ini
                </a>
            </div>
        </form>
    </div>

    <!-- 4 Key Performance Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Omzet -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Omzet (Kotor)</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">
                    Rp {{ number_format($totalSales, 0, ',', '.') }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <!-- Estimasi Laba Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estimasi Keuntungan</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">
                    +Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Transaksi Lunas</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">
                    {{ $totalOrders }} <span class="text-sm font-semibold text-slate-400">Order</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Total Item Terjual -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Menu Terjual</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block">
                    {{ $totalItemsSold }} <span class="text-sm font-semibold text-slate-400">Porsi/Cup</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-mug-saucer"></i>
            </div>
        </div>

    </div>

    <!-- Breakdown Cara Bayar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <h3 class="font-extrabold text-slate-800 text-sm flex items-center space-x-2">
            <i class="fa-solid fa-wallet text-amber-500"></i>
            <span>Breakdown Metode Pembayaran</span>
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @forelse($paymentMethods as $pm)
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase text-slate-500">{{ $pm->payment_method ?: 'Tunai' }}</span>
                        <h4 class="text-base font-extrabold text-slate-900 mt-0.5">Rp {{ number_format($pm->total, 0, ',', '.') }}</h4>
                    </div>
                    <span class="px-2.5 py-1 bg-white text-slate-700 text-xs font-bold rounded-lg border border-slate-200">
                        {{ $pm->count }}x Transaksi
                    </span>
                </div>
            @empty
                <div class="col-span-3 text-center py-3 text-slate-400 text-xs">
                    Belum ada data pembayaran pada rentang tanggal ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- REKAP MENU TERJUAL (BERAPA AJA YANG TERJUAL) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-3 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-ranking-star text-amber-500"></i>
                    <span>Rekap Produk Terjual (Best Sellers)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian berapa banyak tiap menu terjual dan kontribusi keuntungannya.</p>
            </div>
            <span class="bg-amber-100 text-amber-900 font-bold text-xs px-3 py-1 rounded-full">
                {{ $itemSales->count() }} Variasi Menu Terjual
            </span>
        </div>

        <div class="overflow-x-auto pt-2">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">Rank</th>
                        <th class="py-3 px-4">Menu & Kode</th>
                        <th class="py-3 px-4 text-center">Jumlah Terjual (Qty)</th>
                        <th class="py-3 px-4 text-right">Total Omzet</th>
                        <th class="py-3 px-4 text-right">Estimasi Profit</th>
                        <th class="py-3 px-4 text-center">Porsi Terhadap Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($itemSales as $index => $item)
                        @php
                            $percentage = $totalItemsSold > 0 ? round(($item->total_qty / $totalItemsSold) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Rank -->
                            <td class="py-3.5 px-4 text-center font-black">
                                @if($index === 0)
                                    <span class="w-6 h-6 rounded-full bg-amber-400 text-slate-950 inline-flex items-center justify-center text-xs shadow-sm">🥇</span>
                                @elseif($index === 1)
                                    <span class="w-6 h-6 rounded-full bg-slate-300 text-slate-950 inline-flex items-center justify-center text-xs">🥈</span>
                                @elseif($index === 2)
                                    <span class="w-6 h-6 rounded-full bg-amber-700 text-white inline-flex items-center justify-center text-xs">🥉</span>
                                @else
                                    <span class="text-slate-400 font-bold">{{ $index + 1 }}</span>
                                @endif
                            </td>

                            <!-- Item Name & Code -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold mr-1">{{ $item->product_code }}</span>
                                <span class="font-bold text-slate-900 text-xs">{{ $item->product_name }}</span>
                            </td>

                            <!-- Qty Sold -->
                            <td class="py-3.5 px-4 text-center font-black text-sm text-slate-900">
                                {{ $item->total_qty }} <span class="text-[11px] font-normal text-slate-400">porsi</span>
                            </td>

                            <!-- Omzet -->
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($item->total_omzet, 0, ',', '.') }}
                            </td>

                            <!-- Profit -->
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600">
                                +Rp {{ number_format($item->total_profit, 0, ',', '.') }}
                            </td>

                            <!-- Percentage Progress Bar -->
                            <td class="py-3.5 px-4 w-48 text-center">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-600 w-8 text-right">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Belum ada menu yang terjual pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
