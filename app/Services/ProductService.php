<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService {

    public function store(array $data): Product {

        return DB::transaction(function () use ($data) {

            $product_to_create = Product::create($data);

            $category = Category::findOrFail($data['category_id']);
            $product = $category->products()->save($product_to_create);

            if (isset($data['images'])) {

                foreach ($data['images'] as $imageFile) {

                    $path = $imageFile->store('images', 'public');
                    $product->productImages()->create(['image_path' => $path]);

                }
            }

            return $product;
        });

    }

    public function show(string $id): Product {

        return Product::with('images')->findOrFail($id);

    }

    public function index(): LengthAwarePaginator {

        $product = Product::paginate(20);

        if ($product->isEmpty()) {

            throw (new ModelNotFoundException)->setModel(Product::class);
        
        }

        return $product;

    }

    public function delete(string $id): Product {

        $product = Product::findOrFail($id);

        return DB::transaction(function () use ($product) {

            foreach ($product->images as $image) {

                Storage::disk('public')->delete($image->image_url);
                $image->delete();

            }

            return $product->delete();

        });
    }

    public function update(array $data, string $id): Product {

        return DB::transaction(function () use ($data) {

            $product_to_update = Product::findOrFail($id);
            $product_to_update->update($data);

            $category = Category::findOrFail($data['category_id']);
            $product = $category->products()->save($product_to_create);

            if (isset($data['images'])) {

                foreach ($data['images'] as $imageFile) {

                    $path = $imageFile->store('images', 'public');
                    $product->productImages()->create(['image_path' => $path]);

                }
            }

            return $product;
        });

    }

    public function getProductsByCategory(string $category_name): LengthAwarePaginator {

        $category = Category::where('name', $category_name)->firstOrFail();

        return $category->products()->with('category')->paginate(30);

    }

    public function searchProducts(array $data): LengthAwarePaginator {

        $query = Product::with('category');

        $query->when(isset($data['name']), function ($q) use ($data) {

            $q->where('name', 'like', '%' . $data['name'] . '%');

        })->when(isset($data['description']), function ($q) use ($data) {
            
            $q->where('description', 'like', '%' . $data['description'] . '%');
        
        })->when(isset($data['category']), function ($q) use ($data) {
            
            $q->whereHas('category', function ($q) use ($data) {
                
                $q->where('name', 'like', '%' . $data['category'] . '%');
            
            });
        
        })->when(isset($data['min_price']), function ($q) use ($data) {
        
            $q->where('price', '>=', $data['min_price']);
    
        })->when(isset($data['max_price']), function ($q) use ($data) {

            $q->where('price', '<=', $data['max_price']);
    
        });

        if (isset($data['sort_by'])) {

            switch ($data['sort_by']) {

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            
            }

        }

        $perPage = $data['per_page'] ?? 15;

        return $query->paginate($perPage);

    }

    public function checkStockForProduct(string $id): bool {

        $product = Product::where('id', $id)
        ->where('stock_quantity', '>', 0)->firstOrFail();

        if($product){

            return true;

        } else {

            throw new ProductOutOfStockException('Product out of stock: ' . $product->name);

        }

    }

    public function applyDiscount(string $id, float $percentage): Product {

        $product = Product::findOrFail($id);

        if ($percentage <= 0 || $percentage > 100) {

            throw new \InvalidArgumentException('Percentage is not valid! Percentage needs be greater than 0, or less than 100.');

        }

        $discount_amount = $product->price * ($percentage / 100);
        $new_price = $product->price - $discount_amount;

        $product->price = max(0, $new_price);
        $product->save();

        return $product;
    }

    public function makeProductFeaturedOrNotFeatured(string $id, bool $featured): Product {

        $product = Product::findOrFail($id);

        $product->is_featured = $featured;
        $product->save();

        return $product;

    }

    public function getAllFeaturedProducts(): LengthAwarePaginator {


        $product = Product::where('is_featured', true)
        ->with('category')
        ->paginate(30);

        if ($product->isEmpty()) {

            throw (new ModelNotFoundException)->setModel(Product::class);
        
        }

        return $product;

    }

    public function makeProductActiveOrNotActive(string $id, bool $active): Product {

        $product = Product::findOrFail($id);

        $product->is_active = $active;
        $product->save();

        return $product;

    }

    public function getAllActiveProducts(): LengthAwarePaginator {

        $product = Product::where('is_active', true)
        ->with('category')
        ->paginate(30);

        if ($product->isEmpty()) {

            throw (new ModelNotFoundException)->setModel(Product::class);
        
        }

        return $product;

    }

    public function forceModifyOrUpdateStockQuantity(string $id, int $amount): Product {

        $product = Product::findOrFail($id);

        $product->stock_quantity = $amount;
        $product->save();

        return $product;

    }

}

