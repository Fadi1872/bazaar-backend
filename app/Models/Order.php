<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'address_id',
        'status',
        'subtotal',
        'delivery_fee',
        'taxes',
        'total_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate and update totals based on items.
     */
    public function calculateTotals(float $deliveryFee = 0, float $taxRate = 0): void
    {
        $subtotal = $this->items->sum(fn($item) => $item->total);
        $taxes = $taxRate > 0 ? $subtotal * $taxRate : 0;
        $total = $subtotal + $deliveryFee + $taxes;

        $this->update([
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'taxes' => $taxes,
            'total_price' => $total,
        ]);
    }
}
