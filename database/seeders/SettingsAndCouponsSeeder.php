<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Coupon;

class SettingsAndCouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pengaturan Default Toko & Biaya Resto
        $settings = [
            'store_name' => 'CAFE NUSA POS',
            'store_address' => 'Jl. Melati Utama No. 88, Jakarta',
            'store_phone' => '0812-3456-7890',
            
            // Pajak Resto (PB1)
            'tax_enabled' => '1',
            'tax_name' => 'Pajak Restoran (PB1)',
            'tax_percent' => '10',

            // Service Charge (Biaya Layanan Resto / Cafe)
            'service_charge_enabled' => '1',
            'service_charge_name' => 'Service Charge (Biaya Layanan)',
            'service_charge_percent' => '5',
        ];

        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        // 2. Kupon Promo Awal
        $coupons = [
            [
                'code' => 'DISKON10',
                'name' => 'Promo Diskon 10%',
                'type' => 'percent',
                'value' => 10,
                'min_order' => 50000,
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT15K',
                'name' => 'Potongan Langsung 15 Ribu',
                'type' => 'fixed',
                'value' => 15000,
                'min_order' => 60000,
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME',
                'name' => 'Voucher Tamu Baru Rp 10.000',
                'type' => 'fixed',
                'value' => 10000,
                'min_order' => 30000,
                'is_active' => true,
            ]
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
