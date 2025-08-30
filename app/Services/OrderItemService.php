<?php

namespace App\Services;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderItemService {

    public function store(array $data){

        $orderItem = new OrderItem($data);

        return $orderItem;

    }

    public function show(string $id){

        $orderItem = OrderItem::findOrFail($id);

        return $orderItem;

    }

    public function index(): LengthAwarePaginator {

        $orderItem = OrderItem::paginate(15);

        if ($orderItem->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(OrderItem::class);
        }

        return $orderItem;

    }

    public function delete(string $id){

        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();

        return $orderItem;

    }

}