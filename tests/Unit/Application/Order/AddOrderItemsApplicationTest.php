<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order;

use App\Application\Order\AddOrderItemsApplication;
use App\Application\Order\Input\OrderItemsInput;
use App\Application\Order\Output\OrderOutput;
use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Order\ValueObject\OrderPaymentDetails;
use App\Domain\Payment\Enum\PaymentStatus;
use App\Domain\Payment\ValueObject\ExternalPaymentId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Mockery\MockInterface;
use Tests\TestCase;

class AddOrderItemsApplicationTest extends TestCase
{
    public function test_add_order_items_returns_correct_output(): void
    {
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId("111"),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity: 5
                    )
                ]),
                orderStatus: OrderStatus::AWAITING_PAYMENT,
                customerId: new CustomerId("111"),
                previsionDeliveryDate: new \DateTime(),
            ),
            createdAt: new \DateTime(),
            orderPaymentDetails: new OrderPaymentDetails(
                externalPaymentId: new ExternalPaymentId("111"),
                paymentStatus: PaymentStatus::PENDING,
            )

        );

        $data = [
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
        ];

        $orderIdInput = new OrderItemsInput('111', $data);

        $this->mock(OrderService::class, function (MockInterface $mock) use ($order): void {
            $mock
                ->shouldReceive('addOrderItems')
                ->andReturn($order);
        });

        $orderOutput = app(AddOrderItemsApplication::class)->addOrderItems($orderIdInput);

        $this->assertInstanceOf(OrderOutput::class, $orderOutput);
    }
}
