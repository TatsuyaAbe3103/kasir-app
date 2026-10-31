@extends('layouts.app')

@section('title', 'Edit Kupon Promo - Nusa POS')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Back & Header -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.coupons.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Kupon: {{ $coupon->code }}</h1>
            <p class="text-xs text-slate-500">Perbarui informasi potongan kupon atau batasan belanja.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Kode Kupon -->
            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kode Kupon / Voucher *</label>
                <input 
                    type="text" 
                    name="code" 
                    value="{{ old('code', $coupon->code) }}" 
                    required 
                    class="w-full uppercase font-mono font-bold text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 @error('code') border-rose-500 @enderror"
                >
                @error('code') <span class="text-[10px] text-rose-500 font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Nama Promo -->
            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nama Promo / Keterangan *</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $coupon->name) }}" 
                    required 
                    class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                >
            </div>

            <!-- Tipe Diskon & Nilai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Tipe Potongan *</label>
                    <select name="type" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                        <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Besar Diskon *</label>
                    <input 
                        type="number" 
                        name="value" 
                        value="{{ old('value', $coupon->value) }}" 
                        required 
                        min="1" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- Minimal Belanja -->
            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Syarat Minimal Belanja (Rp)</label>
                <input 
                    type="number" 
                    name="min_order" 
                    value="{{ old('min_order', $coupon->min_order) }}" 
                    min="0" 
                    class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                >
            </div>

            <!-- Checkbox Aktif -->
            <div class="pt-1">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-0">
                    <span class="text-xs font-bold text-slate-800">Kupon Aktif dan bisa digunakan kasir</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black rounded-xl transition shadow-md shadow-amber-500/20">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
