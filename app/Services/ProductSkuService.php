<?php

namespace App\Services;

use App\Models\ProductSku;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ProductSkuService {

    /**
     * Attaches attribute options to a ProductSku, ensuring only valid options are used.
     *
     * @param ProductSku $sku The SKU to attach options to.
     * @param array|Collection $optionIds The IDs of the AttributeOptions.
     * @return ProductSku
     */
    public function syncAttributeOptions(array $data, $optionIds): ProductSku {

        // 1. Sanitize the input to ensure it's an array of unique integers
        $sanitizedOptionIds = collect($optionIds)->filter()->unique()->map(function ($id) {
            return (int) $id;
        })->all();

        return DB::transaction(function () use ($data, $sanitizedOptionIds) {

            $productSku = ProductSku::create($data);

            // 2. Sync the attribute options to the SKU.
            // The 'sync' method automatically manages insertions and deletions in the pivot table
            // so that the pivot table only contains the provided IDs for this SKU.
            $productSku->attributeOptions()->sync($sanitizedOptionIds);

            // 3. Optional: You might want to reload the relationship for immediate use
            $productSku->load('attributeOptions');

            return $productSku;

        });

    }

    /**
     * Detaches specific attribute options from a ProductSku.
     *
     * @param ProductSku $sku The SKU to detach options from.
     * @param array|Collection $optionIds The IDs of the AttributeOptions to detach.
     * @return ProductSku
     */
    public function detachAttributeOptions($sku, $optionIds): ProductSku {

        // Use the 'detach' method to remove specific entries from the pivot table.
        $sku->attributeOptions()->detach($optionIds);

        // Reload the relationship
        $sku->load('attributeOptions');

        return $sku;

    }

    public function show(string $id) {

        // 1. Find the specific ProductSku
       $sku = ProductSku::where('id', $id)->with('attributeOptions')->firstOrFail();

        // 2. Access the relationship as a property
        //$options = $sku->attributeOptions;

        return $sku;

        // $options is an Illuminate\Database\Eloquent\Collection of AttributeOption models
        /*foreach ($options as $option) {

            echo $option;
        
        }*/

    }

    public function index() {

        $skus = ProductSku::with('attributeOptions')->paginate(20);

        return $skus;

    }

    /**
     * Retrieves all attribute options for a specific attribute.
     *
     * @param int $attributeId The ID of the Attribute.
     * @return Collection
     */
    public function getOptionsByAttributeId(int $attributeId): Collection {

        return AttributeOption::where('attribute_id', $attributeId)->get();
    
    }

}