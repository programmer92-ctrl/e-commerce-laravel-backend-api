<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService {

    public function store(array $data): Product {

        /*$product_to_save = new Product($data);

        $category = Category::findOrFail($data['category_id']);
        $product = $category->products()->save($product_to_save);
        
        return $product;*/

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

    public function getProductsByCategory(string $category_name): Collection {

        $category = Category::where('name', $category_name)->firstOrFail();
        return $category->products()->with('category')->get();

    }

    public function getProduct(int $id) {

        return Product::with('images')->findOrFail($id);
    }

    public function show(string $id): Product {

        $product = Product::findOrFail($id);
        
        return $product;

    }

    public function index(): LengthAwarePaginator {

        $product = Product::paginate(15);

        if ($product->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(Product::class);
        }

        return $product;

    }

    public function delete(string $id) {
        $product = Product::findOrFail($id);

        return DB::transaction(function () use ($product) {

            foreach ($product->images as $image) {

                Storage::disk('public')->delete($image->image_url);
                $image->delete();

            }

            return $product->delete();

        });
    }

    /*public function delete(string $id){

        $product = Product::findOrFail($id);
        
        $product->delete();

        return $product;

    }*/

    public function update(string $id, array $data, string $image){

        $product = Product::find($id);
        $product->title = $data['title'];
        $product->desc = $data['desc'];
        $product->price = $data['price'];
        $product->photo = $image;
        $product->save();

        return $product;
    }

    public function checkStock(string $id): bool {
        $in_stock = null;

        $product = Product::findOfFail($id);
        $product_stock = $product->stock_quantity;

        if($product_stock > 0){
            $in_stock = true;
        }

        if($product_stock == 0){
            $in_stock = false;
        }

        return $in_stock;

    }

    public function updateProductStock(string $id, int $amount): bool {

        $product = Product::findOrFail($id);

        if($amount < 0) {

            return false;

        } else {

            $product->stock_quantity += $amount;
            $product->save();

            return true;

        }

    }

    public function applyDiscount(string $id, float $percentage): bool {

        $product = Product::findOrFail($id);

        if ($percentage <= 0 || $percentage > 100) {

            return false;

        }

        $discount_amount = $product->price * ($percentage / 100);
        $new_price = $product->price - $discount_amount;

        $product->price = max(0, $new_price);
        $product->save();

        return true;
    }

    public function makeProductFeatured(string $id): bool {

        $product = Product::findOrFail($id);

        $product->is_featured = true;
        $product->save();

        return true;

    }

    public function getAllFeaturedProducts(): Collection {

        $featured_products = Product::where('is_featured', true)->with('category')->get();

        return $featured_products;

    }

}