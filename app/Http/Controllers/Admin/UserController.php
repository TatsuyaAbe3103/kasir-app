<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan Halaman Kelola Pengguna & Hak Akses (User Management)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter Pencarian User ID / Nama / Email
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Filter Role
        if ($request->filled('role') && in_array($request->role, ['admin', 'cashier', 'auditor'])) {
            $query->where('role', $request->role);
        }

        // Filter Status Akun (active, pending, rejected)
        if ($request->filled('status') && in_array($request->status, ['active', 'pending', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Urutkan: Pendaftaran pending paling atas, lalu role admin, kasir, audit
        $users = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('role', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Ringkasan Statistik Pengguna
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $cashierCount = User::where('role', 'cashier')->count();
        $auditorCount = User::where('role', 'auditor')->count();
        $pendingCount = User::where('status', 'pending')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'adminCount',
            'cashierCount',
            'auditorCount',
            'pendingCount'
        ));
    }

    /**
     * Tambah Pengguna Baru Secara Manual oleh Admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|alpha_dash|min:3|max:50|unique:users,username',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150|unique:users,email',
            'role' => 'required|in:admin,cashier,auditor',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'User ID wajib diisi.',
            'username.alpha_dash' => 'User ID hanya boleh huruf, angka, strip, atau underscore.',
            'username.unique' => 'User ID ini sudah terdaftar oleh pengguna lain.',
            'username.min' => 'User ID minimal 3 karakter.',
            'name.required' => 'Nama pengguna wajib diisi.',
            'role.required' => 'Pilih role hak akses pengguna.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'username' => strtolower(trim($validated['username'])),
            'name' => $validated['name'],
            'email' => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'role' => $validated['role'],
            'status' => 'active', // Dibuat manual oleh admin langsung aktif
            'password' => Hash::make($validated['password']),
        ]);

        $roleLabel = match ($validated['role']) {
            'admin' => 'Manager Resto (Admin)',
            'cashier' => 'Kasir POS',
            'auditor' => 'Staff Audit & Gudang',
        };

        return redirect()->route('admin.users.index')->with('success', "Akun '{$validated['name']}' (User ID: {$validated['username']}) dengan role {$roleLabel} berhasil ditambahkan!");
    }

    /**
     * Update Data Pengguna (User ID, Nama, Email, Role, Status & Password Opsional)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', Rule::unique('users')->ignore($user->id)],
            'name' => 'required|string|max:100',
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,cashier,auditor',
            'status' => 'required|in:active,pending,rejected',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.required' => 'User ID wajib diisi.',
            'username.unique' => 'User ID ini sudah dipakai oleh akun lain.',
            'name.required' => 'Nama pengguna wajib diisi.',
            'role.required' => 'Pilih role pengguna.',
            'status.required' => 'Status akun wajib dipilih.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Keamanan: Mencegah hilangnya admin terakhir jika role diubah
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah role akun ini, karena ini adalah satu-satunya akun Admin yang ada di sistem!');
            }
        }

        $user->username = strtolower(trim($validated['username']));
        $user->name = $validated['name'];
        $user->email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', "Data pengguna '{$user->name}' (User ID: {$user->username}) berhasil diperbarui!");
    }

    /**
     * Setujui / Approve Pendaftaran Akun Staf Baru
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return redirect()->back()->with('success', "Pendaftaran User ID '{$user->username}' ({$user->name}) berhasil DISETUJUI (Approved). Staf sekarang sudah dapat login.");
    }

    /**
     * Tolak / Reject Pendaftaran Akun Staf Baru
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', "Pendaftaran User ID '{$user->username}' ({$user->name}) telah DITOLAK (Rejected).");
    }

    /**
     * Ubah Password Pengguna Tertentu / Akun Admin Sendiri
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', "Password untuk User ID '{$user->username}' ({$user->name}) berhasil diperbarui!");
    }

    /**
     * Hapus Pengguna
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Keamanan 1: Tidak bisa menghapus akun yang sedang login saat ini
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login!');
        }

        // Keamanan 2: Tidak bisa menghapus admin jika hanya tersisa 1 admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'Akun admin ini tidak dapat dihapus karena merupakan satu-satunya Admin tersisa!');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun '{$userName}' berhasil dihapus dari sistem.");
    }
}
