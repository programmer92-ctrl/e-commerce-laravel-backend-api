<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPlaced;

class OrderService {


    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Find the authenticated user
            $user = auth()->user();

            // Create the order
            $order = $user->orders()->create($data);

            // Iterate through order items and attach them to the order
            foreach ($data['order_items'] as $itemData) {

                $order->orderItems()->create($itemData);
                
            }

            $product = Product::findOrFail($itemData['product_id']);
            $option = 'decrement';

            OrderPlaced::dispatch($product, $itemData['quantity'], $option);

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
