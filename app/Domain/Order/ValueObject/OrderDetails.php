<?php

declare(strict_types= 1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Enum\OrderStatus;
use DateTime;

class OrderDetails
{
    public function __construct(
        private OrderItemCollection $items,
        private ?OrderStatus $orderStatus = null,
        private ?CustomerId $customerId = null,
        private ?DateTime $previsionDeliveryDate = null,
    ){
        
    }

    public function getItems(): OrderItemCollection
    {
        return $this->items;
    }

    public function getTotalItems(): int
    {
        return $this->items->count();
    }

    public function getOrderStatus(): OrderStatus
    {
        return $this->orderStatus;
    }
}
