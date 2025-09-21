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

    public function store(string $productId, int $quantity = 0): Cart {

        if($quantity <= 0) {

            throw new InvalidArgumentException('Quantity must be greater than zero.');

        }

        return DB::transaction(function () use ($productId, $quantity) {

            $product = $this->getProductForCart($productId);

            $this->throwExceptions($product->stock_quantity, $product->is_active, $product->name);

            $cart = $this->getCart();
            
            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

            if ($cartItem) {

                $cartItem->quantity += $quantity;
                $cartItem->save();
            
            } else {

                $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);

            }

            return $cart;

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

    public function update(string $productId, int $quantity = 0): Cart {

        if($quantity <= 0){

            throw new InvalidArgumentException("Quantity must be greater than zero.");

        }

        return DB::transaction(function () use ($productId, $quantity) {

            $product = $this->getProductForCart($productId);

            $this->throwExceptions($product->stock_quantity, $product->is_active, $product->name);
        
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

    }

    public function getCart(): Cart {

        return  auth()->user()->cart()->firstOrCreate();

    }

    public function getProductForCart(string $productId): Product {

        return Product::where('id', $productId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->firstOrFail();

    }

    public function throwExceptions($stockQuanity, $isActive, $productName): void {

        if($stockQuanity <= 0){

                throw new ProductOutOfStockException('Product out of stock: ' . $productName);

            }

            if($isActive === false){

                throw new ProductIsNotActiveException('Product not active: ' . $productName);

            }

    }

    public function calculateTotalAmountForCart(string $shippingMethod, float $taxAmount): float {

        $cart = auth()->user()->cart()->with('cartItems.product')->first();

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
