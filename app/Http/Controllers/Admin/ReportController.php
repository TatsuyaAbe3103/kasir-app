<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Dashboard & Laporan Penjualan Harian / Berkala
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Query orders yang sudah lunas dalam rentang tanggal
        $ordersQuery = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalSales = (clone $ordersQuery)->sum('total_amount');
        $totalOrders = (clone $ordersQuery)->count();
        $totalDiscount = (clone $ordersQuery)->sum('discount_amount');
        $totalTax = (clone $ordersQuery)->sum('tax_amount');

        // Breakdown Metode Pembayaran
        $paymentMethods = (clone $ordersQuery)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Rekap Menu / Item Terjual (Berapa aja yang terjual)
        $itemSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->select(
                'order_items.product_name',
                DB::raw('COALESCE(products.code, "-") as product_code'),
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_omzet'),
                DB::raw('SUM((order_items.price - COALESCE(products.cost_price, 0)) * order_items.quantity) as total_profit')
            )
            ->groupBy('order_items.product_name', 'products.code')
            ->orderByDesc('total_qty')
            ->get();

        $totalItemsSold = $itemSales->sum('total_qty');
        $totalProfit = $itemSales->sum('total_profit');

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalOrders',
            'totalDiscount',
            'totalTax',
            'totalItemsSold',
            'totalProfit',
            'paymentMethods',
            'itemSales'
        ));
    }

    /**
     * Cetak Laporan Harian (Print / PDF View)
     */
    public function printReport(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $ordersQuery = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalSales = (clone $ordersQuery)->sum('total_amount');
        $totalOrders = (clone $ordersQuery)->count();

        $itemSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->select(
                'order_items.product_name',
                DB::raw('COALESCE(products.code, "-") as product_code'),
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_omzet'),
                DB::raw('SUM((order_items.price - COALESCE(products.cost_price, 0)) * order_items.quantity) as total_profit')
            )
            ->groupBy('order_items.product_name', 'products.code')
            ->orderByDesc('total_qty')
            ->get();

        $totalItemsSold = $itemSales->sum('total_qty');
        $totalProfit = $itemSales->sum('total_profit');

        return view('admin.reports.print', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalOrders',
            'totalItemsSold',
            'totalProfit',
            'itemSales'
        ));
    }
}
