<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cashier_name',
        'opened_at',
        'starting_cash',
        'closed_at',
        'actual_cash',
        'expected_cash',
        'difference',
        'total_sales',
        'total_cash_sales',
        'total_qris_sales',
        'total_transfer_sales',
        'total_discount',
        'total_tax',
        'total_transactions',
        'total_void_transactions',
        'closed_by_name',
        'notes',
        'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'starting_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_qris_sales' => 'decimal:2',
        'total_transfer_sales' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shift_id');
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }
}
