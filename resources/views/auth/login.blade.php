<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - {{ $appBrandName ?? 'POS' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Ornaments -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md space-y-6 relative z-10">

        <!-- Logo & Brand Header -->
        <div class="text-center space-y-2">
            @if(!empty($appBrandLogo))
                <div class="inline-flex w-16 h-16 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700/80 items-center justify-center shadow-xl shadow-amber-500/20 p-1">
                    <img src="{{ asset($appBrandLogo) }}" alt="{{ $appBrandName }}" class="w-full h-full object-cover rounded-xl">
                </div>
            @else
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-500 items-center justify-center text-white shadow-xl shadow-amber-500/30">
                    <i class="fa-solid fa-mug-hot text-2xl"></i>
                </div>
            @endif
            <h1 class="text-2xl font-black tracking-tight text-white">{{ $appBrandName }}</h1>
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest">{{ $appBrandTagline ?? 'Sistem Kasir & Operasional Resto' }}</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-2xl text-xs font-semibold flex items-start space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Login Card -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-6">
            
            <div class="border-b border-slate-700 pb-3">
                <h2 class="text-lg font-black text-white">Masuk ke Sistem</h2>
                <p class="text-xs text-slate-400">Masukkan User ID dan Password staf Anda.</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                
                <!-- User ID / Username -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">User ID</label>
                    <div class="relative">
                        <i class="fa-solid fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            value="{{ old('username') }}" 
                            required 
                            autofocus
                            placeholder="Contoh: admin atau kasir" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-mono font-semibold"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            placeholder="Masukkan password Anda" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Masuk (Sign In)</span>
                    </button>
                </div>
            </form>

            <!-- Sign Up Link -->
            <div class="border-t border-slate-700/80 pt-4 text-center">
                <p class="text-xs text-slate-400">
                    Belum punya akun staf? 
                    <a href="{{ route('register') }}" class="font-bold text-amber-400 hover:text-amber-300 underline underline-offset-4 transition">
                        Daftar Akun Baru (Sign Up)
                    </a>
                </p>
                <p class="text-[10px] text-slate-500 mt-1">
                    * Pendaftaran akun baru memerlukan persetujuan (approval) oleh Manager/Admin sebelum dapat digunakan.
                </p>
            </div>

        </div>

        <div class="text-center">
            <span class="text-[10px] text-slate-600">&copy; {{ date('Y') }} {{ $appBrandName ?? 'POS' }}. {{ $appBrandTagline ?? 'Cafe & Resto System' }}.</span>
        </div>

    </div>

</body>
</html>
