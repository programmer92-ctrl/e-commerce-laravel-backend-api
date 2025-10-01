<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\ProductOutOfStockException;
use App\Exceptions\ProductIsNotActiveException;
use App\Exceptions\QuantityExceedsStockException;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\ShippingMethod;

class CartService {

    public function store(string $productId, int $quantity = 0): Cart {

        $this->checkQuantity($quantity);

        return DB::transaction(function () use ($productId, $quantity) {

            $product = $this->getProductForCart($productId);

            $this->throwExceptions($product->stock_quantity, $product->is_active, $quantity, $product->name);

            $cart = $this->getCart();
            
            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

            if ($cartItem) {

                $cartItem->quantity += $quantity;
                $cartItem->save();
            
            } else {

                $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    //'product_sku_id' => $skuId,
                ]);

            }

            return $cart;

        });

    }

    public function show(Cart $cart, string $id): CartItem {

        $cart = Cart::with('user', 'cartItems')->findOrFail($cart->id);

        return $cart->cartItems()->where('id', $id)->with('product')->firstOrFail();
        
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

        $this->checkQuantity($quantity);

        return DB::transaction(function () use ($productId, $quantity) {

            $product = $this->getProductForCart($productId);

            $this->throwExceptions($product->stock_quantity, $product->is_active, $quantity, $productName);
        
            $cart = $this->getCart();

            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

                if ($cartItem) {

                    if ($quantity > 0) {

                        $cartItem->quantity = $quantity;
                        $cartItem->save();

                    } else {

                        $cartItem->delete();

                    }

                }

            return $cart;

        });

    }

    public function getCart(): Cart {

        return auth()->user()->cart()->firstOrCreate();

    }

    public function getProductForCart(string $productId): Product {

        return Product::where('id', $productId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->lockForUpdate()
            ->firstOrFail();

    }

    public function checkQuantity(int $quantity): void {

        if($quantity <= 0){

            throw new \InvalidArgumentException("Quantity must be greater than zero.");

        }

    }

    public function throwExceptions(int $stockQuantity, bool $isActive, int $requestedQuantity, string $prodouctName): void {

        if($stockQuantity <= 0){

            throw new ProductOutOfStockException('Product out of stock: ' . $productName);

        }

        if($isActive == false){

            throw new ProductIsNotActiveException('Product not active: ' . $productName);

        }

        if ($requestedQuantity > $stockQuantity) {

           throw new QuantityExceedsStockException('The requested quantity exceeds the available stock for ' . $productName);

        }

    }

    public function calculateTotalAmountForCart(string $shippingMethod, float $taxAmount): float {

        $cart = auth()->user()->cart()->with('cartItems.product')->first();

        $totalAmount = 0;

        foreach($cart->cartItems as $cartItem) {

            $totalAmount += $cartItem->product->price * $cartItem->quantity;
            //$totalAmount += $cartItem->skus->price * $cartItem->skus->quantity;

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
