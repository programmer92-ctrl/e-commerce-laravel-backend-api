<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    //

    public function store(StoreOrderRequest $request, OrderService $order): JsonResponse {
    
        $order = $order->store($request->validated());

        return response()->json([
            'message' => 'Order added successfully',
            'order' => $order,
        ]);

    }

    public function index(OrderService $order): JsonResponse {

        $order = $order->index();

        return response()->json([
            'message' => 'Order',
            'order' => $order,
        ]);

    }

    public function show(OrderService $order, string $id): JsonResponse {

        $order = $order->show($id);

        return response()->json([
            'message' => 'Order',
            'order' => $order,
        ]); 

    }

    public function update(Request $request, array $validated_data, string $id){

        $validated_data = $request->validate([
            'product_id' => 'required|numeric',
            'shipping_address' => 'required|string|max:255',
            'billing_address' => 'required|string|max:255',
            'order_number' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
        ]);
        $order = (new OrderService)->update($validated_data, $id);
        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);

    }

    public function delete(OrderService $order, string $id): JsonResponse {

        $order = $order->delete($id);
        return response()->json([
            'message' => 'Order deleted succesfully!',
            'order' => $order,
        ]);

    }

    public function getOrderItemsForUser(OrderService $order, string $userId): JsonResponse {

        $orderItemsForUser = $order->getOrderItemsForUser($userId);

        return response()->json([
            'message' => 'order',
            'order' => $orderItemsForUser,
        ]);

    }

}
