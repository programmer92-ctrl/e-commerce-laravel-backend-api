<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'user_id' => 'required|numeric',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.product_name' => 'required|string|max:255',
            'order_items.*.product_sku_id' => 'integer',
            'order_items.*.product_name' => 'string|max:255',
            'order_items.*.product_sku_code' => 'string|max:255',
            'order_items.*.product_price' => 'numeric',
            'order_items.*.quantity' => 'numeric',
            'order_items.*.subtotal' => 'numeric',
            'order_number' => 'required|string|max:255',
            'total_amount' => 'required|numeric',
            'subtotal_amount' => 'required|numeric',
            'shipping_cost' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'currency' => 'string|max:255',
            'status' => 'string|max:255',
            'payment_method' => 'required|string|max:255',
            'payment_status' => 'required|string|max:255',
            'shipping_method' => 'string|max:255',
            'tracking_number' => 'string|max:255',
            'notes' => 'string|max:255',
        ];
    }
}
