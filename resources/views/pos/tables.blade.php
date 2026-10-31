@extends('layouts.app')

@section('title', 'Denah & Status Meja - Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ showAddTableModal: false, showEditTableModal: false, currentTable: {} }">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Denah & QR Code Meja Cafe</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau meja terisi, cetak stand QR untuk tamu, serta tambah atau kelola meja kafe.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tambah Meja Baru Button -->
            <button 
                type="button" 
                @click="showAddTableModal = true" 
                class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20 active:scale-95"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Meja Baru</span>
            </button>

            <!-- Cetak Semua Stand QR Meja -->
            <a 
                href="{{ route('tables.qr.all') }}" 
                target="_blank" 
                class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs flex items-center space-x-2 transition shadow-sm"
            >
                <i class="fa-solid fa-qrcode"></i>
                <span>Cetak Semua Stand QR Meja</span>
            </a>

            <!-- Legend Indicator -->
            <div class="flex items-center space-x-3 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold">
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-600 text-[11px]">Kosong</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span class="text-slate-600 text-[11px]">Terisi Tamu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($tables as $table)
            @php
                $isOccupied = ($table->status === 'occupied' || $table->activeOrder);
            @endphp
            <div class="bg-white rounded-3xl p-5 border-2 transition duration-200 flex flex-col justify-between shadow-sm hover:shadow-md {{ $isOccupied ? 'border-rose-400 bg-rose-50/20' : 'border-emerald-300' }}">
                
                <!-- Table Top Info -->
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm {{ $isOccupied ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                <i class="fa-solid fa-chair text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base leading-tight">{{ $table->name }}</h3>
                                <span class="text-[11px] text-slate-400 font-semibold">{{ $table->capacity }} Kursi</span>
                            </div>
                        </div>

                        <!-- Top Right: Status Pill & Action Buttons (Edit / Delete) -->
                        <div class="flex items-center space-x-1">
                            <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded-full tracking-wider {{ $isOccupied ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                {{ $isOccupied ? 'Terisi' : 'Tersedia' }}
                            </span>

                            <!-- Edit Table Button -->
                            <button 
                                type="button" 
                                @click="currentTable = {{ json_encode($table) }}; showEditTableModal = true" 
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition" 
                                title="Edit Nama & Kapasitas Meja"
                            >
                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            </button>

                            @if(!$isOccupied)
                                <!-- Delete Table Button -->
                                <form method="POST" action="{{ route('pos.tables.destroy', $table->id) }}" class="inline" onsubmit="return confirm('Hapus meja {{ $table->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center text-xs transition" 
                                        title="Hapus Meja"
                                    >
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Active Order Details if Occupied -->
                    @if($isOccupied && $table->activeOrder)
                        <div class="mt-4 p-3.5 bg-white rounded-2xl border border-rose-200/80 space-y-2 text-xs">
                            <div class="flex justify-between items-center text-slate-500 text-[11px]">
                                <span>Faktur:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $table->activeOrder->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-500 text-[11px]">
                                <span>Tamu:</span>
                                <span class="font-bold text-slate-800">{{ $table->activeOrder->customer_name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-500 text-[11px]">
                                <span>Total Tagihan:</span>
                                <span class="font-black text-rose-600 text-sm">Rp {{ number_format($table->activeOrder->total_amount, 0, ',', '.') }}</span>
                            </div>

                            @if($table->activeOrder->cashier_name === 'Self-Order (QR Meja)')
                                <span class="inline-block text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-md">
                                    📱 Pesanan Mandiri Tamu (QR)
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="mt-4 p-4 text-center text-slate-400 text-xs border border-dashed border-slate-200 rounded-2xl">
                            Meja siap untuk tamu baru.
                        </div>
                    @endif
                </div>

                <!-- Action Button at Bottom -->
                <div class="mt-5 pt-3 border-t border-slate-100 space-y-2">
                    
                    <div class="flex gap-2">
                        @if($isOccupied && $table->activeOrder)
                            <!-- Bayar Bill Meja -->
                            <form method="POST" action="{{ route('pos.orders.update', $table->activeOrder->id) }}" class="flex-1" onsubmit="return confirm('Selesaikan pembayaran meja ini?')">
                                @csrf
                                <input type="hidden" name="payment_status" value="paid">
                                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-sm">
                                    Lunasi Tagihan
                                </button>
                            </form>
                        @elseif($isOccupied)
                            <!-- Reset manual jika status occupied tapi order sudah selesai -->
                            <form method="POST" action="{{ route('pos.tables.reset', $table->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                                    Set Kosong
                                </button>
                            </form>
                        @else
                            <!-- Buat Pesanan untuk Meja ini dari Kasir -->
                            <a href="{{ route('pos.index') }}" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition text-center">
                                + Pesan Kasir
                            </a>
                        @endif

                        <!-- Tombol Buka Stand QR Meja -->
                        <a 
                            href="{{ route('tables.qr', $table->id) }}" 
                            target="_blank" 
                            title="Buka Lembar Stand QR Meja Ini" 
                            class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-bold transition flex items-center space-x-1"
                        >
                            <i class="fa-solid fa-qrcode text-xs"></i>
                            <span class="hidden sm:inline">QR</span>
                        </a>
                    </div>

                    <!-- Shortcut Coba Menu Tamu di Tab Baru -->
                    <a 
                        href="{{ route('guest.menu', $table->id) }}" 
                        target="_blank" 
                        class="text-[10px] text-center block text-slate-400 hover:text-amber-600 font-semibold transition"
                    >
                        🔗 Uji Coba Buka Menu HP Tamu (Simulasi Scan QR)
                    </a>

                </div>

            </div>
        @endforeach
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: TAMBAH MEJA BARU                                        -->
    <!-- ============================================================== -->
    <div 
        x-show="showAddTableModal" 
        @click.self="showAddTableModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Tambah Meja Kafe Baru</h3>
                        <p class="text-[11px] text-slate-500">QR Code meja akan otomatis dibuat setelah disimpan</p>
                    </div>
                </div>
                <button @click="showAddTableModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('pos.tables.store') }}" class="space-y-4">
                @csrf

                <!-- Nama Meja -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama / Nomor Meja *</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-ref="tableNameInput"
                        placeholder="Contoh: Meja 09, Outdoor 03, VIP 02" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold"
                    >
                    <!-- Template Cepat Pilihan Nama -->
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" @click="$refs.tableNameInput.value = 'Meja ' + '{{ str_pad(count($tables) + 1, 2, '0', STR_PAD_LEFT) }}'" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2 py-0.5 rounded-lg transition">
                            + Meja {{ str_pad(count($tables) + 1, 2, '0', STR_PAD_LEFT) }}
                        </button>
                        <button type="button" @click="$refs.tableNameInput.value = 'Outdoor 03'" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2 py-0.5 rounded-lg transition">
                            + Outdoor 03
                        </button>
                        <button type="button" @click="$refs.tableNameInput.value = 'VIP 02'" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2 py-0.5 rounded-lg transition">
                            + VIP 02
                        </button>
                        <button type="button" @click="$refs.tableNameInput.value = 'Rooftop 01'" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2 py-0.5 rounded-lg transition">
                            + Rooftop 01
                        </button>
                    </div>
                </div>

                <!-- Kapasitas Kursi -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Kapasitas Kursi (Orang) *</label>
                    <input 
                        type="number" 
                        name="capacity" 
                        x-ref="tableCapacityInput"
                        value="4" 
                        min="1" 
                        max="100" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold"
                    >
                    <!-- Quick Capacity Pills -->
                    <div class="flex items-center space-x-2 mt-2">
                        <button type="button" @click="$refs.tableCapacityInput.value = 2" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2.5 py-1 rounded-lg transition">
                            2 Kursi
                        </button>
                        <button type="button" @click="$refs.tableCapacityInput.value = 4" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2.5 py-1 rounded-lg transition">
                            4 Kursi
                        </button>
                        <button type="button" @click="$refs.tableCapacityInput.value = 6" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2.5 py-1 rounded-lg transition">
                            6 Kursi
                        </button>
                        <button type="button" @click="$refs.tableCapacityInput.value = 8" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-2.5 py-1 rounded-lg transition">
                            8 Kursi
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="showAddTableModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20">
                        Simpan & Buat Meja
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: EDIT MEJA                                               -->
    <!-- ============================================================== -->
    <div 
        x-show="showEditTableModal" 
        @click.self="showEditTableModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Edit Data Meja</h3>
                        <p class="text-[11px] text-slate-500" x-text="currentTable.name"></p>
                    </div>
                </div>
                <button @click="showEditTableModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" :action="'/tables/' + currentTable.id" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama / Nomor Meja *</label>
                    <input 
                        type="text" 
                        name="name" 
                        :value="currentTable.name" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold"
                    >
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Kapasitas Kursi (Orang) *</label>
                    <input 
                        type="number" 
                        name="capacity" 
                        :value="currentTable.capacity" 
                        min="1" 
                        max="100" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold"
                    >
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="showEditTableModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
