<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Provider;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;

class RabbitMQProducerService extends ServiceProvider implements DeferrableProvider
{

    public function register()
    {
        $this->app->singleton(AbstractConnection::class, function (Application $app) {
            return new AMQPStreamConnection(
                config('rabbitmq.host'),
                config('rabbitmq.port'),
                config('rabbitmq.user'),
                config('rabbitmq.password'),
                config('rabbitmq.vhost')
            );
        });
    }

    public function provides(): array
    {
        return [AbstractConnection::class];
    }

    public function isDeferred(): bool
    {
        return true;
    }
}
