<?php

namespace App\Infrastructure\Order\Adapter\Producer;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Port\Producer\CheckoutOrderProducer;
use App\Infrastructure\Shared\Adapter\RabbitMQProducer;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQCheckoutOrderProducer extends RabbitMQProducer implements CheckoutOrderProducer
{
    public function publish(Order $order): void
    {
        $this->channel->queue_declare(config("rabbitmq.awaiting_payment_queue"), false, true, false, false);

        $orderMessage = new AMQPMessage(json_encode([
            'orderId' => $order->getOrderId()->getIdentifier(),
            'amount' => $order->getOrderDetails()->getTotalAmountInReal()
        ]));

        $this->channel->basic_publish($orderMessage, config("rabbitmq.awaiting_payment_exchange"), config("rabbitmq.default_key"));
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
    
}