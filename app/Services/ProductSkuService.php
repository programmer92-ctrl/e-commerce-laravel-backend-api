<?php

namespace App\Services;

use App\Models\ProductSku;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ProductSkuService {

    public function syncAttributeOptions(array $data, $optionIds): ProductSku {

        $sanitizedOptionIds = collect($optionIds)->filter()->unique()->map(function ($id) {
            return (int) $id;
        })->all();

        return DB::transaction(function () use ($data, $sanitizedOptionIds) {

            $productSku = ProductSku::create($data);

            $productSku->attributeOptions()->sync($sanitizedOptionIds);

            $productSku->load('attributeOptions');

            return $productSku;

        });

    }
    
    public function detachAttributeOptions($sku, $optionIds): ProductSku {

        $sku->attributeOptions()->detach($optionIds);

        $sku->load('attributeOptions');

        return $sku;

    }

    public function show(string $id) {

       $sku = ProductSku::where('id', $id)->with('attributeOptions', 'attributeOptions.attribute')->firstOrFail();
        
        return $sku;

    }

    public function index() {

        $skus = ProductSku::with('attributeOptions', 'attributeOptions.attribute')->paginate(20);

        return $skus;

    }
    
    public function getOptionsByAttributeId(int $attributeId): Collection {

        return AttributeOption::where('attribute_id', $attributeId)->get();
    
    }


}


