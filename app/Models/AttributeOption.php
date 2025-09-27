<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeOption extends Model
{
    //

    protected $fillable = [
        'attribute_id',
        'value',
        'display_value',
    ];

    public function attribute(): BelongsTo {

        return $this->belongsTo(Attribute::class);

    }

    public function productSkus(): BelongsToMany {
        return $this->belongsToMany(ProductSku::class, 
            'attribute_option_product_sku', // 1. Pivot Table Name
            'attribute_option_id',          // 2. Foreign key of this model in the pivot table
            'product_sku_id'               // 3. Foreign key of the other model in the pivot table
        )->withPivot('price_modifier');
    }
}
