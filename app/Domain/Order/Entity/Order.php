<?php

declare(strict_types= 1);

namespace App\Domain\Order\Entity;

use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Order\ValueObject\OrderPaymentDetails;
use App\Domain\Store\Entity\StoreId;
use DateTime;

class Order
{
    public function __construct(
        private StoreId $storeId,
        private OrderDetails $orderDetails,
        private ?OrderId $orderId,
        private ?DateTime $createdAt,
        private ?OrderPaymentDetails $orderPaymentDetails

    ){
    }
}
