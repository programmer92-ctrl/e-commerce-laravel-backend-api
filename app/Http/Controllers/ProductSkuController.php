<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSku;
use App\Services\ProductSkuService;
use Illuminate\Http\JsonResponse;

class ProductSkuController extends Controller
{
    //

    public function syncAttributeOptions(Request $request, ProductSkuService $productSkuService) {

        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'product_id' => 'exists:products,id',
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
            // Assuming the attribute options are sent as an array of IDs
            'attribute_options' => 'nullable|array',
            'attribute_options.*' => 'exists:attribute_options,id', // Ensure IDs exist
        ]);

        $optionIds = $request->input('attribute_options', []);

        // 2. Delegate the business logic to the Attribute Service
        $sku = $productSkuService->syncAttributeOptions($validatedData, $optionIds);

        // 3. Return a response
        return response()->json([
            'message' => 'Product SKU attributes updated successfully.',
            'sku' => $sku->only(['id', 'name']),
            'attributes' => $sku->attributeOptions->pluck('id', 'name'),
            'full sku' => $sku,
            'full sku attribute options' => $sku->attributeOptions,
        ]);

    }

    public function index(ProductSkuService $productSkuService) {

        $productSku = $productSkuService->index();

        return response()->json([
            'message' => 'Product sku retrieved successfully!',
            'product sku' => $productSku,
        ]);

    }

    public function show(ProductSkuService $productSkuService, string $id) {

        $productSku = $productSkuService->show($id);

        return response()->json([
            'message' => 'Product sku retrieved successfully!',
            'product sku' => $productSku,
        ]);

    }

}
