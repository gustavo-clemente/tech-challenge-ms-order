<?php

declare(strict_types= 1);

namespace App\Application\Order\Input;

use App\Application\Mapper\OrderItemMapper;
use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderDetails;
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
                customerId: new CustomerId($orderDetails['customerId']),
                items: $this->getOrderItemsCollection()
            )
        );
    }

    private function getOrderItemsCollection(): OrderItemCollection
    {
        $dataItems = $this->data['orderDetails']["items"];
        
        return OrderItemMapper::mapToCollection($dataItems);
    }

    private function validate(): void
    {
        $validator = Validator::make($this->data, [
            "orderDetails" => "required|array",
            "orderDetails.storeId" => "required|string",
            "orderDetails.customerId" => "required|string",
            "orderDetails.items" => "required|array",
            "items.*.productId" => "required|string",
            "items.*.quantity" => "required|int"
        ]);

        if($validator->fails()){
            throw new ValidationException($validator);
        }
    }
}
