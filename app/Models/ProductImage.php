<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    //
    protected $guarded = [];
    
    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
