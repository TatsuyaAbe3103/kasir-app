<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashierShift;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Tampilkan riwayat shift kasir (Admin / Kasir)
     */
    public function index()
    {
        $shifts = CashierShift::with('user')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $activeShift = CashierShift::where('status', 'open')->latest()->first();

        return view('shift.index', compact('shifts', 'activeShift'));
    }

    /**
     * Cek status shift aktif saat ini (API / JSON untuk layar kasir)
     */
    public function current()
    {
        $activeShift = CashierShift::where('status', 'open')->latest()->first();

        if ($activeShift) {
            // Hitung penjualan sementara
            $orders = Order::where(function ($q) use ($activeShift) {
                $q->where('shift_id', $activeShift->id)
                  ->orWhere(function ($q2) use ($activeShift) {
                      $q2->whereNull('shift_id')
                         ->where('created_at', '>=', $activeShift->opened_at);
                  });
            })->where('payment_status', 'paid');

            $cashSales = (clone $orders)->where('payment_method', 'cash')->sum('total_amount');
            $qrisSales = (clone $orders)->where('payment_method', 'qris')->sum('total_amount');
            $transferSales = (clone $orders)->whereIn('payment_method', ['transfer', 'debit'])->sum('total_amount');
            $totalSales = (clone $orders)->sum('total_amount');
            $trxCount = (clone $orders)->count();

            $expectedCash = $activeShift->starting_cash + $cashSales;

            return response()->json([
                'has_active_shift' => true,
                'shift' => $activeShift,
                'summary' => [
                    'cash_sales' => $cashSales,
                    'qris_sales' => $qrisSales,
                    'transfer_sales' => $transferSales,
                    'total_sales' => $totalSales,
                    'expected_cash' => $expectedCash,
                    'trx_count' => $trxCount,
                ]
            ]);
        }

        return response()->json([
            'has_active_shift' => false,
            'shift' => null,
        ]);
    }

    /**
     * Buka Shift Kasir Baru (Input Kas Awal / Modal Laci)
     */
    public function open(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        // Cek jika sudah ada shift yang sedang terbuka
        $existing = CashierShift::where('status', 'open')->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada shift kasir yang aktif (# ' . $existing->id . ' oleh ' . $existing->cashier_name . '). Harap closing shift terlebih dahulu.'
            ], 422);
        }

        $shift = CashierShift::create([
            'user_id' => Auth::id(),
            'cashier_name' => Auth::user()?->name ?? 'Kasir',
            'opened_at' => now(),
            'starting_cash' => $request->starting_cash,
            'notes' => $request->notes,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil dibuka dengan Kas Awal ' . number_format($request->starting_cash, 0, ',', '.'),
            'shift' => $shift,
            'print_url' => route('shift.print.open', $shift->id, false),
        ]);
    }

    /**
     * Cetak Struk Kas Awal Kasir (Format Thermal 58mm/80mm persis CODK.TXT)
     */
    public function printOpeningReceipt($id)
    {
        $shift = CashierShift::findOrFail($id);
        $storeName = Setting::get('app_brand_name', 'HADI CAFEE');
        $storeTagline = Setting::get('app_brand_tagline', 'Cafe & Resto System');

        return view('shift.open_receipt', compact('shift', 'storeName', 'storeTagline'));
    }

    /**
     * Tutup Shift Kasir (Closing Shift & Hitung Selisih Uang Fisik)
     */
    public function close(Request $request, $id)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $shift = CashierShift::findOrFail($id);

        if ($shift->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Shift ini sudah ditutup sebelumnya.'
            ], 422);
        }

        // Kumpulkan order selama shift ini
        $orders = Order::where(function ($q) use ($shift) {
            $q->where('shift_id', $shift->id)
              ->orWhere(function ($q2) use ($shift) {
                  $q2->whereNull('shift_id')
                     ->where('created_at', '>=', $shift->opened_at);
              });
        })->where('payment_status', 'paid');

        // Update shift_id pada order yang belum terikat
        Order::whereNull('shift_id')
            ->where('created_at', '>=', $shift->opened_at)
            ->update(['shift_id' => $shift->id]);

        $totalCashSales = (clone $orders)->where('payment_method', 'cash')->sum('total_amount');
        $totalQrisSales = (clone $orders)->where('payment_method', 'qris')->sum('total_amount');
        $totalTransferSales = (clone $orders)->whereIn('payment_method', ['transfer', 'debit'])->sum('total_amount');
        $totalSales = (clone $orders)->sum('total_amount');
        $totalDiscount = (clone $orders)->sum('discount_amount');
        $totalTax = (clone $orders)->sum('tax_amount');
        $totalTransactions = (clone $orders)->count();

        // Hitung total order void/dihapus
        $totalVoid = Order::onlyTrashed()
            ->where('created_at', '>=', $shift->opened_at)
            ->count();

        $expectedCash = $shift->starting_cash + $totalCashSales;
        $actualCash = $request->actual_cash;
        $difference = $actualCash - $expectedCash; // Positif = Surplus, Negatif = Minus/Kurang

        $shift->update([
            'closed_at' => now(),
            'actual_cash' => $actualCash,
            'expected_cash' => $expectedCash,
            'difference' => $difference,
            'total_sales' => $totalSales,
            'total_cash_sales' => $totalCashSales,
            'total_qris_sales' => $totalQrisSales,
            'total_transfer_sales' => $totalTransferSales,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'total_transactions' => $totalTransactions,
            'total_void_transactions' => $totalVoid,
            'closed_by_name' => Auth::user()?->name ?? 'Kasir',
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Closing Shift Kasir berhasil disimpan! Selisih kas: ' . ($difference >= 0 ? '+' : '') . number_format($difference, 0, ',', '.'),
            'shift' => $shift,
            'print_url' => route('shift.print.close', $shift->id, false),
        ]);
    }

    /**
     * Cetak Struk Bukti Closing & Rekap Shift (Format Thermal persis CODC.TXT & CODT.TXT)
     */
    public function printClosingReceipt($id)
    {
        $shift = CashierShift::findOrFail($id);
        $storeName = Setting::get('app_brand_name', 'HADI CAFEE');
        $storeTagline = Setting::get('app_brand_tagline', 'Cafe & Resto System');

        // Ambil order pada shift ini untuk detail counter bon
        $orders = Order::where('shift_id', $shift->id)->with('items')->get();

        return view('shift.close_receipt', compact('shift', 'storeName', 'storeTagline', 'orders'));
    }
}
