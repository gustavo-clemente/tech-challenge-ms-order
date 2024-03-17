<?php

declare(strict_types=1);

namespace App\Application\Shared\Handler\RabbitMQ;

use App\Application\Shared\Handler\RabbitMQ\Output\RabbitMQMessageHandlerOutput;
use PhpAmqpLib\Message\AMQPMessage;

interface RabbitMQMessageHandler
{
    public function handler(AMQPMessage $message): RabbitMQMessageHandlerOutput;
}
