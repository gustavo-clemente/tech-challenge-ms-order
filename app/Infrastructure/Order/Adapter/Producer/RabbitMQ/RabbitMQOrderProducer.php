<?php

declare(strict_types=1);

namespace App\Infrastructure\Order\Adapter\Producer\RabbitMQ;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Port\Producer\OrderProducer;
use App\Infrastructure\Shared\Adapter\Producer\RabbitMQProducer;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQOrderProducer extends RabbitMQProducer implements OrderProducer
{
    public function publishOrderForPayment(Order $order): bool
    {
        $exchange = config("rabbitmq.awaiting_payment_exchange");
        $queue = config("rabbitmq.awaiting_payment_queue");
        $defaultKey = config("rabbitmq.default_key");

        $this->channel->exchange_declare($exchange, 'direct', false, true, false);

        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->queue_bind($queue, $exchange, $defaultKey);

        $orderMessage = new AMQPMessage(json_encode([
            'orderId' => $order->getOrderId()->getIdentifier()
        ]));

        $this->channel->basic_publish($orderMessage, $exchange, $defaultKey);

        return true;
    }

    public function publishOrderForPreparation(Order $order): bool
    {
        $exchange = config("rabbitmq.prepare_order_exchange");
        $queue = config("rabbitmq.prepare_order_queue");
        $defaultKey = config("rabbitmq.default_key");
        
        $this->channel->exchange_declare($exchange, 'fanout');

        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->queue_bind($queue, $exchange, $defaultKey);

        $orderMessage = new AMQPMessage(json_encode($order->jsonSerialize()));

        $this->channel->basic_publish($orderMessage, $exchange, $defaultKey);

        return true;
    }

    public function publishFinishedOrder(Order $order): bool
    {
        $exchange = config("rabbitmq.ready_order_exchange");
        $queue = config("rabbitmq.ready_order_queue");
        $defaultKey = config("rabbitmq.default_key");
        
        $this->channel->exchange_declare($exchange, 'fanout');

        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->queue_bind($queue, $exchange, $defaultKey);

        $orderMessage = new AMQPMessage(json_encode([
            'orderId' => $order->getOrderId()->getIdentifier(),
            'customerId' => $order->getOrderDetails()->getCustomerId()->getIdentifier()
        ]));

        $this->channel->basic_publish($orderMessage, $exchange, $defaultKey);

        return true;
    }
}
