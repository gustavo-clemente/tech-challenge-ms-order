<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Adapter;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

abstract class RabbitMQProducer
{
    protected AMQPChannel $channel;

    public function __construct(
        protected AbstractConnection $connection
    ) {
        $this->channel = $this->connection->channel();
    }
}
