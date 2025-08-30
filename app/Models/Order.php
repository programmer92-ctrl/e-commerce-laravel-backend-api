<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    //

    use SoftDeletes;

    //protected $guarded = [];

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'subtotal_amount',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'currency',
        'status',
        'payment_method',
        'payment_status',
        'shipping_method',
        'tracking_number',
        'notes',
    ];

    /*
     * Get the user that owns the order.
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
}
