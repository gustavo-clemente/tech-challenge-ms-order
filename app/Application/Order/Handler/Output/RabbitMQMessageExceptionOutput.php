<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\Output;
use App\Application\Shared\Handler\Output\RabbitMQMessageHandlerOutput;
use App\Application\Shared\Handler\RabbitMQMessageHandler;

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
