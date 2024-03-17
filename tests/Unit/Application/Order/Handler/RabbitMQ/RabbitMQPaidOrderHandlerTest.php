<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Handler\RabbitMQ;

use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQMessageExceptionOutput;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\OrderId;
use Mockery;
use Mockery\MockInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Tests\TestCase;
use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQOrderOutput;
use App\Application\Order\Handler\RabbitMQ\RabbitMQCancelOrderHandler;
use App\Application\Order\Handler\RabbitMQ\RabbitMQPaidOrderHandler;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Exception\OrderException;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;


class RabbitMQPaidOrderHandlerTest extends TestCase
{
    public function test_handler_returns_correct_output(): void
    {
        $orderId = '111';

        $order = new Order(
            orderId: new OrderId($orderId),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity: 1
                    ),
                    new OrderItem(
                        productId: new ProductId('222'),
                        quantity: 1
                    )
                ])
            )
        );

        $message = new AMQPMessage(json_encode([
            "orderId" => $orderId
        ]));

        $this->mock(OrderService::class, function (MockInterface $mock) use ($orderId, $order) {
            $mock
                ->shouldReceive("startOrderPreparation")
                ->once()
                ->with(Mockery::on(function (OrderId $argument) use ($orderId) {
                    return $argument->getIdentifier() === $orderId;
                }))
                ->andReturn($order)
            ;
        });

        $output = app(RabbitMQPaidOrderHandler::class)->handler($message);

        $this->assertInstanceOf(RabbitMQOrderOutput::class, $output);
    }
    public function test_handler_returns_error_output_when_message_is_invalid(): void
    {
        $orderId = '111';

        $message = new AMQPMessage(json_encode([
            "invalidField" => $orderId
        ]));

        $this->mock(OrderService::class, function (MockInterface $mock) {
            $mock
                ->shouldNotReceive("startOrderPreparation");
        });

        $output = app(RabbitMQPaidOrderHandler::class)->handler($message);

        $this->assertInstanceOf(RabbitMQMessageExceptionOutput::class, $output);
    }

    public function test_handler_returns_error_output_when_throw_order_exception(): void
    {
        $orderId = '111';

        $message = new AMQPMessage(json_encode([
            "orderId" => $orderId
        ]));

        $this->mock(OrderService::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive("startOrderPreparation")
                ->andThrow(OrderException::class);
        });

        $output = app(RabbitMQPaidOrderHandler::class)->handler($message);

        $this->assertInstanceOf(RabbitMQMessageExceptionOutput::class, $output);
    }
}
