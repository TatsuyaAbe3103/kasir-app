<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\BankAccount;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'CAFE NUSA POS'),
            'store_address' => Setting::get('store_address', 'Jl. Melati Utama No. 88, Jakarta'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
            
            'tax_enabled' => Setting::get('tax_enabled', '1'),
            'tax_name' => Setting::get('tax_name', 'Pajak Restoran (PB1)'),
            'tax_percent' => Setting::get('tax_percent', '10'),

            'service_charge_enabled' => Setting::get('service_charge_enabled', '1'),
            'service_charge_name' => Setting::get('service_charge_name', 'Service Charge (Layanan)'),
            'service_charge_percent' => Setting::get('service_charge_percent', '5'),

            'qris_merchant_name' => Setting::get('qris_merchant_name', 'CAFE NUSA POS'),
            'qris_image' => Setting::get('qris_image', null),
            'qris_notes' => Setting::get('qris_notes', 'Scan QRIS di atas lalu pastikan nama merchant sesuai'),

            // Branding Aplikasi & Logo
            'app_brand_name' => Setting::get('app_brand_name', 'NUSAPOS'),
            'app_brand_tagline' => Setting::get('app_brand_tagline', 'Cafe & Resto System'),
            'app_brand_logo' => Setting::get('app_brand_logo', null),
        ];

        $bankAccounts = BankAccount::orderBy('id', 'asc')->get();

        return view('admin.settings.index', compact('settings', 'bankAccounts'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:50',
            
            'tax_name' => 'required|string|max:50',
            'tax_percent' => 'required|numeric|min:0|max:100',

            'service_charge_name' => 'required|string|max:50',
            'service_charge_percent' => 'required|numeric|min:0|max:100',

            'qris_merchant_name' => 'nullable|string|max:100',
            'qris_notes' => 'nullable|string|max:255',
            'qris_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',

            // Branding Aplikasi & Logo
            'app_brand_name' => 'required|string|max:50',
            'app_brand_tagline' => 'nullable|string|max:100',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        Setting::set('store_name', $validated['store_name']);
        Setting::set('store_address', $validated['store_address'] ?? '');
        Setting::set('store_phone', $validated['store_phone'] ?? '');

        Setting::set('tax_enabled', $request->has('tax_enabled') ? '1' : '0');
        Setting::set('tax_name', $validated['tax_name']);
        Setting::set('tax_percent', $validated['tax_percent']);

        Setting::set('service_charge_enabled', $request->has('service_charge_enabled') ? '1' : '0');
        Setting::set('service_charge_name', $validated['service_charge_name']);
        Setting::set('service_charge_percent', $validated['service_charge_percent']);

        Setting::set('qris_merchant_name', $validated['qris_merchant_name'] ?? '');
        Setting::set('qris_notes', $validated['qris_notes'] ?? '');

        // Upload / Hapus Foto QRIS Toko
        if ($request->has('remove_qris_image') && $request->remove_qris_image == '1') {
            $oldQris = Setting::get('qris_image');
            if ($oldQris && file_exists(public_path($oldQris))) {
                @unlink(public_path($oldQris));
            }
            Setting::set('qris_image', '');
        } elseif ($request->hasFile('qris_image_file')) {
            $file = $request->file('qris_image_file');
            $filename = 'qris_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/qris'), $filename);
            Setting::set('qris_image', 'uploads/qris/' . $filename);
        }

        // Simpan Branding & Nama Logo
        Setting::set('app_brand_name', $validated['app_brand_name']);
        Setting::set('app_brand_tagline', $validated['app_brand_tagline'] ?? '');

        // Upload Logo Baru / Hapus Logo
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            $oldLogo = Setting::get('app_brand_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            }
            Setting::set('app_brand_logo', '');
        } elseif ($request->hasFile('brand_logo')) {
            $file = $request->file('brand_logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logos'), $filename);
            Setting::set('app_brand_logo', 'uploads/logos/' . $filename);
        }

        return redirect()->back()->with('success', 'Pengaturan toko, logo, dan jaringan deploy berhasil disimpan!');
    }
}
