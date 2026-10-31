@extends('layouts.app')

@section('title', 'Kelola Pengguna & Hak Akses - Nusa POS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{
    showCreateModal: false,
    showEditModal: false,
    showPasswordModal: false,
    showDeleteModal: false,
    editUser: { id: '', name: '', username: '', email: '', role: 'cashier', status: 'active' },
    passwordUser: { id: '', name: '', username: '', email: '' },
    deleteUser: { id: '', name: '', username: '', email: '' },

    openEdit(user) {
        this.editUser = { ...user };
        this.showEditModal = true;
    },
    openPassword(user) {
        this.passwordUser = { ...user };
        this.showPasswordModal = true;
    },
    openDelete(user) {
        this.deleteUser = { ...user };
        this.showDeleteModal = true;
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 font-bold text-xs uppercase tracking-wider">Area Admin</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Pengguna & Hak Akses</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Kontrol akun staf dan admin resto, setujui (approve) pendaftaran baru, atur hak akses, ganti password, atau hapus akun pengguna.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol Ganti Password Akun Saya -->
            <button 
                type="button" 
                @click="openPassword({ id: '{{ Auth::id() }}', name: '{{ addslashes(Auth::user()->name) }}', email: '{{ Auth::user()->email }}' })"
                class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition shadow-sm border border-slate-700"
            >
                <i class="fa-solid fa-key text-amber-400"></i>
                <span>Ganti Password Saya</span>
            </button>

            <!-- Tombol Tambah Pengguna Baru -->
            <button 
                type="button" 
                @click="showCreateModal = true" 
                class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs flex items-center space-x-2 transition shadow-md shadow-amber-500/20"
            >
                <i class="fa-solid fa-user-plus"></i>
                <span>+ Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- Banner Notifikasi Pendaftaran Menunggu Approval -->
    @if($pendingCount > 0)
        <div class="bg-amber-50 border-2 border-amber-300 text-amber-900 p-4 rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-lg flex-shrink-0 animate-bounce">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-amber-900">
                        Ada {{ $pendingCount }} Pendaftaran Akun Staf Menunggu Persetujuan Anda!
                    </h4>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Tinjau akun baru di bawah ini. Akun tersebut tidak bisa login sebelum Anda menyetujuinya (Approve).
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-sm whitespace-nowrap">
                Filter yang Pending &rarr;
            </a>
        </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Pengguna</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalUsers }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Akun Terdaftar</span>
            </div>
        </div>

        <!-- Total Admin -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 border border-amber-100">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Manager Resto</span>
                <span class="text-2xl font-black text-amber-600">{{ $adminCount }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Role Admin</span>
            </div>
        </div>

        <!-- Total Kasir -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-100">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kasir POS</span>
                <span class="text-2xl font-black text-emerald-600">{{ $cashierCount }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Role Kasir Operasional</span>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0 border border-rose-100">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pending Approval</span>
                <span class="text-2xl font-black {{ $pendingCount > 0 ? 'text-rose-600 animate-pulse' : 'text-slate-900' }}">{{ $pendingCount }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">Menunggu Persetujuan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search Name / Email -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Cari Nama / Email</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Contoh: Budi atau budi@email.com" 
                        class="w-full text-xs py-2.5 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Filter Role -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Filter Role</label>
                <select name="role" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Manager Resto (Admin)</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir POS (Cashier)</option>
                    <option value="auditor" {{ request('role') === 'auditor' ? 'selected' : '' }}>Staff Audit & Gudang (Auditor)</option>
                </select>
            </div>

            <!-- Filter Status Akun -->
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Status Persetujuan</label>
                <select name="status" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Aktif (Disetujui)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>🟡 Menunggu Persetujuan (Pending)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>🔴 Ditolak (Rejected)</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Filter Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-amber-500"></i>
                <span>Daftar Pengguna Sistem</span>
            </h2>
            <span class="text-xs text-slate-400 font-medium">Menampilkan {{ $users->count() }} dari {{ $users->total() }} akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase border-b border-slate-200 text-[11px]">
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">User ID (Login)</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Role</th>
                        <th class="py-3.5 px-4 text-center">Status Approval</th>
                        <th class="py-3.5 px-4">Tanggal Daftar</th>
                        <th class="py-3.5 px-4 text-center">Aksi & Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition {{ $user->status === 'pending' ? 'bg-amber-50/40' : ($user->id === Auth::id() ? 'bg-slate-50/50' : '') }}">
                            <!-- Pengguna -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0 {{ $user->role === 'admin' ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($user->role === 'auditor' ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200') }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <strong class="text-slate-900 text-sm font-bold">{{ $user->name }}</strong>
                                            @if($user->id === Auth::id())
                                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-300">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- User ID -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-slate-900 bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-xs">
                                    <i class="fa-solid fa-id-badge text-amber-600"></i>
                                    {{ $user->username ?? '-' }}
                                </span>
                            </td>

                            <!-- Email -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-slate-500 text-xs">
                                    {{ $user->email ?? '-' }}
                                </span>
                            </td>

                            <!-- Role -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 font-bold px-2.5 py-1 rounded-full text-xs border border-amber-200">
                                        <i class="fa-solid fa-crown text-[10px]"></i> Manager Resto
                                    </span>
                                @elseif($user->role === 'auditor')
                                    <span class="inline-flex items-center gap-1.5 bg-sky-50 text-sky-800 font-bold px-2.5 py-1 rounded-full text-xs border border-sky-200">
                                        <i class="fa-solid fa-boxes-stacked text-[10px]"></i> Staff Audit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 font-bold px-2.5 py-1 rounded-full text-xs border border-emerald-200">
                                        <i class="fa-solid fa-cash-register text-[10px]"></i> Kasir POS
                                    </span>
                                @endif
                            </td>

                            <!-- Status Approval -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full text-[10px] border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif (Disetujui)
                                    </span>
                                @elseif($user->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 font-extrabold px-2.5 py-1 rounded-full text-[10px] border border-amber-300 animate-pulse">
                                        <i class="fa-solid fa-clock text-[10px]"></i>
                                        Menunggu Persetujuan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 font-bold px-2.5 py-1 rounded-full text-[10px] border border-rose-200">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Terdaftar Sejak -->
                            <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Jika Status PENDING: Beri Tombol Cepat Setujui & Tolak -->
                                    @if($user->status === 'pending')
                                        <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-black transition flex items-center gap-1 shadow-sm" title="Setujui Akun Ini">
                                                <i class="fa-solid fa-check"></i>
                                                <span>Setujui</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" class="inline" onsubmit="return confirm('Tolak pendaftaran akun ini?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Tolak Pendaftaran">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>Tolak</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit User Button -->
                                    <button 
                                        type="button" 
                                        @click="openEdit({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', username: '{{ addslashes($user->username ?? '') }}', email: '{{ $user->email ?? '' }}', role: '{{ $user->role }}', status: '{{ $user->status }}' })"
                                        class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold transition flex items-center space-x-1" 
                                        title="Edit Profil, Role, & Status"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Ganti Password Cepat Button -->
                                    <button 
                                        type="button" 
                                        @click="openPassword({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', username: '{{ addslashes($user->username ?? '') }}', email: '{{ $user->email ?? '' }}' })"
                                        class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition flex items-center space-x-1" 
                                        title="Ganti Password"
                                    >
                                        <i class="fa-solid fa-key text-[11px] text-amber-500"></i>
                                        <span>Password</span>
                                    </button>

                                    <!-- Delete Button -->
                                    @if($user->id === Auth::id())
                                        <button 
                                            type="button" 
                                            disabled 
                                            class="p-1.5 bg-slate-100 text-slate-300 rounded-lg text-xs font-bold cursor-not-allowed" 
                                            title="Anda tidak bisa menghapus akun Anda sendiri"
                                        >
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    @elseif($user->role === 'admin' && $adminCount <= 1)
                                        <button 
                                            type="button" 
                                            disabled 
                                            class="p-1.5 bg-slate-100 text-slate-300 rounded-lg text-xs font-bold cursor-not-allowed" 
                                            title="Tidak bisa menghapus admin satu-satunya"
                                        >
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    @else
                                        <button 
                                            type="button" 
                                            @click="openDelete({ id: '{{ $user->id }}', name: '{{ addslashes($user->name) }}', username: '{{ addslashes($user->username ?? '') }}', email: '{{ $user->email ?? '' }}' })"
                                            class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition" 
                                            title="Hapus Pengguna"
                                        >
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-3xl mb-2 text-slate-300 block"></i>
                                Tidak ada akun pengguna yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: TAMBAH PENGGUNA BARU                                  -->
    <!-- ============================================================== -->
    <div 
        x-show="showCreateModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg p-6 space-y-5"
            @click.away="showCreateModal = false"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Tambah Pengguna Baru</h3>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Lengkap / Panggilan *</label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="Contoh: Budi Santoso (Kasir 02)" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <!-- User ID (Username) -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">User ID / Username (Untuk Login) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-mono">@</span>
                        <input 
                            type="text" 
                            name="username" 
                            required 
                            placeholder="contoh: kasir01" 
                            pattern="^[a-zA-Z0-9_\-]+$"
                            title="Hanya huruf, angka, strip (-), atau underscore (_)"
                            class="w-full text-xs py-2.5 pl-8 pr-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono font-semibold"
                        >
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Digunakan kasir/staf saat login. Gunakan huruf kecil atau angka tanpa spasi.</span>
                </div>

                <!-- Email -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Alamat Email (Opsional)</label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Contoh: kasir2@nusa.com (boleh dikosongkan)" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <!-- Role / Hak Akses -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Role / Peran Hak Akses *</label>
                    <select 
                        name="role" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                        <option value="cashier">Kasir POS (Hanya Layar Kasir, Riwayat & Meja)</option>
                        <option value="auditor">Staff Audit & Gudang (Hanya Kelola Stok & Laporan Mutasi)</option>
                        <option value="admin">Manager Resto / Admin (Akses Penuh Seluruh Sistem)</option>
                    </select>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Password *</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            minlength="6"
                            placeholder="Minimal 6 karakter" 
                            class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        >
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Konfirmasi Password *</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            minlength="6"
                            placeholder="Ulangi password" 
                            class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="showCreateModal = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20"
                    >
                        Simpan Pengguna Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: EDIT PENGGUNA (NAMA, EMAIL, ROLE, STATUS & PASSWORD)  -->
    <!-- ============================================================== -->
    <div 
        x-show="showEditModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-lg p-6 space-y-5"
            @click.away="showEditModal = false"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-user-pen text-sm"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Edit Data Pengguna</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/users') }}/' + editUser.id" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Nama Lengkap / Panggilan *</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="editUser.name" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <!-- User ID (Username) -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">User ID / Username (Untuk Login) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-mono">@</span>
                        <input 
                            type="text" 
                            name="username" 
                            x-model="editUser.username" 
                            required 
                            pattern="^[a-zA-Z0-9_\-]+$"
                            title="Hanya huruf, angka, strip (-), atau underscore (_)"
                            class="w-full text-xs py-2.5 pl-8 pr-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-mono font-semibold"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Alamat Email (Opsional)</label>
                    <input 
                        type="email" 
                        name="email" 
                        x-model="editUser.email" 
                        placeholder="Boleh dikosongkan"
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Role -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Role / Peran *</label>
                        <select 
                            name="role" 
                            x-model="editUser.role" 
                            required 
                            class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        >
                            <option value="cashier">Kasir POS</option>
                            <option value="auditor">Staff Audit & Stok</option>
                            <option value="admin">Manager Resto / Admin</option>
                        </select>
                    </div>

                    <!-- Status Approval -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">Status Persetujuan *</label>
                        <select 
                            name="status" 
                            x-model="editUser.status" 
                            required 
                            class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                        >
                            <option value="active">🟢 Aktif (Disetujui)</option>
                            <option value="pending">🟡 Menunggu Approval</option>
                            <option value="rejected">🔴 Ditolak (Rejected)</option>
                        </select>
                    </div>
                </div>

                <!-- Ganti Password Opsional -->
                <div class="border-t border-slate-100 pt-3">
                    <span class="text-xs font-bold text-slate-800 block mb-1">Ganti Password (Opsional)</span>
                    <p class="text-[10px] text-slate-400 mb-2">Biarkan kolom password kosong jika tidak ingin mengubah password akun ini.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input 
                                type="password" 
                                name="password" 
                                minlength="6"
                                placeholder="Password baru" 
                                class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                            >
                        </div>
                        <div>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                minlength="6"
                                placeholder="Ulangi password baru" 
                                class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                            >
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="showEditModal = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition shadow-md shadow-amber-500/20"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 3: GANTI PASSWORD KHUSUS                                 -->
    <!-- ============================================================== -->
    <div 
        x-show="showPasswordModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md p-6 space-y-4"
            @click.away="showPasswordModal = false"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight">Ganti Password Akun</h3>
                        <p class="text-[11px] text-slate-400" x-text="passwordUser.name + ' (User ID: ' + (passwordUser.username || passwordUser.email) + ')'"></p>
                    </div>
                </div>
                <button type="button" @click="showPasswordModal = false" class="text-slate-400 hover:text-slate-600 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/users') }}/' + passwordUser.id + '/password'" class="space-y-4">
                @csrf

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Password Baru *</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        minlength="6"
                        placeholder="Minimal 6 karakter" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Ulangi Password Baru *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        minlength="6"
                        placeholder="Ketik ulang password baru" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 font-semibold"
                    >
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="showPasswordModal = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm"
                    >
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 4: KONFIRMASI HAPUS PENGGUNA                             -->
    <!-- ============================================================== -->
    <div 
        x-show="showDeleteModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl w-full max-w-md p-6 space-y-4"
            @click.away="showDeleteModal = false"
        >
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0 border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Hapus Akun Pengguna?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Apakah Anda yakin ingin menghapus akun <strong class="text-slate-900" x-text="deleteUser.name"></strong> (User ID: <span class="font-mono text-slate-900 font-bold" x-text="deleteUser.username || deleteUser.email"></span>)?
                    </p>
                </div>
            </div>

            <form method="POST" :action="'{{ url('admin/users') }}/' + deleteUser.id" class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                @csrf
                @method('DELETE')

                <button 
                    type="button" 
                    @click="showDeleteModal = false" 
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl transition shadow-md shadow-rose-600/20"
                >
                    Ya, Hapus Akun
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
