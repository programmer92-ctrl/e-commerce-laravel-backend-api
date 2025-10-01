<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CartService;
use App\Models\Cart;

class CartController extends Controller
{
    //

    public function store(Request $request, Cart $cart, CartService $cartService): JsonResponse {

        $this->authorize('create', $cart);

        $myCart = $cartService->store($request->productId, $request->quantity);

        return response()->json([
            'message' => 'item added to cart successfully',
            'cart' => $myCart,
        ]);

    }

    public function show(Cart $cart, string $id, CartService $cartService): JsonResponse {

        $this->authorize('view', $cart);

        //$myCart = $cartService->show($cartId);
        $myCart = $cartService->show($cart, $id);

        return response()->json([
            'message' => 'cart',
            'cart' => $myCart,
        ]);

    }

    public function index(Cart $cart, CartService $cartService): JsonResponse {

        $this->authorize('viewAny', $cart);


        $myCart = $cartService->index();

        return response()->json([
            'message' => 'cart',
            'cart' => $myCart,
        ]);

    }

    public function update(Request $request, Cart $cart, CartService $cartService, string $productId): JsonResponse {

        $this->authorize('update', $cart);

        $myCart = $cartService->update($productId, $request->quantity);

        return response()->json([
            'message' => 'cart updated',
            'cart' => $myCart,
        ]);

    }

    public function delete(Cart $cart, CartService $cartService, string $productId): JsonResponse {

        $this->authorize('delete', $cart);

        $myCart = $cartService->delete($productId);

        return response()->json([
            'message' => 'item deleted from cart succussfully',
            'cart' => $myCart,
        ]);

    }
}
