<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Provider;

use App\Domain\Order\Port\Producer\CheckoutOrderProducer;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Infrastructure\Order\Adapter\Producer\RabbitMQCheckoutOrderProducer;
use App\Infrastructure\Order\Adapter\Repository\Eloquent\EloquentOrderRepository;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
   public $bindings = [
    OrderRepository::class => EloquentOrderRepository::class,
    CheckoutOrderProducer::class => RabbitMQCheckoutOrderProducer::class
   ];
}
