<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Pesanan Diterima - {{ $table->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-4 flex items-center justify-center">

    <div class="max-w-md w-full space-y-4">
        
        <!-- Status Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md text-center space-y-5">
            
            @if($order->payment_status === 'paid')
                <!-- Icon LUNAS & SEDANG DIMASAK -->
                <div class="relative w-20 h-20 mx-auto">
                    <div class="w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/20 animate-pulse">
                        <i class="fa-solid fa-fire-burner"></i>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center text-xs font-black shadow">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-300 bg-emerald-500/20 px-3 py-1 rounded-full border border-emerald-500/40 inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-check-circle text-xs"></i> LUNAS ({{ strtoupper($order->payment_method) }})
                    </span>
                    <h2 class="text-xl font-black text-white mt-2">Sedang Dimasak di Dapur! 👨‍🍳</h2>
                    <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                        Terima kasih <strong class="text-white">{{ $order->customer_name }}</strong>, pembayaran Anda telah diterima. Barista & koki kami sedang menyiapkan pesanan Anda.
                    </p>
                </div>
            @else
                <!-- Icon MENUNGGU KASIR -->
                <div class="w-20 h-20 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-3xl mx-auto animate-bounce">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>

                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20">
                        Pesanan Masuk Antrean
                    </span>
                    <h2 class="text-xl font-black text-white mt-2">Terima Kasih, {{ $order->customer_name }}!</h2>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                        Pesananmu telah tercatat di sistem kasir. Silakan duduk santai di <strong class="text-amber-400">{{ $table->name }}</strong>.
                    </p>
                </div>
            @endif

            <!-- Invoice & Table Pill -->
            <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block text-left">No. Faktur</span>
                    <span class="font-mono font-bold text-amber-400">{{ $order->invoice_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Meja Pemesan</span>
                    <span class="font-bold text-white">{{ $table->name }}</span>
                </div>
            </div>

            <!-- Items Summary -->
            <div class="text-left space-y-2 border-t border-slate-800 pt-4">
                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Rincian Menu yang Dipesan:</h4>
                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-800/80">
                            <span class="text-slate-300">
                                <strong>{{ $item->quantity }}x</strong> {{ $item->product_name }}
                                @if($item->notes)
                                    <span class="block text-[10px] text-amber-400/80 italic">({{ $item->notes }})</span>
                                @endif
                            </span>
                            <span class="font-bold text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 flex justify-between items-center text-sm border-t border-slate-800">
                    <span class="font-bold text-slate-300">Total Tagihan:</span>
                    <span class="text-base font-black text-amber-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Notice Payment Status -->
            @if($order->payment_status === 'paid')
                <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-[11px] text-emerald-200 text-left flex items-start space-x-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 text-xs"></i>
                    <p>
                        <strong>Tidak perlu ke kasir lagi!</strong> Hidangan akan langsung diantar pelayan ke <strong>{{ $table->name }}</strong> saat selesai dimasak.
                    </p>
                </div>
            @else
                <div class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-[11px] text-amber-200 text-left flex items-start space-x-2.5">
                    <i class="fa-solid fa-circle-info text-amber-400 mt-0.5 text-xs"></i>
                    <p>
                        Pembayaran dapat dilakukan di kasir saat Anda selesai menikmati hidangan dengan menyebutkan <strong>{{ $table->name }}</strong>.
                    </p>
                </div>
            @endif

            <!-- Add More Button -->
            <div class="pt-2">
                <a 
                    href="{{ route('guest.menu', $table->id) }}" 
                    class="w-full py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-2xl block transition border border-slate-700 shadow-sm"
                >
                    <i class="fa-solid fa-plus text-[10px] mr-1"></i> Pesan Menu Tambahan Lagi
                </a>
            </div>

        </div>

    </div>

</body>
</html>
