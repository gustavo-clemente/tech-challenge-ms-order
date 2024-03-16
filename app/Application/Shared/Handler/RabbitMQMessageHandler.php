<?php

declare(strict_types=1);

namespace App\Application\Shared\Handler;

use PhpAmqpLib\Message\AMQPMessage;

interface RabbitMQMessageHandler
{
    public function handler(AMQPMessage $message): void;
}
