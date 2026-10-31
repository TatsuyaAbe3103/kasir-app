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
        // 1. Tabel Pengaturan Cafe / Biaya Restoran
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Kupon / Promo Diskon
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. DISKON10, HEMAT20K
            $table->string('name'); // e.g. Promo Grand Opening
            $table->enum('type', ['percent', 'fixed'])->default('percent'); // Persen atau Nominal Tetap
            $table->decimal('value', 12, 2); // 10 (%) atau 20000 (Rp)
            $table->decimal('min_order', 12, 2)->default(0); // Syarat minimal belanja
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tambahan kolom biaya di tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('service_charge_percent', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('service_charge_amount', 12, 2)->default(0)->after('service_charge_percent');
            $table->string('coupon_code')->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_charge_percent', 'service_charge_amount', 'coupon_code']);
        });
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('settings');
    }
};
