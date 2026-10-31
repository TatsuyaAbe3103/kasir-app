<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kategori Menu (Coffee, Non-Coffee, Food, Snacks, dll)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('coffee'); // nama icon / emoji
            $table->timestamps();
        });

        // 2. Meja Cafe / Resto
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Meja 01, Meja 02, VIP-1
            $table->integer('capacity')->default(4); // Kapasitas kursi
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->timestamps();
        });

        // 3. Menu / Produk
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('code')->unique(); // Kode SKU misal: COF-001
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2); // Harga jual
            $table->decimal('cost_price', 12, 2)->default(0); // Harga modal (untuk hitung laba bersih)
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true); // Stok ready / habis
            $table->timestamps();
        });

        // 4. Pesanan / Transaksi (Orders)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Contoh: INV-20261030-0001
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->string('customer_name')->default('Pelanggan');
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in');
            
            // Status alur dapur & kasir
            $table->enum('status', ['pending', 'cooking', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'qris', 'transfer', 'debit'])->nullable();
            
            // Perhitungan harga
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_percent', 5, 2)->default(0); // Pajak resto misal 10%
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0); // Uang yang diterima kasir
            $table->decimal('change_amount', 12, 2)->default(0); // Kembalian
            
            $table->text('notes')->nullable(); // Catatan umum pesanan
            $table->string('cashier_name')->default('Kasir');
            $table->timestamps();
        });

        // 5. Rincian Item Pesanan (Order Items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->string('notes')->nullable(); // Misal: "Less sugar, es sedikit, pedas level 2"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('categories');
    }
};
