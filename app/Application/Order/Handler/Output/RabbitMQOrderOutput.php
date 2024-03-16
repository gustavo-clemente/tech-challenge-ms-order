<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\Output;
use App\Application\Shared\Handler\Output\RabbitMQMessageHandlerOutput;
use App\Domain\Order\Entity\Order;

class RabbitMQOrderOutput implements RabbitMQMessageHandlerOutput
{   
    public function __construct(
        private Order $order,
        private string $message
    ) {

    }
    public function getResponse(): array
    {
        return [
            'message' => $this->message,
            'orderId' => $this->order->getOrderId()->getIdentifier()
        ];
    }
}
