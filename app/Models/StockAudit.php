<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAudit extends Model
{
    use HasFactory;

    protected $table = 'stock_audits';

    protected $fillable = [
        'product_id',
        'user_id',
        'user_name',
        'type', // 'in', 'out', 'adjustment', 'sale'
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
