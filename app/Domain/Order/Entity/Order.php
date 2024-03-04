<?php

declare(strict_types= 1);

namespace App\Domain\Order\Entity;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Store\Entity\StoreId;

class Order
{
    public function __construct(
        private OrderId $orderId,
        private StoreId $storeId,
        private CustomerId $customerId,
        private OrderStatus $orderStatus
    ){
    }
}
