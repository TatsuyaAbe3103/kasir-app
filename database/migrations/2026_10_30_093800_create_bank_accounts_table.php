<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name'); // e.g. "Bank Central Asia (BCA)"
            $table->string('bank_code', 20); // e.g. "BCA"
            $table->string('account_number', 50);
            $table->string('account_name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed data awal sesuai data pengguna
        DB::table('bank_accounts')->insert([
            [
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_code' => 'BCA',
                'account_number' => '8410967465',
                'account_name' => 'Muhammad Hadi Syahputra',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'bank_code' => 'MANDIRI',
                'account_number' => '137000987654',
                'account_name' => 'KEDAI KOPI NUSA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                'bank_code' => 'BRI',
                'account_number' => '012301009999',
                'account_name' => 'KEDAI KOPI NUSA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
