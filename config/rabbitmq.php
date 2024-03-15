<?php

return [
    'host' => env('RABBITMQ_HOST'),
    'port' => env('RABBITMQ_PORT'),
    'user' => env('RABBITMQ_USER'),
    'password' => env('RABBITMQ_PASS'),
    'checkout_queue' => env('RABBITMQ_CHECKOUT_ORDER_QUEUE'),
];