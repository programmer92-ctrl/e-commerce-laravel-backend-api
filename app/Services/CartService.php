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
            $productValidated = true;
            
            if($this->validateProductStatus($product->is_active, $product->stock_quantity, $quantity, $product->name) == $productValidated){

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

            }   

        });
    }

    public function show(string $id): Cart {

        $cart = Cart::with('user', 'cartItems')->findOrFail($id);

        return $cart;
        
    }

    public function index(): Collection {

        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems.product')->get();

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
            $productValidated = true;

            if($this->validateProductStatus($product->is_active, $product->stock_quantity, $quantity, $product->name) == $productValidated){

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
            }


        });

    }

    private function isActive(bool $active): bool {

        return $active;

    }

    private function hasStock(int $productQuantity, int $quantity): bool {

        if($productQuantity >= $quantity) {

            return true;

        } else {

            return false;

        }

    }

    private function validateProductStatus(bool $active, int $productQuantity, int $quantity, string $product): bool {

        $isActive = true;
        $inStock = true;

        if($this->isActive($active) == $isActive) {

                if($this->hasStock($productQuantity, $quantity) == $inStock) {

                    return true;

                    } else {

                        throw new ProductOutOfStockException('Product out of stock: ' . $product->name);

                    }

                } else {

                    throw new ProductIsNotActiveException('Product not active: ' . $product->name);
                }

    }

    public function calculateTotalAmountForCart(string $shippingMethod, float $taxAmount): float {

        $cart = auth()->user()->cart()->with('cartItems.product');

        $totalAmount = 0;

        foreach($cart->cartItems as $cartItem) {

            $totalAmount += $cartItem->product->price * $cartItem->quantity;

        }

        if($shippingMethod === ShippingMethod::Ground->value) {

            $totalAmount += 12.00;

        }

        if($shippingMethod === ShippingMethod::Standard->value) {

            $totalAmount += 14.00;

        }

        if($shippingMethod === ShippingMethod::Express->value) {

            $totalAmount += 16.00;

        }

        $totalAmount += $taxAmount;

        return $totalAmount;

    }

}


