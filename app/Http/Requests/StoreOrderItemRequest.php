<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
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
            'order_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'product_sku_id' => 'numeric',
            'product_name' => 'required|string|max:255',
            'product_sku_code' => 'string|max:255',
            'product_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ];
    }
}
