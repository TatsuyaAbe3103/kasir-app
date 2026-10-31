<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Daftar Seluruh Menu
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form Tambah Menu Baru
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan Menu Baru (Mendukung Upload File Gambar & Link URL)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'image_url' => 'nullable|string|max:500',
            'is_available' => 'boolean',
        ]);

        $imagePath = null;

        // 1. Cek apakah ada file gambar yang diunggah langsung dari komputer
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = '/uploads/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            // 2. Atau jika menggunakan link URL
            $imagePath = $request->image_url;
        }

        $productData = [
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'image' => $imagePath,
            'is_available' => $request->has('is_available'),
        ];

        Product::create($productData);

        return redirect()->route('admin.products.index')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Form Edit Menu
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update Menu
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'image_url' => 'nullable|string|max:500',
            'is_available' => 'boolean',
        ]);

        $imagePath = $product->image;

        // Cek jika unggah file foto baru
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = '/uploads/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'image' => $imagePath,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus Menu
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Menu '$productName' berhasil dihapus!");
    }

    /**
     * Toggle status ketersediaan (Ready / Habis)
     */
    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_available' => !$product->is_available]);

        $statusStr = $product->is_available ? 'Tersedia' : 'Habis';
        return redirect()->back()->with('success', "Status menu '$product->name' diubah menjadi $statusStr.");
    }
}
