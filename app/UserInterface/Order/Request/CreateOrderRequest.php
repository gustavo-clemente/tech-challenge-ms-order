<?php

declare(strict_types=1);

namespace App\UserInterface\Order\Request;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "orderDetails" => "required|array",
            "orderDetails.storeId" => "required|string",
            "orderDetails.items" => "required|array",
            "items.*.productId" => "required|string",
            "items.*.quantity" => "required|int"
        ];
    }
}
