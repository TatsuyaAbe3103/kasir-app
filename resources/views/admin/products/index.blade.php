@extends('layouts.app')

@section('title', 'Kelola Menu Resto - Admin Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Area Admin</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Menu & Harga</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Tambah menu baru, ubah harga jual/modal, atau hapus menu yang sudah tidak dijual.</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama menu atau kode SKU..." 
                    class="w-full text-xs py-2.5 pl-10 pr-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500"
                >
            </div>
            <div class="flex gap-2">
                <select name="category_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3 px-4">Menu</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Modal (HPP)</th>
                        <th class="py-3 px-4">Harga Jual</th>
                        <th class="py-3 px-4">Estimasi Laba</th>
                        <th class="py-3 px-4 text-center">Status Stok</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Image + Name -->
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        src="{{ $p->image ?: 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=100' }}" 
                                        alt="{{ $p->name }}" 
                                        class="w-11 h-11 rounded-xl object-cover border border-slate-200 flex-shrink-0"
                                    >
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-mono text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold">{{ $p->code }}</span>
                                            <h4 class="font-bold text-slate-900 text-xs">{{ $p->name }}</h4>
                                        </div>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5 max-w-xs">{{ $p->description }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3 px-4">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-lg text-[11px] font-semibold">
                                    {{ $p->category->icon ?? '' }} {{ $p->category->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Cost Price -->
                            <td class="py-3 px-4 text-slate-500 font-medium">
                                Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- Sale Price -->
                            <td class="py-3 px-4 font-black text-slate-900">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </td>

                            <!-- Margin / Profit -->
                            <td class="py-3 px-4">
                                @php
                                    $profit = $p->price - $p->cost_price;
                                    $marginPercent = $p->price > 0 ? round(($profit / $p->price) * 100) : 0;
                                @endphp
                                <span class="text-emerald-600 font-extrabold block">
                                    +Rp {{ number_format($profit, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-emerald-500 font-bold">
                                    ({{ $marginPercent }}% margin)
                                </span>
                            </td>

                            <!-- Stock Toggle -->
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}">
                                    @csrf
                                    @if($p->is_available)
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold transition">
                                            <i class="fa-solid fa-check text-[9px]"></i> Tersedia
                                        </button>
                                    @else
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-[10px] font-bold transition">
                                            <i class="fa-solid fa-ban text-[9px]"></i> Habis
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions (Edit & Delete) -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="p-1.5 bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-700 rounded-lg text-xs transition" title="Edit Menu">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $p->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-600 rounded-lg text-xs transition" title="Hapus Menu">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Menu tidak ditemukan.
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

</div>
@endsection
