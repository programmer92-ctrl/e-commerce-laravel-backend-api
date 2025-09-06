<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPlaced;

class OrderService {

    public function store(array $data): Order {

        return DB::transaction(function () use ($data) {

            $user = auth()->user();

            $cart = Cart::find($user->id);

            $order = $user->orders()->create($data);

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
            
            foreach($order->orderItems as $orderItem){

                $order->total_amount += $orderItem->subtotal;
                $order->save();

            }

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

