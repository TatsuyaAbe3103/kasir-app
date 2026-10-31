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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('deleted_by')->nullable()->after('cashier_name');
            $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by');
            $table->text('delete_reason')->nullable()->after('deleted_by_id');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['deleted_by', 'deleted_by_id', 'delete_reason']);
        });
    }
};
