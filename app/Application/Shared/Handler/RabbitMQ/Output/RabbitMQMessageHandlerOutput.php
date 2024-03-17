<?php

declare(strict_types=1);

namespace App\Application\Shared\Handler\RabbitMQ\Output;

interface RabbitMQMessageHandlerOutput
{
    public function getResponse(): array;
}
