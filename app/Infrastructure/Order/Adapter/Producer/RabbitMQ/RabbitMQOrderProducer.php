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
        $this->channel->queue_declare(config("rabbitmq.awaiting_payment_queue"), false, true, false, false);

        $orderMessage = new AMQPMessage(json_encode([
            'orderId' => $order->getOrderId()->getIdentifier(),
            'amount' => $order->getOrderDetails()->getTotalAmountInReal()
        ]));

        $this->channel->basic_publish($orderMessage, config("rabbitmq.awaiting_payment_exchange"), config("rabbitmq.default_key"));

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
}
