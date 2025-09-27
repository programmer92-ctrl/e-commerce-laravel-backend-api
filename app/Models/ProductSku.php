<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSku extends Model
{
    //
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'image_path',
    ];

    public function products(): BelongsTo {

        return $this->belongsTo(Product::class);

    }

    public function attributeOptions(): BelongsToMany {
        return $this->belongsToMany(AttributeOption::class,
            'attribute_option_product_sku', // 1. Pivot Table Name
            'product_sku_id',               // 2. Foreign key of this model in the pivot table
            'attribute_option_id'           // 3. Foreign key of the other model in the pivot table
        )->withPivot('price_modifier');
    }
}
