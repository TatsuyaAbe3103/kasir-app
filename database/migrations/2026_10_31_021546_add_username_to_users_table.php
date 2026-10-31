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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
        });

        // Set default usernames for existing seeded users
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $username = match ($user->role) {
                'admin' => 'admin',
                'cashier' => 'kasir',
                'auditor' => 'audit',
                default => explode('@', $user->email)[0] ?? ('user' . $user->id)
            };

            // Ensure unique username
            $exists = \DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                $username .= $user->id;
            }

            \DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
