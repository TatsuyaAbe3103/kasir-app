<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom stock pada tabel products jika belum ada
        if (!Schema::hasColumn('products', 'stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock')->default(50)->after('price');
            });
        }

        // 2. Buat tabel stock_audits untuk rekap riwayat barang masuk, keluar, dan opname
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name', 100)->nullable();
            $table->enum('type', ['in', 'out', 'adjustment', 'sale']); // masuk, keluar/rusak, opname, penjualan
            $table->integer('quantity'); // jumlah penambahan/pengurangan
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason', 150); // e.g. "Pembelian Stok Bahan", "Barang Rusak / Basi", "Stok Opname Fisik", "Penjualan Kasir"
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Tambahkan User Baru Khusus Auditor / Gudang
        $exists = DB::table('users')->where('email', 'audit@nusa.com')->exists();
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Staff Audit & Gudang',
                'email' => 'audit@nusa.com',
                'password' => Hash::make('password'),
                'role' => 'auditor',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_audits');
        if (Schema::hasColumn('products', 'stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
        DB::table('users')->where('email', 'audit@nusa.com')->delete();
    }
};
