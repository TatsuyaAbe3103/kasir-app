<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Coupon;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class GuestOrderController extends Controller
{
    /**
     * Menu Digital Tamu via Scan QR Meja
     */
    public function menu($tableId)
    {
        $table = Table::findOrFail($tableId);
        $categories = Category::all();
        $products = Product::where('is_available', true)->with('category')->get();

        $storeName = Setting::get('store_name', 'CAFE NUSA POS');
        $taxEnabled = Setting::get('tax_enabled', '1') === '1';
        $taxPercent = (float) Setting::get('tax_percent', '10');
        $serviceChargeEnabled = Setting::get('service_charge_enabled', '1') === '1';
        $serviceChargePercent = (float) Setting::get('service_charge_percent', '5');

        $activeCoupons = Coupon::where('is_active', true)->get();

        return view('guest.menu', compact(
            'table',
            'categories',
            'products',
            'storeName',
            'taxEnabled',
            'taxPercent',
            'serviceChargeEnabled',
            'serviceChargePercent',
            'activeCoupons'
        ));
    }

    /**
     * Tamu Kirim Pesanan dari HP
     */
    public function storeOrder(Request $request, $tableId)
    {
        $table = Table::findOrFail($tableId);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'payment_choice' => 'required|in:qris,va,cash',
            'notes' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $table) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                // Kurangi stok otomatis & catat audit pesanan tamu
                $product->logStockChange(-$item['quantity'], 'sale', 'Pesanan QR ' . $table->name);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Biaya Layanan & Pajak Restoran
            $serviceChargeEnabled = Setting::get('service_charge_enabled', '1') === '1';
            $serviceChargePercent = $serviceChargeEnabled ? (float) Setting::get('service_charge_percent', '5') : 0;
            $serviceChargeAmount = ($subtotal * $serviceChargePercent) / 100;

            $taxEnabled = Setting::get('tax_enabled', '1') === '1';
            $taxPercent = $taxEnabled ? (float) Setting::get('tax_percent', '10') : 0;
            $taxAmount = ($subtotal * $taxPercent) / 100;

            // Kupon dengan Validasi Anti-Abuse
            $discountAmount = 0;
            $couponCode = null;
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper(trim($validated['coupon_code'])))->where('is_active', true)->first();
                if ($coupon && $subtotal >= $coupon->min_order) {
                    $calc = $coupon->calculateDiscount($subtotal);
                    if ($calc > 0) {
                        $discountAmount = $calc;
                        $couponCode = $coupon->code;
                    }
                }
            }

            $totalAmount = max(0, $subtotal + $serviceChargeAmount + $taxAmount - $discountAmount);

            $today = date('Ymd');
            $countToday = Order::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'table_id' => $table->id,
                'customer_name' => $validated['customer_name'],
                'order_type' => 'dine_in',
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_choice'],
                'subtotal' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'service_charge_percent' => $serviceChargePercent,
                'service_charge_amount' => $serviceChargeAmount,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'change_amount' => 0,
                'notes' => $validated['notes'] ?? null,
                'cashier_name' => 'Self-Order (QR Meja)',
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Tandai status meja terisi
            $table->update(['status' => 'occupied']);

            // Jika memilih bayar langsung via QRIS atau VA di meja
            if (in_array($validated['payment_choice'], ['qris', 'va'])) {
                $redirectUrl = route('guest.pay', ['table' => $table->id, 'order' => $order->id]);
            } else {
                $redirectUrl = route('guest.success', ['table' => $table->id, 'order' => $order->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => $redirectUrl,
            ]);
        });
    }

    /**
     * Halaman Pembayaran Langsung di Meja (QRIS & Virtual Account)
     */
    public function paymentPage($tableId, $orderId)
    {
        $table = Table::findOrFail($tableId);
        $order = Order::with('items')->findOrFail($orderId);

        if ($order->payment_status === 'paid') {
            return redirect()->route('guest.success', ['table' => $table->id, 'order' => $order->id]);
        }

        $storeName = Setting::get('store_name', 'CAFE NUSA POS');
        $qrisMerchantName = Setting::get('qris_merchant_name', $storeName);
        $qrisImage = Setting::get('qris_image', null);
        $qrisNotes = Setting::get('qris_notes', 'Scan QRIS di atas lalu pastikan nama merchant sesuai');

        // Ambil semua rekening bank / Virtual Account yang statusnya AKTIF
        $activeBanks = BankAccount::where('is_active', true)->get();

        return view('guest.pay', compact('table', 'order', 'storeName', 'qrisMerchantName', 'qrisImage', 'qrisNotes', 'activeBanks'));
    }

    /**
     * Konfirmasi Pembayaran Langsung (Langsung Diproses Dapur!)
     */
    public function confirmPayment(Request $request, $tableId, $orderId)
    {
        $table = Table::findOrFail($tableId);
        $order = Order::findOrFail($orderId);

        $paymentMethod = $request->input('payment_method', $order->payment_method ?: 'qris');

        $order->update([
            'payment_status' => 'paid',
            'status' => 'cooking', // Langsung diproses dapur!
            'payment_method' => $paymentMethod,
            'paid_amount' => $order->total_amount,
            'change_amount' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi! Pesanan langsung diproses di dapur.',
            'redirect_url' => route('guest.success', ['table' => $table->id, 'order' => $order->id]),
        ]);
    }

    /**
     * Halaman Konfirmasi Pesanan untuk Tamu
     */
    public function orderSuccess($tableId, $orderId)
    {
        $table = Table::findOrFail($tableId);
        $order = Order::with('items')->findOrFail($orderId);
        $storeName = Setting::get('store_name', 'CAFE NUSA POS');

        return view('guest.success', compact('table', 'order', 'storeName'));
    }

    /**
     * Helper untuk mendapatkan Domain Deploy / IP LAN agar QR Code bisa di-scan dari HP
     */
    private function resolveHostUrl($routeUrl)
    {
        // 1. Cek apakah ada domain deploy yang diset di background (.env / config pos)
        $deployUrl = config('pos.deploy_url') ?: env('POS_DEPLOY_URL');
        if (!empty($deployUrl) && !str_contains($deployUrl, 'localhost') && !str_contains($deployUrl, '127.0.0.1')) {
            $parsed = parse_url($routeUrl);
            $pathAndQuery = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            return rtrim($deployUrl, '/') . $pathAndQuery;
        }

        // 2. Default lokal: otomatis gunakan IP LAN WiFi komputer server
        $host = request()->getHost();
        if ($host === '127.0.0.1' || $host === 'localhost') {
            $localIp = gethostbyname(gethostname());
            if ($localIp && $localIp !== '127.0.0.1') {
                return str_replace(['127.0.0.1', 'localhost'], $localIp, $routeUrl);
            }
        }
        return $routeUrl;
    }

    /**
     * Cetak Lembar Stand QR Code Meja (Tent Card)
     */
    public function printTableQR($tableId)
    {
        $table = Table::findOrFail($tableId);
        $storeName = Setting::get('store_name', 'CAFE NUSA POS');
        $orderUrl = $this->resolveHostUrl(route('guest.menu', $table->id));

        return view('guest.tent_card', compact('table', 'storeName', 'orderUrl'));
    }

    /**
     * Cetak Semua Lembar Stand QR Meja Sekaligus
     */
    public function printAllTablesQR()
    {
        $tables = Table::all();
        $storeName = Setting::get('store_name', 'CAFE NUSA POS');

        foreach ($tables as $t) {
            $t->qr_url = $this->resolveHostUrl(route('guest.menu', $t->id));
        }

        return view('guest.tent_cards_all', compact('tables', 'storeName'));
    }
}
