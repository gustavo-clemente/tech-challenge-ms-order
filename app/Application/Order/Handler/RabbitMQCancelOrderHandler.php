<?php

declare(strict_types=1);

namespace App\Application\Order\Handler;
use App\Application\Shared\Handler\RabbitMQMessageHandler;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\OrderId;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQCancelOrderHandler implements RabbitMQMessageHandler
{
    public function __construct( 
        private OrderService $orderService
    ) {

    }
   public function handler(AMQPMessage $message): void
   {
      $messageBody = json_decode($message->getBody());
      
      $orderId = new OrderId($messageBody->orderId);

      $this->orderService->cancelOrder($orderId);
   }
}
