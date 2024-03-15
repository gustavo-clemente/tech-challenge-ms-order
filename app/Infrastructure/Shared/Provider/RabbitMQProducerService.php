<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Provider;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQProducerService extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(AbstractConnection::class, function ($app) {
            return new AMQPStreamConnection(
                config('rabbitmq.host'),
                config('rabbitmq.port'),
                config('rabbitmq.user'),
                config('rabbitmq.password')
            );
        });
    }
}
