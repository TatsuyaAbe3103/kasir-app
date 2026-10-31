<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru (Sign Up) - {{ $appBrandName ?? 'POS' }}</title>
    
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
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest">Pendaftaran Akun Staf Baru</p>
        </div>

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

        <!-- Register Card -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-5">
            
            <div class="border-b border-slate-700 pb-3">
                <h2 class="text-lg font-black text-white">Buat Akun Staf</h2>
                <p class="text-xs text-slate-400">Pilih User ID yang mudah diingat untuk login kasir.</p>
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                
                <!-- User ID -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">User ID (Untuk Login) *</label>
                    <div class="relative">
                        <i class="fa-solid fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="text" 
                            name="username" 
                            value="{{ old('username') }}" 
                            required 
                            placeholder="Contoh: kasir1 atau budi" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition font-mono font-semibold"
                        >
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">* Tanpa spasi, gunakan huruf kecil atau angka.</span>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Nama Lengkap *</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            placeholder="Contoh: Budi Santoso" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>

                <!-- Email (Opsional) -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                        Alamat Email <span class="text-slate-500 normal-case">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="nama@email.com (bisa dikosongkan)" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>

                <!-- Role / Peran Tugas -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Peran Tugas *</label>
                    <div class="relative">
                        <select 
                            name="role" 
                            required 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 px-3 text-xs text-white focus:outline-none focus:border-amber-500 transition font-semibold"
                        >
                            <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Kasir POS (Layar Kasir & Meja)</option>
                            <option value="auditor" {{ old('role') === 'auditor' ? 'selected' : '' }}>Staff Audit & Gudang (Kelola Stok)</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Password *</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            minlength="6"
                            placeholder="Minimal 6 karakter" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Konfirmasi Password *</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock-open absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            minlength="6"
                            placeholder="Ketik ulang password" 
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-xl py-2.5 pl-10 pr-4 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition"
                        >
                    </div>
                </div>

                <!-- Notice Approval -->
                <div class="bg-amber-500/10 border border-amber-500/20 p-3 rounded-2xl text-[11px] text-amber-300/90 space-y-1">
                    <div class="flex items-center space-x-1.5 font-bold">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>Persetujuan Admin Diperlukan</span>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-relaxed">
                        Setelah mendaftar, akun Anda akan berstatus <strong>Pending</strong> dan baru bisa login setelah <strong>disetujui (di-approve)</strong> oleh Manager Resto (Admin).
                    </p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Daftarkan Akun (Sign Up)</span>
                    </button>
                </div>
            </form>

            <!-- Sign In Link -->
            <div class="border-t border-slate-700/80 pt-4 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-amber-400 hover:text-amber-300 underline underline-offset-4 transition">
                        Masuk di Sini (Sign In)
                    </a>
                </p>
            </div>

        </div>

        <div class="text-center">
            <span class="text-[10px] text-slate-600">&copy; {{ date('Y') }} {{ $appBrandName ?? 'POS' }}. {{ $appBrandTagline ?? 'Cafe & Resto System' }}.</span>
        </div>

    </div>

</body>
</html>
