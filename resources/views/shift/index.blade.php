@extends('layouts.app')

@section('title', 'Rekap Shift Kasir & Cash Drawer - ' . ($appBrandName ?? 'POS'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Audit Kasir</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Shift Kasir & Cash Drawer</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Lacak modal kas awal, total uang tunai & non-tunai, serta verifikasi selisih uang laci kasir saat closing shift.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pos.index') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20">
                <i class="fa-solid fa-cash-register"></i>
                <span>Buka Layar Kasir</span>
            </a>
        </div>
    </div>

    <!-- Active Shift Status Card -->
    @if($activeShift)
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-3xl p-6 border border-slate-700 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0 animate-pulse">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500 text-slate-950 font-black text-[10px] uppercase">Shift Sedang Aktif</span>
                        <span class="text-xs text-slate-400 font-mono">Shift #{{ str_pad($activeShift->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="text-lg font-black text-white mt-1">{{ $activeShift->cashier_name }}</h3>
                    <p class="text-xs text-slate-300">Dibuka sejak {{ $activeShift->opened_at->format('d M Y, H:i') }} • Modal Kas Awal: <strong class="text-amber-400">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</strong></p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end md:self-center">
                <a href="{{ route('shift.print.open', $activeShift->id) }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition border border-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak Kas Awal</span>
                </a>
                <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-sm">
                    Lanjut Kasir &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-3xl p-5 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-amber-900">Belum Ada Shift Kasir yang Dibuka</h4>
                    <p class="text-xs text-amber-700 mt-0.5">Buka layar kasir untuk memasukkan modal uang kembalian di laci kas dan mencetak struk kas awal.</p>
                </div>
            </div>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-xs whitespace-nowrap">
                Buka Kasir Sekarang &rarr;
            </a>
        </div>
    @endif

    <!-- Shift History Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-amber-500"></i>
                <span>Riwayat Shift & Laporan Kasir</span>
            </h2>
            <span class="text-xs text-slate-400 font-medium">Total {{ $shifts->total() }} shift tercatat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4">No. Shift</th>
                        <th class="py-3.5 px-4">Kasir Bertugas</th>
                        <th class="py-3.5 px-4">Waktu Buka / Tutup</th>
                        <th class="py-3.5 px-4 text-right">Kas Awal</th>
                        <th class="py-3.5 px-4 text-right">Penjualan Tunai</th>
                        <th class="py-3.5 px-4 text-right">Total Omzet</th>
                        <th class="py-3.5 px-4 text-right">Uang Fisik Laci</th>
                        <th class="py-3.5 px-4 text-center">Selisih Kas</th>
                        <th class="py-3.5 px-4 text-center">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shifts as $s)
                        <tr class="hover:bg-slate-50/80 transition {{ $s->status === 'open' ? 'bg-emerald-50/30' : '' }}">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                #{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                                @if($s->status === 'open')
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-500 text-white">OPEN</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <strong class="text-slate-900 block">{{ $s->cashier_name }}</strong>
                                <span class="text-[10px] text-slate-400">ID: #{{ $s->user_id }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                <div><i class="fa-solid fa-arrow-right-to-bracket text-emerald-500 text-[10px] mr-1"></i> {{ $s->opened_at->format('d/m/y H:i') }}</div>
                                @if($s->closed_at)
                                    <div class="mt-0.5 text-slate-400"><i class="fa-solid fa-arrow-right-from-bracket text-rose-500 text-[10px] mr-1"></i> {{ $s->closed_at->format('d/m/y H:i') }}</div>
                                @else
                                    <span class="text-[10px] text-emerald-600 font-bold">Sedang Berjalan...</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-700">
                                Rp {{ number_format($s->starting_cash, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-600">
                                Rp {{ number_format($s->total_cash_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($s->total_sales, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800">
                                {{ $s->actual_cash !== null ? 'Rp ' . number_format($s->actual_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono">
                                @if($s->status === 'closed')
                                    @if($s->difference == 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            PAS (Rp 0)
                                        </span>
                                    @elseif($s->difference > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                            +Rp {{ number_format($s->difference, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 font-bold">
                                            -Rp {{ number_format(abs($s->difference), 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('shift.print.open', $s->id) }}" target="_blank" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition flex items-center space-x-1" title="Cetak Kas Awal">
                                        <i class="fa-solid fa-receipt text-[11px] text-amber-500"></i>
                                        <span>Awal</span>
                                    </a>
                                    @if($s->status === 'closed')
                                        <a href="{{ route('shift.print.close', $s->id) }}" target="_blank" class="p-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1" title="Cetak Struk Closing">
                                            <i class="fa-solid fa-print text-[11px] text-amber-400"></i>
                                            <span>Closing</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 block"></i>
                                Belum ada data shift kasir. Buka kasir untuk memulai transaksi pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
