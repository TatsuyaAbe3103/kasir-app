<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bayar di Meja - {{ $storeName }} ({{ $table->name }})</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen py-6 px-4 antialiased selection:bg-amber-500 selection:text-slate-950" x-data="payApp()">

    <div class="max-w-md mx-auto space-y-4">

        <!-- Top Header Navigation -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <a href="{{ route('guest.menu', $table->id) }}" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-300 hover:text-white">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-sm font-black text-white leading-tight">Pembayaran Meja</h1>
                    <span class="text-[10px] text-slate-400">{{ $storeName }}</span>
                </div>
            </div>
            <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 px-3 py-1 rounded-xl text-xs font-black">
                {{ $table->name }}
            </div>
        </div>

        <!-- Total Bill Card -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-900/80 p-5 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total yang Harus Dibayar</span>
                    <h2 class="text-2xl font-black text-amber-400 tracking-tight mt-0.5">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </h2>
                    <span class="text-[10px] font-mono text-slate-400 mt-1 block">
                        No. Invoice: <span class="text-slate-300 font-semibold">{{ $order->invoice_number }}</span>
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block font-semibold">Batas Waktu Bayar</span>
                    <div class="inline-flex items-center space-x-1.5 bg-rose-500/10 text-rose-400 border border-rose-500/30 px-2.5 py-1 rounded-lg mt-1 font-mono font-black text-xs">
                        <i class="fa-solid fa-clock text-[10px] animate-pulse"></i>
                        <span x-text="timerStr">15:00</span>
                    </div>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-amber-500/10 blur-xl"></div>
        </div>

        <!-- Payment Method Tabs (QRIS vs Virtual Account) -->
        <div class="grid grid-cols-2 gap-2 bg-slate-900/90 p-1.5 rounded-2xl border border-slate-800">
            <button 
                type="button"
                @click="activeMethod = 'qris'" 
                class="py-2.5 rounded-xl font-black text-xs flex items-center justify-center space-x-2 transition duration-200"
                :class="activeMethod === 'qris' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-400 hover:text-white'"
            >
                <i class="fa-solid fa-qrcode text-sm"></i>
                <span>QRIS Instan</span>
            </button>
            <button 
                type="button"
                @click="activeMethod = 'va'" 
                class="py-2.5 rounded-xl font-black text-xs flex items-center justify-center space-x-2 transition duration-200"
                :class="activeMethod === 'va' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-400 hover:text-white'"
            >
                <i class="fa-solid fa-building-columns text-sm"></i>
                <span>Virtual Account</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: QRIS INSTAN                         -->
        <!-- ========================================== -->
        <div x-show="activeMethod === 'qris'" class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl text-center space-y-4">
            <div>
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                    Bisa Semua E-Wallet & Mobile Banking
                </span>
                <h3 class="text-base font-black text-white mt-2">Scan QRIS untuk Bayar</h3>
                <p class="text-[11px] text-slate-400">
                    Buka GoPay, OVO, Dana, ShopeePay, BCA Mobile, Livin, atau aplikasi perbankan Anda.
                </p>
            </div>

            <!-- Dynamic / Custom QR Code Card -->
            <div class="inline-block p-4 bg-white rounded-3xl shadow-xl shadow-slate-950/50 border-4 border-slate-800 relative mx-auto text-center">
                @if(!empty($qrisImage))
                    <img 
                        src="{{ asset($qrisImage) }}" 
                        alt="QRIS {{ $qrisMerchantName }}" 
                        class="w-60 max-h-72 mx-auto object-contain rounded-xl"
                    >
                @else
                    <img 
                        src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data={{ urlencode('QRIS:' . $qrisMerchantName . '-INV:' . $order->invoice_number . '-TOTAL:' . $order->total_amount) }}" 
                        alt="QRIS Code" 
                        class="w-56 h-56 mx-auto object-contain rounded-xl"
                    >
                @endif
                <div class="mt-2.5 pt-2 border-t border-slate-200 space-y-0.5">
                    <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider block">Merchant Resmi</span>
                    <h4 class="text-xs font-black text-slate-900 tracking-tight">{{ $qrisMerchantName }}</h4>
                </div>
            </div>

            <!-- Supported Logos Badges -->
            <div class="flex items-center justify-center flex-wrap gap-1.5 pt-1 text-[10px] text-slate-400">
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">GoPay</span>
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">OVO</span>
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">Dana</span>
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">ShopeePay</span>
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">BCA Mobile</span>
                <span class="bg-slate-800 px-2 py-1 rounded-lg border border-slate-700/60 font-bold">Mandiri Livin</span>
            </div>

            <!-- Steps -->
            <div class="bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800 text-left space-y-2 text-xs text-slate-300">
                <div class="flex items-start space-x-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-0.5">1</span>
                    <p class="text-[11px]">Buka aplikasi pembayaran pilihanmu lalu pilih menu <strong>Scan / Bayar</strong>.</p>
                </div>
                <div class="flex items-start space-x-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-0.5">2</span>
                    <p class="text-[11px]">Arahkan kamera ke kode QRIS di atas dan pastikan nominal <strong class="text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>.</p>
                </div>
                <div class="flex items-start space-x-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-0.5">3</span>
                    <p class="text-[11px]">Selesaikan pembayaran lalu tekan tombol konfirmasi di bawah ini.</p>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: VIRTUAL ACCOUNT                     -->
        <!-- ========================================== -->
        <div x-show="activeMethod === 'va'" style="display: none;" class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl space-y-4">
            
            <div class="text-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
                    Transfer Rekening Bank & Virtual Account
                </span>
                <h3 class="text-base font-black text-white mt-2">Pilih Bank Pembayaran</h3>
            </div>

            <template x-if="activeBanks.length === 0">
                <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700 text-center text-xs text-slate-400">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg mb-1 block"></i>
                    Pembayaran transfer bank sedang dinonaktifkan oleh cafe. Silakan pilih metode <strong>QRIS Instan</strong> di atas.
                </div>
            </template>

            <template x-if="activeBanks.length > 0">
                <div class="space-y-4">
                    <!-- Bank Selector Tabs -->
                    <div class="flex flex-wrap gap-2 justify-center">
                        <template x-for="(bank, index) in activeBanks" :key="bank.id">
                            <button 
                                type="button"
                                @click="selectedBankIndex = index"
                                class="px-3.5 py-2 rounded-xl border text-center transition font-black text-xs flex-1 min-w-[85px]"
                                :class="selectedBankIndex === index ? 'bg-amber-500/20 border-amber-500 text-amber-300 shadow-sm' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white'"
                            >
                                <div class="text-xs font-black uppercase" x-text="bank.bank_code"></div>
                                <span class="text-[9px] font-normal block text-slate-400 truncate max-w-[100px]" x-text="bank.bank_name"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Virtual Account / Bank Account Number Display -->
                    <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase font-bold block" x-text="'No. Rekening / VA ' + (currentBank() ? currentBank().bank_name : '')"></span>
                                <div class="font-mono text-lg font-black text-white tracking-widest mt-0.5" x-text="currentBank() ? currentBank().account_number : '-'"></div>
                                <span class="text-[11px] text-slate-400 font-semibold block mt-1">
                                    a.n. <strong class="text-amber-400" x-text="currentBank() ? currentBank().account_name : '-'"></strong>
                                </span>
                            </div>
                            <button 
                                type="button"
                                @click="copyText(currentBank() ? currentBank().account_number : '', 'va')" 
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs transition border border-slate-700 flex items-center space-x-1.5 active:scale-95"
                            >
                                <i :class="copiedVA ? 'fa-solid fa-check text-emerald-400' : 'fa-regular fa-copy'"></i>
                                <span x-text="copiedVA ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        <div class="border-t border-slate-800 pt-3 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase font-bold block">Nominal Transfer Persis</span>
                                <div class="font-mono text-sm font-black text-amber-400 mt-0.5">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <button 
                                type="button"
                                @click="copyText('{{ $order->total_amount }}', 'amount')" 
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition border border-slate-700 flex items-center space-x-1.5 active:scale-95"
                            >
                                <i :class="copiedAmount ? 'fa-solid fa-check text-emerald-400' : 'fa-regular fa-copy'"></i>
                                <span x-text="copiedAmount ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Transfer Instructions -->
                    <div class="text-[11px] text-slate-400 space-y-1.5 bg-slate-800/40 p-3 rounded-2xl border border-slate-800">
                        <p class="font-semibold text-slate-300">Cara Bayar via m-Banking / ATM:</p>
                        <ol class="list-decimal list-inside space-y-1 text-slate-400">
                            <li>Buka m-Banking Anda, pilih menu <strong>Transfer Bank / Virtual Account</strong>.</li>
                            <li>Tujuan Bank: <strong class="text-white" x-text="currentBank() ? currentBank().bank_name : ''"></strong>.</li>
                            <li>Masukkan nomor rekening di atas & periksa nama penerima: <strong class="text-amber-400" x-text="currentBank() ? currentBank().account_name : ''"></strong>.</li>
                            <li>Selesaikan transaksi dan tekan tombol konfirmasi di bawah.</li>
                        </ol>
                    </div>
                </div>
            </template>

        </div>

        <!-- ========================================== -->
        <!-- ACTION BUTTON: KONFIRMASI PEMBAYARAN       -->
        <!-- ========================================== -->
        <div class="pt-2">
            <button 
                type="button"
                @click="confirmPay()" 
                :disabled="isConfirming"
                class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50 text-slate-950 font-black text-sm rounded-2xl transition shadow-xl shadow-emerald-500/20 flex items-center justify-center space-x-2 active:scale-98"
            >
                <i x-show="!isConfirming" class="fa-solid fa-circle-check text-base"></i>
                <i x-show="isConfirming" class="fa-solid fa-spinner fa-spin text-base"></i>
                <span x-text="isConfirming ? 'Memverifikasi Pembayaran...' : 'Saya Sudah Bayar (Konfirmasi)'"></span>
            </button>
            <p class="text-[10px] text-center text-slate-400 mt-2.5">
                ⚡ Begitu Anda selesai membayar, pesanan <strong class="text-emerald-400">otomatis langsung dimasak</strong> oleh Dapur & Barista!
            </p>
        </div>

    </div>

    <!-- Alpine App Logic -->
    <script>
        function payApp() {
            return {
                activeMethod: '{{ $order->payment_method === 'va' ? 'va' : 'qris' }}',
                activeBanks: @json($activeBanks),
                selectedBankIndex: 0,
                copiedVA: false,
                copiedAmount: false,
                timeLeft: 15 * 60,
                timerStr: '15:00',
                isConfirming: false,
                timerInterval: null,

                init() {
                    this.startTimer();
                },

                currentBank() {
                    if (this.activeBanks && this.activeBanks.length > 0) {
                        return this.activeBanks[this.selectedBankIndex] || this.activeBanks[0];
                    }
                    return null;
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                            const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
                            const s = (this.timeLeft % 60).toString().padStart(2, '0');
                            this.timerStr = `${m}:${s}`;
                        } else {
                            clearInterval(this.timerInterval);
                        }
                    }, 1000);
                },

                copyText(text, type) {
                    if (!text) return;
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text);
                    } else {
                        const el = document.createElement('textarea');
                        el.value = text;
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                    }

                    if (type === 'va') {
                        this.copiedVA = true;
                        setTimeout(() => this.copiedVA = false, 2500);
                    } else if (type === 'amount') {
                        this.copiedAmount = true;
                        setTimeout(() => this.copiedAmount = false, 2500);
                    }
                },

                async confirmPay() {
                    this.isConfirming = true;
                    const curBank = this.currentBank();
                    const pMethod = this.activeMethod === 'qris' ? 'qris' : (curBank ? 'va_' + curBank.bank_code.toLowerCase() : 'va');

                    try {
                        const response = await fetch("{{ route('guest.pay.confirm', ['table' => $table->id, 'order' => $order->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                payment_method: pMethod
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.href = result.redirect_url;
                        } else {
                            alert(result.message || 'Gagal memverifikasi pembayaran.');
                            this.isConfirming = false;
                        }
                    } catch (e) {
                        console.error('Error:', e);
                        alert('Terjadi kesalahan saat memproses verifikasi pembayaran.');
                        this.isConfirming = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
