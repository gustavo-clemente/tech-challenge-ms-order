<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order;

use App\Application\Order\CreateOrderApplication;
use App\Application\Order\Input\CreateOrderInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Order\Service\OrderService;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateOrderApplicationTest extends TestCase
{
    public function test_create_order_returns_correct_output()
    {
        $data = [
            'orderDetails' => [
                'storeId' => '1',
                'items' => [
                    [
                        "productId" => '1',
                        "quantity" => 1
                    ],
                    [
                        "productId" => '2',
                        "quantity" => 1
                    ]
                ]
            ]
        ];

        $createOrderInput = new CreateOrderInput($data);

        $order = $createOrderInput->getOrder();

        $this->mock(OrderService::class, function(MockInterface $mock) use ($order){
            $mock
             ->shouldReceive('createOrder')
             ->andReturn($order);

        });

        $orderOutput = app(CreateOrderApplication::class)->createOrder($createOrderInput);

        $this->assertInstanceOf(OrderOutput::class, $orderOutput);
    }
}
