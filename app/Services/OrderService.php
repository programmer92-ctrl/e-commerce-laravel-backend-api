<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPlaced;
use Exception;
use App\Exceptions\ProductOutOfStockException;
use App\Enums\ShippingMethod;

class OrderService {

    public function store(array $data): Order {

        return DB::transaction(function () use ($data) {

            $user = auth()->user();

            $cart = Cart::find($user->id);

            $order = $user->orders()->create($data);

            foreach($cart->cartItems as $cartItem) {
                
                if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                    
                    throw new ProductOutOfStockException('Product out of stock: ' . $cartItem->product->name);
                
                }
        
            }

            foreach($cart->cartItems as $cartItem){

                $order->orderItems()->create([
                    'product_id' => $cartItem->product->id,
                    'quantity' => $cartItem->quantity,
                    'product_name' => $cartItem->product->name,
                    'product_price' => $cartItem->product->price,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity,
                    'product_sku_code' => $cartItem->product->sku,
                ]);

                $product = Product::findOrFail($cartItem->product->id);
                $option = 'decrement';

                OrderPlaced::dispatch($product, $cartItem->quantity, $option);
            }

            $totalAmount = 0;

            foreach($order->orderItems as $orderItem){

                $totalAmount += $orderItem->subtotal;

            }

            $totalAmount += $order->tax_amount;
            
            if($order->shipping_method === ShippingMethod::Ground->value){

                $order->update(['shipping_cost' => 12.00]);
                $totalAmount += $order->shipping_cost;

            }

            if($order->shipping_method === ShippingMethod::Standard->value){

                $order->update(['shipping_cost' => 14.00]);
                $totalAmount += $order->shipping_cost;

            }

            if($order->shipping_method === ShippingMethod::Express->value){

                $order->update(['shipping_cost' => 16.00]);
                $totalAmount += $order->shipping_cost;

            }

            $order->update(['total_amount' => $totalAmount]);

            return $order;

        });

    }

    public function index(): LengthAwarePaginator {

        $order = Order::with('user', 'orderItems')->paginate(15);

        if ($order->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(Order::class);
        }

        return $order;

    }

    public function show(string $id): Order {

        $order = Order::with('orderItems')->findOrFail($id);

        return $order;

    }

    public function delete(string $id){

        $order = Order::findOrFail($id);
        $order->delete();

        return $order;
    }
 
    public function getOrderItemsForUser(string $id){

        $orders = Order::where('user_id', $id)->with('orderItems')->get();

        if ($orders->isEmpty()) {
            return $orders;;
        }

        return $orders;
        
    }


}

