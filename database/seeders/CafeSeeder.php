<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Table;
use App\Models\Product;

class CafeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Meja Cafe
        $tables = [
            ['name' => 'Meja 01', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'Meja 02', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Meja 03', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Meja 04', 'capacity' => 6, 'status' => 'available'],
            ['name' => 'Meja 05', 'capacity' => 2, 'status' => 'available'],
            ['name' => 'VIP Lounge', 'capacity' => 8, 'status' => 'available'],
            ['name' => 'Outdoor 01', 'capacity' => 4, 'status' => 'available'],
            ['name' => 'Outdoor 02', 'capacity' => 4, 'status' => 'available'],
        ];

        foreach ($tables as $t) {
            Table::firstOrCreate(['name' => $t['name']], $t);
        }

        // 2. Kategori Menu
        $categories = [
            ['name' => 'Signature Coffee', 'slug' => 'coffee', 'icon' => '☕'],
            ['name' => 'Non-Coffee & Tea', 'slug' => 'non-coffee', 'icon' => '🍵'],
            ['name' => 'Main Course', 'slug' => 'food', 'icon' => '🍛'],
            ['name' => 'Snacks & Pastry', 'slug' => 'snacks', 'icon' => '🥐'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $created = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
            $catMap[$cat['slug']] = $created->id;
        }

        // 3. Menu Produk
        $products = [
            // Kopi
            [
                'category_id' => $catMap['coffee'],
                'code' => 'COF-01',
                'name' => 'Kopi Susu Aren Spesial',
                'description' => 'Espresso robusta & arabika dengan susu segar dan gula aren asli.',
                'price' => 22000,
                'cost_price' => 9000,
                'image' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['coffee'],
                'code' => 'COF-02',
                'name' => 'Caramel Macchiato',
                'description' => 'Espresso dengan steamed milk dan saus caramel premium.',
                'price' => 28000,
                'cost_price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['coffee'],
                'code' => 'COF-03',
                'name' => 'Caffe Latte Creamy',
                'description' => 'Single shot espresso dengan microfoam susu yang lembut.',
                'price' => 25000,
                'cost_price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['coffee'],
                'code' => 'COF-04',
                'name' => 'Americano On The Rocks',
                'description' => 'Double shot espresso dingin yang segar dan kuat.',
                'price' => 19000,
                'cost_price' => 6000,
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],

            // Non Kopi
            [
                'category_id' => $catMap['non-coffee'],
                'code' => 'BEV-01',
                'name' => 'Matcha Kyoto Latte',
                'description' => 'Bubuk matcha murni dari Uji dengan susu segar.',
                'price' => 26000,
                'cost_price' => 11000,
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['non-coffee'],
                'code' => 'BEV-02',
                'name' => 'Signature Dark Chocolate',
                'description' => 'Cokelat Belgia pekat dengan taburan bubuk cocoa.',
                'price' => 24000,
                'cost_price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['non-coffee'],
                'code' => 'BEV-03',
                'name' => 'Peach Sparkling Tea',
                'description' => 'Teh melati dingin dengan sirup peach dan soda segar.',
                'price' => 21000,
                'cost_price' => 7000,
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],

            // Makanan Berat
            [
                'category_id' => $catMap['food'],
                'code' => 'FOD-01',
                'name' => 'Nasi Goreng Spesial Cafe',
                'description' => 'Nasi goreng harum rempah lengkap dengan telur mata sapi & kerupuk.',
                'price' => 32000,
                'cost_price' => 14000,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['food'],
                'code' => 'FOD-02',
                'name' => 'Spaghetti Creamy Carbonara',
                'description' => 'Pasta al dente dengan saus keju parmesan dan smoked beef.',
                'price' => 36000,
                'cost_price' => 16000,
                'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['food'],
                'code' => 'FOD-03',
                'name' => 'Chicken Katsu Sambal Matah',
                'description' => 'Fillet ayam krispi disajikan dengan nasi hangat dan sambal matah segar.',
                'price' => 35000,
                'cost_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],

            // Cemilan
            [
                'category_id' => $catMap['snacks'],
                'code' => 'SNK-01',
                'name' => 'Truffle French Fries',
                'description' => 'Kentang goreng renyah dengan aroma truffle oil dan taburan keju.',
                'price' => 23000,
                'cost_price' => 9000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['snacks'],
                'code' => 'SNK-02',
                'name' => 'Butter Croissant Crispy',
                'description' => 'Pastry mentega renyah keemasan khas Perancis.',
                'price' => 22000,
                'cost_price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
            [
                'category_id' => $catMap['snacks'],
                'code' => 'SNK-03',
                'name' => 'Platter Mix Cafe',
                'description' => 'Kombinasi sosis panggang, nugget, dan kentang goreng saus BBQ.',
                'price' => 29000,
                'cost_price' => 13000,
                'image' => 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&auto=format&fit=crop&q=60',
                'is_available' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['code' => $p['code']], $p);
        }
    }
}
