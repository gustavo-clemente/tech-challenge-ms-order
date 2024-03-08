<?php

declare(strict_types= 1);

namespace App\Application\Order\Input;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateOrderInput
{
    public function __construct(
      private array $data
    ) {
        $this->validate();
    }

    public function getOrder(): Order
    {
        $orderDetails = $this->data['orderDetails'];

        return new Order(
            orderDetails: new OrderDetails(
                storeId: new StoreId($orderDetails['storeId']),
                items: $this->getOrderItemsCollection()
            )
        );
    }

    private function getOrderItemsCollection(): OrderItemCollection
    {
        $dataItems = $this->data['orderDetails']["items"];
        $orderItems = [];

        foreach($dataItems as $item){
            $orderItems[] = new OrderItem(
                productId: new ProductId($item['productId']),
                quantity: $item['quantity']
            );
        }

        return new OrderItemCollection($orderItems);
    }

    private function validate(): void
    {
        $validator = Validator::make($this->data, [
            "orderDetails" => "required|array",
            "orderDetails.storeId" => "required|string",
            "orderDetails.items" => "required|array",
            "items.*.productId" => "required|string",
            "items.*.quantity" => "required|int"
        ]);

        if($validator->fails()){
            throw new ValidationException($validator);
        }
    }
}
