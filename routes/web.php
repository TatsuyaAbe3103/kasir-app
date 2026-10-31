<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\GuestOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\ShiftController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root Redirect
Route::get('/', function () {
    if (auth()->check() && auth()->user()->isAuditor()) {
        return redirect()->route('inventory.index');
    }
    return redirect()->route('pos.index');
});

// ==============================================================
// PUSAT PANDUAN & DEMO SHOWCASE
// ==============================================================
Route::get('/guide', [GuideController::class, 'index'])->name('guide.index');
Route::get('/panduan', [GuideController::class, 'index']);
Route::get('/try', [GuideController::class, 'index']);
Route::post('/demo/reset', [GuideController::class, 'resetDemo'])->name('demo.reset');

// ==============================================================
// PUBLIC: PESAN MANDIRI VIA QR MEJA (SELF-ORDERING DARI HP TAMU)
// ==============================================================
Route::get('/m/{table}', [GuestOrderController::class, 'menu'])->name('guest.menu');
Route::post('/m/{table}/order', [GuestOrderController::class, 'storeOrder'])->name('guest.order');
Route::get('/m/{table}/pay/{order}', [GuestOrderController::class, 'paymentPage'])->name('guest.pay');
Route::post('/m/{table}/pay/{order}/confirm', [GuestOrderController::class, 'confirmPayment'])->name('guest.pay.confirm');
Route::get('/m/{table}/success/{order}', [GuestOrderController::class, 'orderSuccess'])->name('guest.success');

// ==============================================================
// KASIR & OPERASIONAL MEJA (Bisa diakses Kasir & Admin)
// ==============================================================
Route::middleware(['auth'])->group(function () {
    // Shift Kasir & Cash Drawer
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shift.index');
    Route::get('/shifts/current', [ShiftController::class, 'current'])->name('shift.current');
    Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shift.open');
    Route::post('/shifts/{id}/close', [ShiftController::class, 'close'])->name('shift.close');
    Route::get('/shifts/{id}/print-open', [ShiftController::class, 'printOpeningReceipt'])->name('shift.print.open');
    Route::get('/shifts/{id}/print-close', [ShiftController::class, 'printClosingReceipt'])->name('shift.print.close');

    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order.store');
    Route::get('/pos/receipt/{id}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/kitchen/{id}', [PosController::class, 'kitchenTicket'])->name('pos.kitchen');
    Route::get('/pos/printer-test', [PosController::class, 'printerTest'])->name('pos.printer.test');

    Route::get('/orders', [PosController::class, 'orders'])->name('pos.orders');
    Route::post('/orders/{id}/status', [PosController::class, 'updateOrderStatus'])->name('pos.orders.update');
    Route::delete('/orders/{id}', [PosController::class, 'destroyOrder'])->name('pos.orders.destroy');

    Route::get('/tables', [PosController::class, 'tables'])->name('pos.tables');
    Route::post('/tables', [PosController::class, 'storeTable'])->name('pos.tables.store');
    Route::put('/tables/{id}', [PosController::class, 'updateTable'])->name('pos.tables.update');
    Route::delete('/tables/{id}', [PosController::class, 'destroyTable'])->name('pos.tables.destroy');
    Route::post('/tables/{id}/reset', [PosController::class, 'resetTable'])->name('pos.tables.reset');
    Route::get('/tables/{table}/qr', [GuestOrderController::class, 'printTableQR'])->name('tables.qr');
    Route::get('/tables-qr/print-all', [GuestOrderController::class, 'printAllTablesQR'])->name('tables.qr.all');
});

// ==============================================================
// AREA AUDIT STOK & INVENTARIS (Bisa diakses Auditor & Admin)
// ==============================================================
Route::middleware(['auth', 'auditor'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
    Route::post('/{id}/toggle', [InventoryController::class, 'toggleAvailability'])->name('toggle');
    Route::get('/logs', [InventoryController::class, 'logs'])->name('logs');
    Route::get('/deleted-orders', [InventoryController::class, 'deletedOrders'])->name('deleted_orders');
});

// ==============================================================
// AREA KHUSUS ADMIN (Kelola Menu, Biaya Resto, Kupon, & Laporan)
// ==============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Kelola Menu
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');

    // Kelola Biaya Resto (PB1 & Service Charge)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Kelola Rekening Bank & Virtual Account (VA) untuk Meja Tamu
    Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank_accounts.store');
    Route::put('/bank-accounts/{id}', [BankAccountController::class, 'update'])->name('bank_accounts.update');
    Route::post('/bank-accounts/{id}/toggle', [BankAccountController::class, 'toggle'])->name('bank_accounts.toggle');
    Route::delete('/bank-accounts/{id}', [BankAccountController::class, 'destroy'])->name('bank_accounts.destroy');

    // Kelola Kupon & Diskon
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{id}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    Route::post('/coupons/{id}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');

    // Laporan Penjualan & Rekap Terjual
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'printReport'])->name('reports.print');

    // Kelola Pengguna & Hak Akses (User Management)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/password', [UserController::class, 'changePassword'])->name('users.password');
    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});
