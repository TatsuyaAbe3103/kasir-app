<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'min_order' => 'float',
        'is_active' => 'boolean',
    ];

    public function calculateDiscount($subtotal)
    {
        if (!$this->is_active || $subtotal < $this->min_order) {
            return 0;
        }

        if ($this->type === 'percent') {
            return ($subtotal * $this->value) / 100;
        }

        return min($this->value, $subtotal);
    }
}
