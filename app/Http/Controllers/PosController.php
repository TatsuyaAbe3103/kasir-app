<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Halaman Kasir POS
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->isAuditor()) {
            return redirect()->route('inventory.index');
        }

        $categories = Category::all();
        $products = Product::where('is_available', true)->with('category')->get();
        $tables = Table::all();
        
        // Pengaturan Biaya Tambahan Resto
        $taxEnabled = Setting::get('tax_enabled', '1') === '1';
        $taxPercent = (float) Setting::get('tax_percent', '10');
        $taxName = Setting::get('tax_name', 'PB1 Resto');

        $serviceChargeEnabled = Setting::get('service_charge_enabled', '1') === '1';
        $serviceChargePercent = (float) Setting::get('service_charge_percent', '5');
        $serviceChargeName = Setting::get('service_charge_name', 'Service Charge');

        // Kupon Promo Aktif
        $activeCoupons = Coupon::where('is_active', true)->get();

        // Shift Kasir Aktif
        $activeShift = \App\Models\CashierShift::where('status', 'open')->latest()->first();

        return view('pos.index', compact(
            'categories', 
            'products', 
            'tables', 
            'taxEnabled', 
            'taxPercent', 
            'taxName',
            'serviceChargeEnabled',
            'serviceChargePercent',
            'serviceChargeName',
            'activeCoupons',
            'activeShift'
        ));
    }

    /**
     * Simpan Transaksi Baru
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'order_type' => 'required|in:dine_in,takeaway',
            'table_id' => 'nullable|exists:tables,id',
            'payment_status' => 'required|in:unpaid,paid',
            'payment_method' => 'nullable|in:cash,qris,transfer,debit',
            'tax_percent' => 'nullable|numeric|min:0',
            'service_charge_percent' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Hitung subtotal dan siapkan item
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                // Kurangi stok otomatis & catat audit transaksi penjualan kasir
                $product->logStockChange(-$item['quantity'], 'sale', 'Penjualan Kasir');

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Biaya Tambahan Resto (PB1 & Service Charge)
            $serviceChargePercent = $validated['service_charge_percent'] ?? 0;
            $serviceChargeAmount = ($subtotal * $serviceChargePercent) / 100;

            $taxPercent = $validated['tax_percent'] ?? 0;
            $taxAmount = ($subtotal * $taxPercent) / 100;

            $discountAmount = 0;
            $couponCode = null;
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper(trim($validated['coupon_code'])))
                    ->where('is_active', true)
                    ->first();
                if ($coupon && $subtotal >= $coupon->min_order) {
                    $calc = $coupon->calculateDiscount($subtotal);
                    if ($calc > 0) {
                        $discountAmount = $calc;
                        $couponCode = $coupon->code;
                    }
                }
            } elseif (!empty($validated['discount_amount'])) {
                $discountAmount = min($subtotal, (float) $validated['discount_amount']);
            }

            $totalAmount = max(0, $subtotal + $serviceChargeAmount + $taxAmount - $discountAmount);

            $paidAmount = $validated['paid_amount'] ?? ($validated['payment_status'] === 'paid' ? $totalAmount : 0);
            $changeAmount = max(0, $paidAmount - $totalAmount);

            // Generate nomor faktur: INV-YYYYMMDD-XXXX
            $today = date('Ymd');
            $countToday = Order::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Status alur
            $status = ($validated['payment_status'] === 'paid') ? 'completed' : 'pending';

            $activeShift = \App\Models\CashierShift::where('status', 'open')->latest()->first();
            $splitBillCode = $request->input('split_bill_code');

            $order = Order::create([
                'invoice_number' => $splitBillCode ? ($invoiceNumber . '-' . $splitBillCode) : $invoiceNumber,
                'shift_id' => $activeShift ? $activeShift->id : null,
                'split_bill_code' => $splitBillCode,
                'table_id' => $validated['order_type'] === 'dine_in' ? ($validated['table_id'] ?? null) : null,
                'customer_name' => $validated['customer_name'] ?: 'Pelanggan',
                'order_type' => $validated['order_type'],
                'status' => $status,
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'subtotal' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'service_charge_percent' => $serviceChargePercent,
                'service_charge_amount' => $serviceChargeAmount,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'notes' => $validated['notes'] ?? null,
                'cashier_name' => auth()->user() ? auth()->user()->name : 'Kasir',
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Update status meja jika dine in
            if ($order->order_type === 'dine_in' && $order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->update([
                        'status' => $order->payment_status === 'unpaid' ? 'occupied' : 'available'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'receipt_url' => route('pos.receipt', $order->id, false),
                'kitchen_url' => route('pos.kitchen', $order->id, false),
            ]);
        });
    }

    /**
     * Struk Thermal Pembayaran (Receipt)
     */
    public function receipt($id)
    {
        $order = Order::with(['items.product', 'table'])->findOrFail($id);
        $storeName = Setting::get('store_name', 'CAFE NUSA POS');
        $storeAddress = Setting::get('store_address', 'Jl. Melati Utama No. 88, Jakarta');
        $storePhone = Setting::get('store_phone', '0812-3456-7890');

        return view('pos.receipt', compact('order', 'storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Tiket Dapur / Barista
     */
    public function kitchenTicket($id)
    {
        $order = Order::with(['items.product', 'table'])->findOrFail($id);
        return view('pos.kitchen', compact('order'));
    }

    public function printerTest()
    {
        $storeName = Setting::get('app_brand_name', 'HADI CAFEE');
        $storeTagline = Setting::get('app_brand_tagline', 'Cafe & Resto System');
        return view('pos.printer_test', compact('storeName', 'storeTagline'));
    }

    /**
     * Riwayat Pesanan & Laporan Harian
     */
    public function orders(Request $request)
    {
        if (auth()->check() && auth()->user()->isAuditor()) {
            return redirect()->route('inventory.index');
        }

        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        $isDeletedView = $isAdmin && $request->view === 'deleted';

        if ($isDeletedView) {
            $query = Order::onlyTrashed()->with(['items.product', 'table'])->latest('deleted_at');
        } else {
            $query = Order::with(['items', 'table'])->latest();
        }

        if ($request->filled('status') && !$isDeletedView) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status') && !$isDeletedView) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date')) {
            $dateCol = $isDeletedView ? 'deleted_at' : 'created_at';
            $query->whereDate($dateCol, $request->date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Rekap ringkas hari ini
        $todayTotalSales = Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount');
        $todayOrdersCount = Order::whereDate('created_at', today())->count();
        $unpaidCount = Order::where('payment_status', 'unpaid')->count();
        $deletedOrdersCount = $isAdmin ? Order::onlyTrashed()->count() : 0;

        return view('pos.orders', compact('orders', 'todayTotalSales', 'todayOrdersCount', 'unpaidCount', 'deletedOrdersCount', 'isDeletedView'));
    }

    /**
     * Hapus / Batalkan Order (Kasir & Admin)
     */
    public function destroyOrder(Request $request, $id)
    {
        $validated = $request->validate([
            'delete_reason' => 'required|string|max:255',
        ], [
            'delete_reason.required' => 'Alasan pembatalan/penghapusan pesanan wajib diisi.',
        ]);

        $order = Order::with(['items.product', 'table'])->findOrFail($id);

        return DB::transaction(function () use ($order, $validated) {
            $userName = auth()->user() ? auth()->user()->name : 'Kasir';

            // 1. Kembalikan stok untuk setiap item menu yang dipesan
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->logStockChange(
                        $item->quantity, // Return positive qty
                        'in',
                        "Pengembalian Stok (Order #{$order->invoice_number} Dibatalkan/Dihapus)",
                        "Alasan: {$validated['delete_reason']} (Dihapus oleh {$userName})",
                        auth()->user()
                    );
                }
            }

            // 2. Bebaskan meja jika tipe pesanan dine in
            if ($order->order_type === 'dine_in' && $order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $otherActive = Order::where('table_id', $table->id)
                        ->where('id', '!=', $order->id)
                        ->where('payment_status', 'unpaid')
                        ->exists();
                    if (!$otherActive) {
                        $table->update(['status' => 'available']);
                    }
                }
            }

            // 3. Catat audit penghapusan dan simpan
            $order->deleted_by = $userName;
            $order->deleted_by_id = auth()->id();
            $order->delete_reason = $validated['delete_reason'];
            $order->status = 'cancelled';
            $order->save();

            // 4. Soft delete agar hilang dari kasir namun tetap tersimpan untuk Admin & Audit
            $order->delete();

            return redirect()->back()->with('success', "Pesanan #{$order->invoice_number} berhasil dihapus & seluruh stok menu telah dikembalikan ke inventaris.");
        });
    }

    /**
     * Update Status Order
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($request->has('payment_status') && $request->payment_status === 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'completed',
                'paid_amount' => $order->total_amount,
                'change_amount' => 0,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        } elseif ($request->has('status')) {
            $order->update(['status' => $request->status]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Denah & Manajemen Meja
     */
    public function tables()
    {
        if (auth()->check() && auth()->user()->isAuditor()) {
            return redirect()->route('inventory.index');
        }

        $tables = Table::with('activeOrder.items')->get();
        return view('pos.tables', compact('tables'));
    }

    /**
     * Reset Status Meja
     */
    public function resetTable($id)
    {
        $table = Table::findOrFail($id);
        $table->update(['status' => 'available']);
        return redirect()->back()->with('success', 'Status meja berhasil direset!');
    }

    /**
     * Tambah Meja Baru
     */
    public function storeTable(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tables,name',
            'capacity' => 'required|integer|min:1|max:100',
        ]);

        $table = Table::create([
            'name' => trim($validated['name']),
            'capacity' => (int) $validated['capacity'],
            'status' => 'available',
        ]);

        return redirect()->back()->with('success', 'Meja ' . $table->name . ' berhasil ditambahkan! Stand QR meja otomatis siap dicetak.');
    }

    /**
     * Update Data Meja
     */
    public function updateTable(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1|max:100',
        ]);

        $table->update([
            'name' => trim($validated['name']),
            'capacity' => (int) $validated['capacity'],
        ]);

        return redirect()->back()->with('success', 'Data ' . $table->name . ' berhasil diperbarui!');
    }

    /**
     * Hapus Meja
     */
    public function destroyTable($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status === 'occupied' || $table->activeOrder) {
            return redirect()->back()->with('error', 'Meja ' . $table->name . ' tidak dapat dihapus karena sedang terisi tamu / ada pesanan aktif!');
        }

        $tableName = $table->name;
        $table->delete();

        return redirect()->back()->with('success', 'Meja ' . $tableName . ' berhasil dihapus!');
    }
}
