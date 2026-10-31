<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockAudit;
use App\Models\Order;

class InventoryController extends Controller
{
    /**
     * Dashboard Manajemen Stok & Audit Barang
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Status Stok (semua, menipis, habis, tersedia)
        if ($request->filter === 'low') {
            $query->where('stock', '<=', 10)->where('stock', '>', 0);
        } elseif ($request->filter === 'out') {
            $query->where(function($q) {
                $q->where('stock', '<=', 0)->orWhere('is_available', false);
            });
        } elseif ($request->filter === 'ready') {
            $query->where('stock', '>', 10)->where('is_available', true);
        }

        // Pencarian Nama / SKU
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            });
        }

        $products = $query->orderBy('stock', 'asc')->paginate(15);

        // Ringkasan Statistik Audit & Stok
        $totalProducts = Product::count();
        $lowStockCount = Product::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->orWhere('is_available', false)->count();
        $totalStockUnits = Product::sum('stock');

        // 10 Riwayat Mutasi / Audit Terakhir
        $recentAudits = StockAudit::with(['product', 'user'])->latest()->take(10)->get();

        return view('inventory.index', compact(
            'products',
            'categories',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalStockUnits',
            'recentAudits'
        ));
    }

    /**
     * Catat Mutasi Stok: Penambahan (In), Pengurangan (Out), atau Opname Fisik
     */
    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:150',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $type = $validated['type'];
        $qty = (int) $validated['quantity'];

        if ($type === 'in') {
            $change = $qty;
        } elseif ($type === 'out') {
            if ($qty > $product->stock) {
                return redirect()->back()->with('error', "Jumlah stok keluar ({$qty}) tidak boleh melebihi stok yang tersedia saat ini ({$product->stock})!");
            }
            $change = -$qty;
        } else {
            // Opname
            $change = $qty; // Nilai akhir stok fisik
        }

        $product->logStockChange($change, $type, $validated['reason'], $validated['notes'], auth()->user());

        $typeLabel = match($type) {
            'in' => 'Penambahan Stok Masuk (+)',
            'out' => 'Pengurangan Stok Keluar (-)',
            'adjustment' => 'Penyesuaian Stok Opname',
        };

        return redirect()->back()->with('success', "{$typeLabel} untuk menu '{$product->name}' berhasil dicatat!");
    }

    /**
     * Tandai Produk Tersedia / Habis Secara Cepat
     */
    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = !$product->is_available;

        $product->update(['is_available' => $newStatus]);

        $statusText = $newStatus ? 'Tersedia (Ready)' : 'Habis (Sold Out)';
        $reason = $newStatus ? 'Ditandai Tersedia Manual oleh Auditor' : 'Ditandai Habis Manual oleh Auditor';

        StockAudit::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user() ? auth()->user()->name : 'Auditor',
            'type' => 'adjustment',
            'quantity' => 0,
            'stock_before' => $product->stock,
            'stock_after' => $product->stock,
            'reason' => $reason,
        ]);

        return redirect()->back()->with('success', "Status menu '{$product->name}' diubah menjadi {$statusText}!");
    }

    /**
     * Rekap Lengkap Laporan Barang Masuk & Keluar (Audit Mutasi Stok)
     */
    public function logs(Request $request)
    {
        $query = StockAudit::with(['product.category', 'user'])->latest();

        // Filter Jenis Mutasi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter Produk Menu
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Ringkasan Statistik Berdasarkan Filter yang sedang aktif
        $statsBase = StockAudit::query();
        if ($request->filled('date')) {
            $statsBase->whereDate('created_at', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $statsBase->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->filled('product_id')) {
            $statsBase->where('product_id', $request->product_id);
        }

        $totalInQty = (int) (clone $statsBase)->where('type', 'in')->sum('quantity');
        $totalOutQty = (int) abs((clone $statsBase)->where('type', 'out')->sum('quantity'));
        $totalSaleQty = (int) abs((clone $statsBase)->where('type', 'sale')->sum('quantity'));
        $totalAdjustmentCount = (int) (clone $statsBase)->where('type', 'adjustment')->count();

        $audits = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('inventory.logs', compact(
            'audits',
            'products',
            'totalInQty',
            'totalOutQty',
            'totalSaleQty',
            'totalAdjustmentCount'
        ));
    }

    /**
     * Histori & Audit Pesanan yang Dihapus / Void (Khusus Auditor & Admin)
     */
    public function deletedOrders(Request $request)
    {
        $query = Order::onlyTrashed()->with(['items.product', 'table'])->latest('deleted_at');

        if ($request->filled('date')) {
            $query->whereDate('deleted_at', $request->date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('deleted_by', 'like', "%{$s}%")
                  ->orWhere('delete_reason', 'like', "%{$s}%");
            });
        }

        $deletedOrders = $query->paginate(15)->withQueryString();

        // Statistik Ringkasan
        $totalDeletedCount = Order::onlyTrashed()->count();
        $totalDeletedAmount = (float) Order::onlyTrashed()->sum('total_amount');
        $todayDeletedCount = Order::onlyTrashed()->whereDate('deleted_at', today())->count();

        return view('inventory.deleted_orders', compact(
            'deletedOrders',
            'totalDeletedCount',
            'totalDeletedAmount',
            'todayDeletedCount'
        ));
    }
}
