<?php

declare(strict_types=1);

namespace App\Application\Order\Handler;

use App\Application\Order\Handler\Output\RabbitMQCancelOrderOutput;
use App\Application\Order\Handler\Output\RabbitMQMessageExceptionOutput;
use App\Application\Shared\Handler\Output\RabbitMQMessageHandlerOutput;
use App\Application\Shared\Handler\RabbitMQMessageHandler;
use App\Domain\Order\Exception\OrderException;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\OrderId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQCancelOrderHandler implements RabbitMQMessageHandler
{
    public function __construct(
        private OrderService $orderService
    ) {
    }
    public function handler(AMQPMessage $message): RabbitMQMessageHandlerOutput
    {
        try {
            $messageBody = json_decode($message->getBody(), true);

            $this->validate($messageBody);

            $orderId = new OrderId($messageBody['orderId']);

            return new RabbitMQCancelOrderOutput($this->orderService->cancelOrder($orderId));

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
