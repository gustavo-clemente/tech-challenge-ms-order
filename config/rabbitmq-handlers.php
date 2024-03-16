<?php

use App\Application\Order\Handler\RabbitMQCancelOrderHandler;

return [
    env('RABBITMQ_CANCEL_ORDER_QUEUE') => RabbitMQCancelOrderHandler::class,
];