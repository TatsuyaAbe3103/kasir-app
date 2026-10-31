<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Product;
use App\Models\StockAudit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuideController extends Controller
{
    /**
     * Tampilkan Halaman Pusat Panduan & Dokumentasi Interaktif
     */
    public function index()
    {
        return view('guide.index');
    }

    /**
     * Reset Data Demo (Transaksi, Meja, & Sampel Penjualan)
     */
    public function resetDemo(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Bersihkan transaksi order lama
            OrderItem::truncate();
            Order::truncate();
            if (Schema::hasTable('stock_audits')) {
                StockAudit::truncate();
            }

            // 2. Reset Status Semua Meja
            Table::query()->update(['status' => 'available']);

            // 3. Ambil produk yang tersedia
            $products = Product::where('is_available', true)->get();
            if ($products->isEmpty()) {
                $products = Product::all();
            }

            // 4. Generate 6 transaksi contoh hari ini agar laporan & riwayat tampil cantik
            if ($products->count() >= 2) {
                $paymentMethods = ['cash', 'qris', 'transfer', 'cash', 'qris'];
                $cashierNames = ['Siti (Kasir 01)', 'Manager Resto (Admin)'];

                for ($i = 1; $i <= 6; $i++) {
                    $orderNum = 'INV-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                    $method = $paymentMethods[array_rand($paymentMethods)];
                    $cashier = $cashierNames[array_rand($cashierNames)];
                    $tableId = ($i % 2 === 0) ? ($i % 5 + 1) : null;
                    $orderType = $tableId ? 'dine_in' : 'takeaway';

                    // Pilih 2-3 item acak
                    $sampleItems = $products->random(min(3, $products->count()));
                    $subtotal = 0;
                    $itemsData = [];

                    foreach ($sampleItems as $p) {
                        $qty = rand(1, 2);
                        $itemTotal = $p->price * $qty;
                        $subtotal += $itemTotal;
                        $itemsData[] = [
                            'product_id' => $p->id,
                            'product_name' => $p->name,
                            'price' => $p->price,
                            'cost_price' => $p->cost_price ?? ($p->price * 0.4),
                            'quantity' => $qty,
                            'subtotal' => $itemTotal,
                            'notes' => ($qty > 1) ? 'Less ice' : null,
                            'created_at' => Carbon::today()->addHours(8 + $i)->addMinutes(rand(5, 50)),
                            'updated_at' => Carbon::today()->addHours(8 + $i)->addMinutes(rand(5, 50)),
                        ];
                    }

                    $tax = round($subtotal * 0.1); // PB1 10%
                    $total = $subtotal + $tax;
                    $paidAmount = ($method === 'cash') ? ceil($total / 50000) * 50000 : $total;
                    $changeAmount = $paidAmount - $total;

                    $order = Order::create([
                        'order_number' => $orderNum,
                        'table_id' => $tableId,
                        'customer_name' => 'Pelanggan #' . $i,
                        'order_type' => $orderType,
                        'subtotal' => $subtotal,
                        'tax' => $tax,
                        'service_charge' => 0,
                        'discount_amount' => 0,
                        'total' => $total,
                        'payment_method' => $method,
                        'payment_status' => 'paid',
                        'paid_amount' => $paidAmount,
                        'change_amount' => $changeAmount,
                        'cashier_name' => $cashier,
                        'created_at' => Carbon::today()->addHours(8 + $i)->addMinutes(rand(5, 50)),
                        'updated_at' => Carbon::today()->addHours(8 + $i)->addMinutes(rand(5, 50)),
                    ]);

                    foreach ($itemsData as $item) {
                        $item['order_id'] = $order->id;
                        OrderItem::create($item);
                    }
                }

                // Tandai 1 meja sebagai 'occupied' (terisi) untuk demonstrasi visual denah meja
                $occupiedTable = Table::first();
                if ($occupiedTable) {
                    $occupiedTable->update(['status' => 'occupied']);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data Demo berhasil di-reset! Meja, stok, dan sampel riwayat transaksi kembali ke setelan awal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mereset data demo: ' . $e->getMessage());
        }
    }
}
