<?php

declare(strict_types=1);

namespace App\Application\Order\Input;

use App\Domain\Order\ValueObject\OrderId;

class OrderIdInput
{
    public function __construct(
        private int|string $id
    ) {
    }

    public function getOrderId(): OrderId
    {
        return new OrderId($this->id);
    }
}
