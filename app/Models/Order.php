<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'invoice_number',
        'purchaser_id',
        'order_date',
    ];

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Convenience: compute order total (price * qty)
     * Note: price is stored in products; order_items may store price override.
     */
    public function getOrderTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->price ?? ($item->product?->price ?? 0);
            return $price * ($item->quantity ?? 0);
        });
    }
}
