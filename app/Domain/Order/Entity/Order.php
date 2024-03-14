<?php

declare(strict_types= 1);

namespace App\Domain\Order\Entity;

use App\Domain\Order\Exception\EmptyOrderException;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Order\ValueObject\OrderPaymentDetails;
use DateTime;

class Order implements \JsonSerializable
{
    public function __construct(
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    private function validate(): void
    {
        $totalItems = $this->orderDetails->getTotalItems();

        if($totalItems === 0){
            throw new EmptyOrderException("An Order should have at least one item");
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'orderId' => $this->orderId->getIdentifier(),
            'orderDetails' => $this->orderDetails->jsonSerialize(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'orderPaymentDetails' => $this->orderPaymentDetails?->jsonSerialize()
         ];
    }
}
