<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CartService;

class CartController extends Controller
{
    //

    public function store(Request $request, CartService $cart): JsonResponse {

        $cart = $cart->store($request->productId, $request->quantity);

        return response()->json([
            'message' => 'item added to cart successfully',
            'cart' => $cart,
        ]);

    }

    public function show(CartService $cart, string $cartId): JsonResponse {

        $cart = $cart->show($cartId);

        return response()->json([
            'message' => 'cart',
            'cart' => $cart,
        ]);

    }

    public function index(CartService $cart): JsonResponse {

        $cart = $cart->index();

        return response()->json([
            'message' => 'cart',
            'cart' => $cart,
        ]);

    }

    public function update(Request $request, CartService $cart, string $productId): JsonResponse {

        $cart = $cart->update($productId, $request->quantity);

        return response()->json([
            'message' => 'cart updated',
            'cart' => $cart,
        ]);

    }

    public function delete(CartService $cart, string $productId): JsonResponse {

        $cart = $cart->delete($productId);

        return response()->json([
            'message' => 'item deleted from cart succussfully',
            'cart' => $cart,
        ]);

    }
}
