<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    //
    
    public function store(StoreProductRequest $request, ProductService $product): JsonResponse {

        //$path = $request->file('image')->store('images', 'public');
        
        $product = $product->store($request->validated());

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product,
            ]);
    }

    public function getProductsByCategory(ProductService $product, string $category_name): JsonResponse {

        $product = $product->getProductsByCategory($category_name);
        return response()->json([
            'message' => 'Products by category retrieved successfully!',
            'products' => $product,
        ]);

    }

    public function index(ProductService $product): JsonResponse {

        $product = $product->index();

        return response()->json([
            'message' => 'Products retrieved successfully!',
            'products' => $product,
        ]);

    }

    public function show(ProductService $product, string $id): JsonResponse {

        $product = $product->show($id);
        return response()->json([
            'message' => 'Product retrieved successfully!',
            'product' => $product,
        ]);

    }

    public function delete(ProductService $product, string $id): JsonResponse {

        $product = $product->delete($id);
        return response()->json([
            'message' => 'Product deleted succussfully!',
            'product' => $product,
        ]);

    }

    public function update(Request $request, string $id): JsonResponse {

        $validated_data = $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required',
            'price' => 'required|numeric',
        ]);
        $path = $request->file('image')->store('images', 'public');
        $product = (new ProductService)->update($id, $validated_data, $path);
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $product,
        ]);

    }

    public function searchProducts(Request $request, ProductService $productService): JsonResponse {

        $filters = $request->all();

        $product = $productService->searchProducts($filters);

        return response()->json([
            'message' => 'Search for product request successful!',
            'product' => $product,
        ]);

    }

}
