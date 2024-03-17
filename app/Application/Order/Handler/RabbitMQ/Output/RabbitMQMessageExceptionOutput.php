<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\RabbitMQ\Output;
use App\Application\Shared\Handler\RabbitMQ\Output\RabbitMQMessageHandlerOutput;

class RabbitMQMessageExceptionOutput implements RabbitMQMessageHandlerOutput
{
    public function __construct(
        private string $message,
    ) {

    }
    public function getResponse(): array
    {
        return [
            "error" => $this->message
        ];
    }
}
