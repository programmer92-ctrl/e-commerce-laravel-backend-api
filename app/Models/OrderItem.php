<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    //
    protected $fillable = [
        'order_id',
        'product_id',
        'product_sku_id',
        'product_name',
        'product_sku_code',
        'product_price',
        'quantity',
        'subtotal',
    ];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function products(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }
    
}
