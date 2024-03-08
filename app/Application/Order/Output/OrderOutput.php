<?php

declare(strict_types=1);

namespace App\Application\Order\Output;

use App\Domain\Order\Entity\Order;

class OrderOutput implements \JsonSerializable
{
    public function __construct(
        private Order $order,
    ) {

    }

    public function jsonSerialize(): array
    {
         return $this->order->jsonSerialize();
    }
}
