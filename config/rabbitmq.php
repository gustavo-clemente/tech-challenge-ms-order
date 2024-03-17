<?php

return [
    'host' => env('RABBITMQ_HOST'),
    'vhost' => env('RABBITMQ_VHOST'),
    'port' => env('RABBITMQ_PORT'),
    'user' => env('RABBITMQ_USER'),
    'password' => env('RABBITMQ_PASS'),
    'default_key' => env('RABBITMQ_DEFAULT_KEY'),
    'awaiting_payment_queue' => env('RABBITMQ_AWAITING_PAYMENT_ORDER_QUEUE'),
    'awaiting_payment_exchange' => env('RABBITMQ_AWAITING_PAYMENT_ORDER_EXCHANGE'),
    'cancel_order_queue' => env('RABBITMQ_CANCEL_ORDER_QUEUE'),
    'paid_order_queue' => env('RABBITMQ_PAID_ORDER_QUEUE'),
    'prepare_order_queue' => env('RABBITMQ_PREPARE_ORDER_QUEUE'),
    'prepare_order_exchange' => env('RABBITMQ_PREPARE_ORDER_EXCHANGE'),
    'ready_order_queue' => env('RABBITMQ_READY_ORDER_QUEUE'),
    'ready_order_exchange' => env('RABBITMQ_READY_ORDER_EXCHANGE'),
];