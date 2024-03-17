<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Handler\RabbitMQ\Output;

use App\Application\Order\Handler\RabbitMQ\Output\RabbitMQOrderOutput;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Tests\TestCase;

class RabbitMQOrderOutputTest extends TestCase
{
    public function test_get_response_returns_correct_response(): void
    {
        $orderId = new OrderId("111");
        $order = new Order(
            orderId: $orderId,
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

        $expectedMessage = 'test Message';

        $handlerOutput = new RabbitMQOrderOutput($order, $expectedMessage);
        $response = $handlerOutput->getResponse();
        
        $this->assertEquals($orderId->getIdentifier(), $response['orderId']);
        $this->assertEquals($expectedMessage, $response['message']);
    }
}
