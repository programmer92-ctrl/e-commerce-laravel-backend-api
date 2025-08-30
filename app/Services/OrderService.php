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

            $order = $user->orders()->create($data);
            // Create the order
            /*$order = $user->orders()->create([
                'total_amount' => $data['total_amount'],
                'status' => $data['status'],
                'order_number' => $data['order_number'],
                'subtotal_amount' => $data['subtotal_amount'],
                'shipping_cost' => $data['shipping_cost'],
                'tax_amount' => $data['tax_amount'],
                'discount_amount' => $data['discount_amount'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'currency' => $data['currency'],
                'shipping_method' => $data['shipping_method'],
                'tracking_number' => $data['tracking_number'],
                'notes' => $data['notes'],
            ]);*/

            // Iterate through order items and attach them to the order
            foreach ($data['order_items'] as $itemData) {
                // You can add more logic here, like decrementing product inventory
                /*$order->orderItems()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'product_name' => $itemData['product_name'],
                    'product_sku_id' => $itemData['product_sku_id'],
                    'product_sku_code' => $itemData['product_sku_code'],
                    'product_price' => $itemData['product_price'],
                    'subtotal' => $itemData['subtotal'],
                ]);*/

                $order->orderItems()->create($itemData);

                /*$product = Product::findOrFail($itemData['product_id']);
                $option = 'decrement';

                OrderPlaced::dispatch($product, $itemData['quantity'], $option);*/

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

    public function update(array $validated_data, string $id){

        $order = Order::find($id);
        $order->shipping_address = $validated_data['shipping_address'];
        $order->billing_address = $validated_data['billing_address'];
        $order->order_number = $validated_data['order_number'];
        $order->total_amount = $validated_data['total_amount'];
        $order->status = $validated_data['status'];
        $order->save();

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

        /*foreach ($orders as $order) {

            if ($order->orderItems->isEmpty()) {
                return $order;
                continue;
            }

            return $order;

            foreach ($order->orderItems as $item) {

                return ['item_id' => $item->product_id, 'item_quantity' => $item->quantity, 'item_price' => $item->product_price, 'item_name' => $item->product_name];
            
            }
        }*/
    }

}