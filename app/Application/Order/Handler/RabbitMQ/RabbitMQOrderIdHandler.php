<?php

declare(strict_types=1);

namespace App\Application\Order\Handler\RabbitMQ;

use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQMessageExceptionOutput;
use App\Application\Shared\Handler\RabbitMQ\Output\RabbitMQMessageHandlerOutput;
use App\Application\Shared\Handler\RabbitMQ\RabbitMQMessageHandler;
use App\Domain\Order\Exception\OrderException;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\OrderId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RabbitMQOrderIdHandler implements RabbitMQMessageHandler
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    abstract protected function handlerAction(OrderId $orderId): RabbitMQMessageHandlerOutput;

    public function handler(AMQPMessage $message): RabbitMQMessageHandlerOutput
    {
        try {
            $messageBody = json_decode($message->getBody(), true);

            $this->validate($messageBody);

            $orderId = new OrderId($messageBody['orderId']);

            return $this->handlerAction($orderId);

        } catch (OrderException $exception) {
            $orderIdentifier = $orderId->getIdentifier();
            return new RabbitMQMessageExceptionOutput($exception->getMessage(). " Id: {$orderIdentifier}");
        }
        catch(ValidationException $exception) {
            $messageBody = $message->getBody();
            return new RabbitMQMessageExceptionOutput($exception->getMessage() . " Message: {$messageBody}");
        }
    }

    private function validate(array $messageBody): void
    {
        $validator = Validator::make($messageBody, [
            "orderId" => "required|string"
        ]);

        if($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
