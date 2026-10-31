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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('cashier_name');
            $table->dateTime('opened_at');
            $table->decimal('starting_cash', 14, 2)->default(0); // Modal kas awal

            $table->dateTime('closed_at')->nullable();
            $table->decimal('actual_cash', 14, 2)->nullable(); // Uang fisik di laci saat closing
            $table->decimal('expected_cash', 14, 2)->nullable(); // Kas awal + Penjualan tunai
            $table->decimal('difference', 14, 2)->nullable(); // actual_cash - expected_cash (selisih lebih / kurang)

            $table->decimal('total_sales', 14, 2)->default(0);
            $table->decimal('total_cash_sales', 14, 2)->default(0);
            $table->decimal('total_qris_sales', 14, 2)->default(0);
            $table->decimal('total_transfer_sales', 14, 2)->default(0);
            $table->decimal('total_discount', 14, 2)->default(0);
            $table->decimal('total_tax', 14, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->integer('total_void_transactions')->default(0);

            $table->string('closed_by_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // Tambah foreign column shift_id ke orders
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'shift_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('shift_id')->nullable()->after('id')->constrained('cashier_shifts')->nullOnDelete();
                $table->string('split_bill_code')->nullable()->after('order_number'); // misal BILL-1, BILL-2
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'shift_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['shift_id']);
                $table->dropColumn(['shift_id', 'split_bill_code']);
            });
        }
        Schema::dropIfExists('cashier_shifts');
    }
};
