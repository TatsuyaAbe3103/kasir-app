<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'shift_id',
        'split_bill_code',
        'table_id',
        'customer_name',
        'order_type',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_percent',
        'tax_amount',
        'service_charge_percent',
        'service_charge_amount',
        'discount_amount',
        'coupon_code',
        'total_amount',
        'paid_amount',
        'change_amount',
        'notes',
        'cashier_name',
        'deleted_by',
        'deleted_by_id',
        'delete_reason',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax_percent' => 'float',
        'tax_amount' => 'float',
        'service_charge_percent' => 'float',
        'service_charge_amount' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
        'deleted_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shift()
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }
}
