<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Provider;

use App\Domain\Order\Port\Producer\OrderProducer;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Infrastructure\Order\Adapter\Producer\RabbitMQ\RabbitMQOrderProducer;
use App\Infrastructure\Order\Adapter\Repository\Eloquent\EloquentOrderRepository;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
   public $bindings = [
    OrderRepository::class => EloquentOrderRepository::class,
    OrderProducer::class => RabbitMQOrderProducer::class
   ];
}
