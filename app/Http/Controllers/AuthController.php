<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.reports.index');
            } elseif (Auth::user()->isAuditor()) {
                return redirect()->route('inventory.index');
            }
            return redirect()->route('pos.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'User ID / Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Fleksibel: Bisa login pakai User ID (Username) ataupun Email
        $loginValue = strtolower(trim($input['username']));
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $loginValue,
            'password' => $input['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Cek Status Persetujuan Akun oleh Admin
            if (Auth::user()->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Pendaftaran akun Anda masih menunggu persetujuan (approval) dari Manager/Admin sebelum dapat digunakan.',
                ])->onlyInput('username');
            } elseif (Auth::user()->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Pendaftaran akun ini telah ditolak oleh Admin. Hubungi manager resto untuk informasi lebih lanjut.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.reports.index'))
                    ->with('success', 'Selamat datang, ' . Auth::user()->name . ' (Admin)!');
            } elseif (Auth::user()->isAuditor()) {
                return redirect()->intended(route('inventory.index'))
                    ->with('success', 'Selamat datang, ' . Auth::user()->name . ' (Auditor Stok)!');
            }

            return redirect()->intended(route('pos.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'username' => 'User ID atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    /**
     * Halaman Sign Up / Pendaftaran Staf Baru
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('pos.index');
        }

        return view('auth.register');
    }

    /**
     * Proses Sign Up Pengguna Baru Menggunakan User ID
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|alpha_dash|min:3|max:50|unique:users,username',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150|unique:users,email',
            'role' => 'required|in:cashier,auditor',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'User ID wajib diisi.',
            'username.alpha_dash' => 'User ID hanya boleh berisi huruf, angka, tanda strip (-), atau underscore (_).',
            'username.unique' => 'User ID ini sudah dipakai oleh staf lain. Silakan pilih User ID lain.',
            'username.min' => 'User ID minimal 3 karakter.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Pilih peran tugas staf.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'username' => strtolower(trim($validated['username'])),
            'name' => $validated['name'],
            'email' => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'role' => $validated['role'],
            'status' => 'pending', // Menunggu persetujuan admin!
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('success', "Pendaftaran User ID '{$validated['username']}' berhasil! Akun Anda sedang menunggu persetujuan (approval) dari Manager/Admin sebelum bisa login.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
