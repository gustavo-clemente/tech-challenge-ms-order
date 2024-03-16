<?php

return [
    'host' => env('RABBITMQ_HOST'),
    'vhost' => env('RABBITMQ_VHOST'),
    'port' => env('RABBITMQ_PORT'),
    'user' => env('RABBITMQ_USER'),
    'password' => env('RABBITMQ_PASS'),
    'default_key' => env('RABBITMQ_DEFAULT_KEY'),
    'awaiting_payment_queue' => env('RABBITMQ_AWAITING_PAYMENT_ORDER_QUEUE'),
    'awaiting_payment_exchange' => env('RABBITMQ_AWAITING_PAYMENT_ORDER_EXCHANGE')
];