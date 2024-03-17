<?php

declare(strict_types= 1);

namespace App\Application\Order\Input;

use App\Application\Order\Mapper\OrderItemMapper;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\ValueObject\OrderId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderItemsInput
{
    public function __construct(
        private int|string $id,
        private array $data
    ) {
        $this->validate();
    }

    public function getOrderId(): OrderId
    {
        return new OrderId($this->id);
    }

    public function getOrderItems(): OrderItemCollection
    {
        $dataItems = $this->data["items"];
        
        return OrderItemMapper::mapToCollection($dataItems);
    }

    private function validate(): void
    {
        $validator = Validator::make($this->data, [
            "items" => "required|array",
            "items.*.productId" => "required|string",
            "items.*.quantity" => "required|int"
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
