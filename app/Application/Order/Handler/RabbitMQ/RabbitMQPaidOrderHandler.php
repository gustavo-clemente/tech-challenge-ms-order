<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\RabbitMQ;

use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQOrderOutput;
use App\Application\Shared\Handler\RabbitMQ\Output\RabbitMQMessageHandlerOutput;
use App\Domain\Order\ValueObject\OrderId;

class RabbitMQPaidOrderHandler extends RabbitMQOrderIdHandler
{
    protected function handlerAction(OrderId $orderId): RabbitMQMessageHandlerOutput
    {
        return new RabbitMQOrderOutput(
            $this->orderService->startOrderPreparation($orderId),
            "Order preparation started successfully"
        );
    }
}
