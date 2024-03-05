<?php

declare(strict_types= 1);

namespace App\Domain\Order\Entity;

use App\Domain\Order\Exception\EmptyOrderException;
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
        private ?OrderId $orderId = null,
        private ?DateTime $createdAt = null,
        private ?OrderPaymentDetails $orderPaymentDetails = null

    ){
        $this->validate();
    }

    public function getOrderId(): ?OrderId
    {
        return $this->orderId;
    }

    public function getOrderDetails(): OrderDetails
    {
        return $this->orderDetails;
    }

    private function validate(): void
    {
        $totalItems = $this->orderDetails->getTotalItems();

        if($totalItems === 0){
            throw new EmptyOrderException("An Order should have at least one item");
        }
    }
}
