<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'price',
        'cost_price',
        'stock',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'float',
        'cost_price' => 'float',
        'stock' => 'integer',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockAudits()
    {
        return $this->hasMany(StockAudit::class)->latest();
    }

    /**
     * Helper untuk mengubah stok & otomatis mencatat riwayat audit
     */
    public function logStockChange($qtyChange, $type, $reason, $notes = null, $user = null)
    {
        $stockBefore = (int) $this->stock;
        
        if ($type === 'adjustment') {
            // Opname: set langsung ke nilai baru
            $stockAfter = max(0, (int) $qtyChange);
            $diff = $stockAfter - $stockBefore;
        } else {
            // In or Out
            $stockAfter = max(0, $stockBefore + $qtyChange);
            $diff = $qtyChange;
        }

        $this->update([
            'stock' => $stockAfter,
            'is_available' => $stockAfter > 0,
        ]);

        return StockAudit::create([
            'product_id' => $this->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'user_name' => $user ? $user->name : (auth()->check() ? auth()->user()->name : 'Sistem'),
            'type' => $type,
            'quantity' => $diff,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }
}
