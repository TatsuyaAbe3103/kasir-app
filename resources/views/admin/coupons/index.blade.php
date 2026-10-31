@extends('layouts.app')

@section('title', 'Kupon Promo & Diskon - Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Area Admin</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kupon Promo & Diskon</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Buat kode kupon diskon (persen atau nominal rupiah) yang bisa diketik oleh kasir saat transaksi.</p>
        </div>

        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20">
            <i class="fa-solid fa-ticket"></i>
            <span>Buat Kupon Baru</span>
        </a>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3 px-4">Kode Kupon</th>
                        <th class="py-3 px-4">Nama Promo</th>
                        <th class="py-3 px-4">Bentuk Potongan</th>
                        <th class="py-3 px-4">Nilai Diskon</th>
                        <th class="py-3 px-4">Syarat Min. Belanja</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Code -->
                            <td class="py-3.5 px-4 font-mono font-black text-sm text-amber-600">
                                <span class="bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-lg">
                                    {{ $coupon->code }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $coupon->name }}
                            </td>

                            <!-- Type -->
                            <td class="py-3.5 px-4">
                                @if($coupon->type === 'percent')
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[11px]">
                                        Persentase (%)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[11px]">
                                        Nominal Tetap (Rp)
                                    </span>
                                @endif
                            </td>

                            <!-- Value -->
                            <td class="py-3.5 px-4 font-extrabold text-slate-900 text-sm">
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @else
                                    Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                @endif
                            </td>

                            <!-- Min Order -->
                            <td class="py-3.5 px-4 text-slate-600 font-semibold">
                                @if($coupon->min_order > 0)
                                    Rp {{ number_format($coupon->min_order, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400">Tanpa Minimum</span>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}">
                                    @csrf
                                    @if($coupon->is_active)
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold transition">
                                            <i class="fa-solid fa-check text-[9px]"></i> Aktif
                                        </button>
                                    @else
                                        <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-full text-[10px] font-bold transition">
                                            Nonaktif
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="p-1.5 bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-700 rounded-lg text-xs transition" title="Edit Kupon">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Hapus kupon {{ $coupon->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-600 rounded-lg text-xs transition" title="Hapus Kupon">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Belum ada kupon promo yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
