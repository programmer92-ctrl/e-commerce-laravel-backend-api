<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;

class CartService {


    public function getCart() {

        return  auth()->user()->cart()->firstOrCreate();

    }

    public function store(string $productId, int $quantity = 1) {
        
        $product = Product::findOrFail($productId);

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

    public function show(string $id): Cart {

        $cart = Cart::with('user', 'cartItems')->findOrFail($id);

        return $cart;
    }

    public function index() {

        $cart = Cart::where('user_id', auth()->user()->id)->with('cartItems')->get();

        if ($cart->isEmpty()) {

            return $cart;

        }

        return $cart;

    }

    public function delete(string $productId) {

        $cart = $this->getCart();
        $product = Product::findOrFail($productId);

        $cart->cartItems()->where('product_id', $product->id)->delete();

        return $cart;

    }

    public function update(string $productId, int $quantity) {
        
        $cart = $this->getCart();

        $product = Product::findOrFail($productId);

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

}