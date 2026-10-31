<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:100',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon promo baru berhasil dibuat!');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'name' => 'required|string|max:100',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil dihapus!');
    }

    public function toggle($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);

        $statusStr = $coupon->is_active ? 'Aktif' : 'Nonaktif';
        return redirect()->back()->with('success', "Status kupon '$coupon->code' diubah menjadi $statusStr.");
    }
}
