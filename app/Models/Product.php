<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'sku',
        'name',
        'price',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
