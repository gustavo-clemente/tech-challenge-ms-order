<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\Output;
use App\Application\Shared\Handler\Output\RabbitMQMessageHandlerOutput;
use App\Domain\Order\Entity\Order;

class RabbitMQCancelOrderOutput implements RabbitMQMessageHandlerOutput
{   
    public function __construct(
        private Order $order,
    ) {

    }
    public function getResponse(): array
    {
        return [
            'message' => 'order successfully canceled',
            'orderId' => $this->order->getOrderId()->getIdentifier()
        ];
    }
}
