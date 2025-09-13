<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CartService {

    public function getCart(): Cart {

        return  auth()->user()->cart()->firstOrCreate();

    }

    public function store(string $productId, int $quantity = 1): Cart {

        return DB::transaction(function () use ($productId, $quantity) {
        
            $product = Product::findOrFail($productId);
            $isActive = true;
            
            if($product->is_active == $isActive) {

                if($product->stock_quantity >= $quantity) {

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

                    throw new Exception('Product out of stock: ' . $product->name);

                }

            } else {

                throw new Exception('Product is not active: ' . $product->name);

            }

        });
    }

    public function show(string $id): Cart {

        $cart = Cart::with('user', 'cartItems')->findOrFail($id);

        return $cart;
        
    }

    public function index(): Collection {

        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems')->get();

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
        
        return DB::transaction(function () use ($productId) {

            $product = Product::findOrFail($productId);
            $isActive = true;

            if($product->is_active == $isActive) {

                if($product->stock_quantity >= $quantity) {

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

                        throw new Exception('Product out of stock: ' . $product->name);

                    }

                } else {

                    throw new Exception('Product not active: ' . $product->name);
                }

        });

    }

    public function calculateTotalAmountForCart(array $data): float {

        $cart = auth()->user()->cart();

        $totalAmount = 0;

        foreach($cart->cartItems as $cartItem) {

            $totalAmount += $cartItem->product->price * $cartItem->quantity;

        }

        if($data['shippingCost'] === 'Ground') {

            $totalAmount += 12.00;

        }

        if($data['shippingCost'] === 'Standard') {

            $totalAmount += 14.00;

        }

        if($data['shippingCost'] === 'Express') {

            $totalAmount += 16.00;

        }

        $totalAmount += $data['tax_amount'];

        return $totalAmount;

    }

}
