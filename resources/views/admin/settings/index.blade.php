@extends('layouts.app')

@section('title', 'Pengaturan Toko - Nusa POS')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ showAddModal: false, editBankModal: false, currentEditBank: {} }">

    <!-- Header Section -->
    <div>
        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Area Admin</span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Toko & Restoran</h1>
        </div>
        <p class="text-xs text-slate-500 mt-1">Atur identitas toko, kelola rekening bank & Virtual Account (VA) untuk tamu, serta biaya resto & pajak.</p>
    </div>

    <!-- Info Alert: Penjelasan 2 Biaya Resto -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-900 space-y-2">
        <h4 class="font-extrabold flex items-center space-x-2">
            <i class="fa-solid fa-circle-info text-blue-600"></i>
            <span>Mengenal 2 Biaya Tambahan yang Umum di Cafe & Restoran:</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
            <div class="bg-white/80 p-3 rounded-xl border border-blue-100">
                <strong class="text-blue-950 block">1. PB1 (Pajak Restoran)</strong>
                <p class="text-[11px] text-blue-800 mt-0.5 leading-relaxed">
                    Pajak Restoran sebesar <strong>10%</strong> yang ditarik untuk pemerintah daerah kota/kabupaten.
                </p>
            </div>
            <div class="bg-white/80 p-3 rounded-xl border border-blue-100">
                <strong class="text-blue-950 block">2. Service Charge (Biaya Layanan)</strong>
                <p class="text-[11px] text-blue-800 mt-0.5 leading-relaxed">
                    Biasanya sebesar <strong>5% s/d 10%</strong> untuk layanan meja, tips barista, dan operasional resto.
                </p>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- CARD 1: KELOLA REKENING BANK & VIRTUAL ACCOUNT (BISA MATI/TAMBAH) -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-building-columns text-amber-500"></i>
                    <span>Daftar Bank & Virtual Account (VA) untuk Tamu</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Aktifkan/matikan bank sesuai kebutuhan, atau tambahkan rekening bank lain.
                </p>
            </div>
            <button 
                type="button" 
                @click="showAddModal = true"
                class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20 flex items-center justify-center space-x-1.5 self-start sm:self-auto"
            >
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Bank Baru</span>
            </button>
        </div>

        <!-- List Bank Accounts -->
        <div class="space-y-3">
            @forelse($bankAccounts as $bank)
                @php
                    $bCode = strtoupper(trim($bank->bank_code));
                    $bgClass = match($bCode) {
                        'BCA' => 'bg-blue-600 text-white',
                        'MANDIRI', 'MDR' => 'bg-amber-500 text-slate-950 font-black',
                        'BRI' => 'bg-blue-800 text-white',
                        'BNI' => 'bg-teal-700 text-white',
                        'BSI' => 'bg-emerald-700 text-white',
                        'CIMB' => 'bg-red-700 text-white',
                        'PERMATA' => 'bg-emerald-800 text-white',
                        'JAGO' => 'bg-orange-500 text-white',
                        'SEABANK' => 'bg-orange-600 text-white',
                        default => 'bg-slate-900 text-amber-400',
                    };
                    if (!$bank->is_active) {
                        $bgClass = 'bg-slate-300 text-slate-600';
                    }
                @endphp
                <div class="p-4 rounded-2xl border transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $bank->is_active ? 'bg-slate-50/80 border-slate-200/80' : 'bg-slate-100/60 border-slate-200/50 opacity-60' }}">
                    <div class="flex items-start space-x-3">
                        <div class="min-w-[56px] h-10 px-2.5 rounded-xl flex items-center justify-center font-black shadow-sm flex-shrink-0 tracking-tight text-center {{ $bgClass }} {{ strlen($bCode) > 4 ? 'text-[10px]' : 'text-xs' }}">
                            {{ $bCode }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="font-black text-slate-900 text-xs">{{ $bank->bank_name }}</h4>
                                @if($bank->is_active)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full text-[10px] border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif di Meja
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-200 text-slate-600 font-bold px-2 py-0.5 rounded-full text-[10px]">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Nonaktif (Mati)
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs">
                                <span class="text-slate-500">No. Rekening / VA:</span>
                                <strong class="font-mono text-slate-900 ml-1">{{ $bank->account_number }}</strong>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                a.n. <strong class="text-slate-700">{{ $bank->account_name }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Actions: Toggle Mati/Hidup, Edit, Hapus -->
                    <div class="flex items-center space-x-2 self-end sm:self-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/60 w-full sm:w-auto justify-end">
                        <!-- Toggle Button (Matiin / Aktifkan) -->
                        <form method="POST" action="{{ route('admin.bank_accounts.toggle', $bank->id) }}" class="inline">
                            @csrf
                            @if($bank->is_active)
                                <button 
                                    type="submit" 
                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center space-x-1" 
                                    title="Matikan Bank ini dari opsi pembayaran meja"
                                >
                                    <i class="fa-solid fa-power-off text-[10px]"></i>
                                    <span>Matikan</span>
                                </button>
                            @else
                                <button 
                                    type="submit" 
                                    class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs rounded-xl transition flex items-center space-x-1" 
                                    title="Aktifkan Bank ini agar muncul di meja"
                                >
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                    <span>Aktifkan</span>
                                </button>
                            @endif
                        </form>

                        <!-- Edit Button -->
                        <button 
                            type="button" 
                            @click="currentEditBank = {{ json_encode($bank) }}; editBankModal = true"
                            class="p-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs transition" 
                            title="Edit Rekening"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('admin.bank_accounts.destroy', $bank->id) }}" class="inline" onsubmit="return confirm('Hapus rekening {{ $bank->bank_name }}?')">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="p-2 bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 rounded-xl text-xs transition" 
                                title="Hapus Bank"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <i class="fa-solid fa-building-columns text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada rekening bank yang didaftarkan. Tamu hanya bisa bayar via QRIS.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- FORM UTAMA: BRANDING LOGO, DEPLOY, BIAYA RESTO, & TOKO        -->
    <!-- ============================================================== -->
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ previewLogo: '{{ !empty($settings['app_brand_logo']) ? asset($settings['app_brand_logo']) : '' }}' }">
        @csrf

        <!-- CARD 2: BRANDING LOGO & NAMA APLIKASI (WHITE-LABEL) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-palette text-amber-500"></i>
                    <span>Logo & Nama Brand Aplikasi (White-Label)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Ubah logo, nama aplikasi di header atas, dan tagline sesuai identitas brand kafe atau klien Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <!-- Preview Live Logo di Header -->
                <div class="p-4 bg-slate-900 rounded-2xl text-white space-y-3">
                    <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider block">Live Preview Header:</span>
                    <div class="flex items-center space-x-3">
                        <template x-if="previewLogo">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0">
                                <img :src="previewLogo" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!previewLogo">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-500 flex items-center justify-center text-white shadow-md flex-shrink-0">
                                <i class="fa-solid fa-mug-hot text-xl"></i>
                            </div>
                        </template>
                        <div>
                            <span class="font-black text-base tracking-tight text-white block leading-tight" x-text="$refs.brandNameInput ? $refs.brandNameInput.value : '{{ $settings['app_brand_name'] }}'"></span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold block" x-text="$refs.taglineInput ? $refs.taglineInput.value : '{{ $settings['app_brand_tagline'] }}'"></span>
                        </div>
                    </div>
                </div>

                <!-- Input Upload Logo & Text Form -->
                <div class="md:col-span-2 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Brand Aplikasi / Toko *</label>
                            <input 
                                type="text" 
                                name="app_brand_name" 
                                x-ref="brandNameInput" 
                                value="{{ $settings['app_brand_name'] }}" 
                                required 
                                class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-bold"
                            >
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Tagline / Slogan Brand</label>
                            <input 
                                type="text" 
                                name="app_brand_tagline" 
                                x-ref="taglineInput" 
                                value="{{ $settings['app_brand_tagline'] }}" 
                                placeholder="Contoh: Cafe & Resto System" 
                                class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                            >
                        </div>
                    </div>

                    <!-- Upload Gambar Logo -->
                    <div class="pt-1">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Upload File Gambar Logo (PNG / JPG / SVG / WebP)</label>
                        <input 
                            type="file" 
                            name="brand_logo" 
                            accept="image/*" 
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    previewLogo = URL.createObjectURL(file);
                                }
                            "
                            class="w-full text-xs py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-600 cursor-pointer"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">
                            Disarankan gambar berbentuk persegi (rasio 1:1, misalnya 200x200 px) dengan latar transparan.
                        </p>

                        @if(!empty($settings['app_brand_logo']))
                            <div class="mt-2 flex items-center space-x-2">
                                <label class="inline-flex items-center space-x-1.5 text-xs text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_logo" value="1" @change="if($event.target.checked) previewLogo = ''" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus logo kustom & kembalikan ke ikon cangkir kopi bawaan</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: PENGATURAN BIAYA RESTORAN -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-5">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center space-x-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-receipt text-amber-500"></i>
                <span>Biaya Tambahan Restoran / Cafe</span>
            </h3>

            <!-- 1. PAJAK RESTORAN (PB1) -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">Pajak Restoran (PB1)</h4>
                        <span class="text-[11px] text-slate-400">Aktifkan jika cafe menarik pajak restoran</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tax_enabled" value="1" {{ $settings['tax_enabled'] === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 block mb-1">Label / Nama Pajak di Struk</label>
                        <input type="text" name="tax_name" value="{{ $settings['tax_name'] }}" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 block mb-1">Persentase Pajak (%)</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" max="100" name="tax_percent" value="{{ $settings['tax_percent'] }}" required class="w-full text-xs py-2 pl-3 pr-8 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SERVICE CHARGE (BIAYA LAYANAN) -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">Service Charge (Biaya Layanan Resto)</h4>
                        <span class="text-[11px] text-slate-400">Biaya layanan untuk kenyamanan & pelayanan meja</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="service_charge_enabled" value="1" {{ $settings['service_charge_enabled'] === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 block mb-1">Label di Struk</label>
                        <input type="text" name="service_charge_name" value="{{ $settings['service_charge_name'] }}" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 block mb-1">Persentase Layanan (%)</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" max="100" name="service_charge_percent" value="{{ $settings['service_charge_percent'] }}" required class="w-full text-xs py-2 pl-3 pr-8 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QRIS CONFIGURATION (UPLOAD FOTO QRIS ASLI ATAU DINAMIS) -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2.5">
                    <div class="flex items-center space-x-2 text-xs font-black text-slate-900">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm"><i class="fa-solid fa-qrcode"></i></span>
                        <div>
                            <h4 class="font-black text-slate-900 text-xs">Pengaturan QRIS Resto</h4>
                            <span class="text-[10px] text-slate-500">Bisa upload foto barcode QRIS asli kafe atau pakai QR dinamis</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    <!-- Preview Foto QRIS yang sedang aktif -->
                    <div class="p-3 bg-white rounded-xl border border-slate-200 text-center space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">QRIS yang Tampil di HP Tamu:</span>
                        @if(!empty($settings['qris_image']))
                            <img src="{{ asset($settings['qris_image']) }}" alt="QRIS Toko" class="w-36 h-36 mx-auto object-contain rounded-lg border border-slate-100">
                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="fa-solid fa-check text-[9px]"></i> Pakai Foto QRIS Toko
                            </span>
                        @else
                            <div class="w-36 h-36 mx-auto bg-slate-100 rounded-lg flex flex-col items-center justify-center text-slate-400 p-2 border border-dashed border-slate-300">
                                <i class="fa-solid fa-qrcode text-3xl mb-1 text-slate-300"></i>
                                <span class="text-[10px] text-center font-semibold text-slate-500">QRIS Generator Otomatis</span>
                            </div>
                            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200">
                                Generator Otomatis
                            </span>
                        @endif
                    </div>

                    <!-- Input Detail QRIS -->
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Toko / Merchant pada QRIS *</label>
                            <input type="text" name="qris_merchant_name" value="{{ $settings['qris_merchant_name'] }}" placeholder="Contoh: CAFE NUSA POS" class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-700 block mb-1">Upload Foto / Gambar Barcode QRIS Asli Kafe</label>
                            <input 
                                type="file" 
                                name="qris_image_file" 
                                accept="image/*"
                                class="w-full text-xs py-1.5 px-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer"
                            >
                            <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">
                                Upload foto atau gambar barcode QRIS yang Anda dapatkan dari Bank BCA, Mandiri, GoPay Merchant, ShopeePay, atau DANA Bisnis kafe Anda.
                            </p>

                            @if(!empty($settings['qris_image']))
                                <div class="mt-2">
                                    <label class="inline-flex items-center space-x-1.5 text-xs text-rose-600 font-semibold cursor-pointer">
                                        <input type="checkbox" name="remove_qris_image" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        <span>Hapus foto QRIS ini (Kembali ke QRIS dinamis otomatis)</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- CARD 3: IDENTITAS TOKO DI STRUK -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center space-x-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-store text-amber-500"></i>
                <span>Informasi Toko (Tercetak di Struk)</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nama Cafe / Restoran *</label>
                    <input type="text" name="store_name" value="{{ $settings['store_name'] }}" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nomor Telepon Toko</label>
                    <input type="text" name="store_phone" value="{{ $settings['store_phone'] }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Alamat Cafe / Restoran</label>
                <input type="text" name="store_address" value="{{ $settings['store_address'] }}" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20">
                    Simpan Informasi Toko & Biaya Resto
                </button>
            </div>
        </div>

    </form>

    <!-- ============================================================== -->
    <!-- MODAL: TAMBAH BANK BARU                                        -->
    <!-- ============================================================== -->
    <div 
        x-show="showAddModal" 
        @click.self="showAddModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Tambah Rekening / Bank Baru</h3>
                        <p class="text-[11px] text-slate-500">Bank baru akan muncul sebagai pilihan di HP tamu</p>
                    </div>
                </div>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.bank_accounts.store') }}" class="space-y-3.5">
                @csrf

                <!-- Pilihan Template Bank Cepat -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Pilih Bank Populer (Opsional)</label>
                    <select 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        @change="
                            const val = $event.target.value;
                            if (val) {
                                const parts = val.split('|');
                                $refs.bankCodeInput.value = parts[0];
                                $refs.bankNameInput.value = parts[1];
                            }
                        "
                    >
                        <option value="">-- Pilih Bank --</option>
                        <option value="BCA|Bank Central Asia (BCA)">BCA - Bank Central Asia</option>
                        <option value="MANDIRI|Bank Mandiri">Mandiri - Bank Mandiri</option>
                        <option value="BRI|Bank Rakyat Indonesia (BRI)">BRI - Bank Rakyat Indonesia</option>
                        <option value="BNI|Bank Negara Indonesia (BNI)">BNI - Bank Negara Indonesia</option>
                        <option value="BSI|Bank Syariah Indonesia (BSI)">BSI - Bank Syariah Indonesia</option>
                        <option value="CIMB|Bank CIMB Niaga">CIMB Niaga</option>
                        <option value="PERMATA|Bank Permata">Bank Permata</option>
                        <option value="JAGO|Bank Jago">Bank Jago</option>
                        <option value="SEABANK|SeaBank Indonesia">SeaBank</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Kode Singkat *</label>
                        <input type="text" name="bank_code" x-ref="bankCodeInput" placeholder="BCA/BNI" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono uppercase">
                    </div>
                    <div class="col-span-2">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Lengkap Bank *</label>
                        <input type="text" name="bank_name" x-ref="bankNameInput" placeholder="Bank Central Asia (BCA)" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nomor Rekening / Virtual Account *</label>
                    <input type="text" name="account_number" placeholder="Contoh: 8410967465 atau 8808123456" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Atas Nama Pemilik Rekening (a.n) *</label>
                    <input type="text" name="account_name" placeholder="Contoh: Muhammad Hadi Syahputra" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20">
                        Simpan Rekening Bank
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: EDIT BANK                                               -->
    <!-- ============================================================== -->
    <div 
        x-show="editBankModal" 
        @click.self="editBankModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Edit Rekening Bank</h3>
                        <p class="text-[11px] text-slate-500" x-text="currentEditBank.bank_name"></p>
                    </div>
                </div>
                <button @click="editBankModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" :action="'/admin/bank-accounts/' + currentEditBank.id" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Kode Singkat *</label>
                        <input type="text" name="bank_code" :value="currentEditBank.bank_code" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono uppercase">
                    </div>
                    <div class="col-span-2">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Lengkap Bank *</label>
                        <input type="text" name="bank_name" :value="currentEditBank.bank_name" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nomor Rekening / Virtual Account *</label>
                    <input type="text" name="account_number" :value="currentEditBank.account_number" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Atas Nama Pemilik Rekening (a.n) *</label>
                    <input type="text" name="account_name" :value="currentEditBank.account_name" required class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="editBankModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
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
