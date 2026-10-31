<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    /**
     * Tambah Bank / Virtual Account Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        BankAccount::create([
            'bank_name' => $validated['bank_name'],
            'bank_code' => strtoupper(trim($validated['bank_code'])),
            'account_number' => trim($validated['account_number']),
            'account_name' => trim($validated['account_name']),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Rekening bank / Virtual Account baru berhasil ditambahkan!');
    }

    /**
     * Update Data Rekening Bank
     */
    public function update(Request $request, $id)
    {
        $bank = BankAccount::findOrFail($id);

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $bank->update([
            'bank_name' => $validated['bank_name'],
            'bank_code' => strtoupper(trim($validated['bank_code'])),
            'account_number' => trim($validated['account_number']),
            'account_name' => trim($validated['account_name']),
        ]);

        return redirect()->back()->with('success', 'Data rekening bank ' . $bank->bank_code . ' berhasil diperbarui!');
    }

    /**
     * Toggle Aktif / Matiin Bank
     */
    public function toggle($id)
    {
        $bank = BankAccount::findOrFail($id);
        $bank->update(['is_active' => !$bank->is_active]);

        $statusText = $bank->is_active ? 'diaktifkan' : 'dimatikan (nonaktif)';
        return redirect()->back()->with('success', 'Bank ' . $bank->bank_name . ' berhasil ' . $statusText . '!');
    }

    /**
     * Hapus Rekening Bank
     */
    public function destroy($id)
    {
        $bank = BankAccount::findOrFail($id);
        $name = $bank->bank_name;
        $bank->delete();

        return redirect()->back()->with('success', 'Rekening ' . $name . ' berhasil dihapus!');
    }
}
