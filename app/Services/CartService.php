<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\ProductOutOfStockException;
use App\Exceptions\ProductIsNotActiveException;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\ShippingMethod;

class CartService {

    public function getCart(): Cart {

        return  auth()->user()->cart()->firstOrCreate();

    }

    public function store(string $productId, int $quantity = 1): Cart {

        return DB::transaction(function () use ($productId, $quantity) {
        
            $product = Product::findOrFail($productId);
            $isActive = true;
            $inStock = true;

            if($this->isActive($product->is_active) == $isActive) {

                if($this->hasStock($product->stock_quantity, $quantity) == $inStock) {

                    $cart = $this->getCart();

                    $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

                    if ($cartItem) {

                        $cartItem->quantity += $quantity;
                        $cartItem->save();

                        return $cart;

                    } else {

                        $cart->cartItems()->create([
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                        ]);

                        return $cart;

                    }

                } else {

                    throw new ProductOutOfStockException('Product out of stock: ' . $product->name);

                }

            } else {

                throw new ProductIsNotActiveException('Product is not active: ' . $product->name);

            }

        });
    }

    public function show(string $id): Cart {

        $cart = Cart::with('user', 'cartItems')->findOrFail($id);

        return $cart;
        
    }

    public function index(): Collection {

        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->get();

        if ($cart->isEmpty()) {

            return $cart;

        }

        return $cart;

    }

    public function delete(string $productId): Cart {

        $cart = auth()->user()->cart();

        $product = Product::findOrFail($productId);

        $cart->cartItems()->where('product_id', $product->id)->delete();

        return $cart;

    }

    public function update(string $productId, int $quantity): Cart {
        
        return DB::transaction(function () use ($productId, $quantity) {

            $product = Product::findOrFail($productId);
            $isActive = true;
            $inStock = true;

            if($this->isActive($product->is_active) == $isActive) {

                if($this->hasStock($product->stock_quantity, $quantity) == $inStock) {

                    $cart = $this->getCart();

                    $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

                    if ($cartItem) {

                        if ($quantity > 0) {

                            $cartItem->quantity = $quantity;
                            $cartItem->save();

                            return $cart;

                        } else {

                            $cartItem->delete();

                            return $cart;

                        }

                    }

                    } else {

                        throw new ProductOutOfStockException('Product out of stock: ' . $product->name);

                    }

                } else {

                    throw new ProductIsNotActiveException('Product not active: ' . $product->name);
                }

        });

    }

    private function isActive(bool $active): bool {

        if($active == true) {

            return true;

        } else {

            return false;

        }

    }

    private function hasStock(int $productQuantity, int $quantity): bool {

        if($productQuantity >= $quantity) {

            return true;

        } else {

            return false;

        }

    }

    public function calculateTotalAmountForCart(string $shippingMethod, float $taxAmount): float {

        $cart = auth()->user()->cart()->with('cartItems')->get();

        $totalAmount = 0;

        foreach($cart->cartItems as $cartItem) {

            $totalAmount += $cartItem->product->price * $cartItem->quantity;

        }

        if($shippingMethod === ShippingMethod::Ground) {

            $totalAmount += 12.00;

        }

        if($shippingMethod === ShippingMethod::Standard) {

            $totalAmount += 14.00;

        }

        if($shippingMethod === ShippingMethod::Express) {

            $totalAmount += 16.00;

        }

        $totalAmount += $taxAmount;

        return $totalAmount;

    }

}
