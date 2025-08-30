<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderItemService;
use App\Http\Requests\StoreOrderItemRequest;
use Illuminate\Http\JsonResponse;

class OrderItemController extends Controller
{
    //

    public function store(StoreOrderItemRequest $request, OrderItemService $orderItem): JsonRespnose {

        $orderItem = $orderItem->store($request->validated());

        return response()->json([
            'message' => 'Order Item added successfully',
            'order item' => $orderItem,
        ]);

    }

    public function show(OrderItemService $orderItem, string $id): JsonRespone {

        $orderItem = $orderItem->show($id);

        return response()->json([
            'message' => 'order item',
            'order item' => $orderItem,
        ]);

    }

    public function index(OrderItemService $orderItem): JsonResponse {

        $orderItem = $orderItem->index();

        return response()->json([
            'message' => 'order item',
            'order item' => $orderItem,
        ]);

    }

    public function delete(OrderItemService $orderItem, string $id): JsonResponse {

        $orderItem = $orderItem->delete($id);

        return response->json([
            'message' => 'order item deleted successfully',
            'order item' => $orderItem,
        ]);

    }
}
