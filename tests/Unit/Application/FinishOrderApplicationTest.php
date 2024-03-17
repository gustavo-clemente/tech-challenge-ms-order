<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order;

use App\Application\Order\FinishOrderApplication;
use App\Application\Order\Input\OrderIdInput;
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
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class FinishOrderApplicationTest extends TestCase
{
    public function test_finish_order_returns_correct_output(): void
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
                orderStatus: OrderStatus::IN_PREPARATION,
                customerId: new CustomerId("111"),
                previsionDeliveryDate: new \DateTime(),
            ),
            createdAt: new \DateTime(),
            orderPaymentDetails: new OrderPaymentDetails(
                externalPaymentId: new ExternalPaymentId("111"),
                paymentStatus: PaymentStatus::PENDING,
            )

        );

        $orderIdInput = new OrderIdInput('111');

        $this->mock(OrderService::class, function (MockInterface $mock) use ($order, $orderIdInput)  : void  {
            $mock
              ->shouldReceive('finishOrderPreparation')
              ->with(Mockery::on(function (OrderId $argument) use ($orderIdInput) : bool {
                 return $argument->getIdentifier() === $orderIdInput->getOrderId()->getIdentifier();
              }))
              ->andReturn($order);
        });

        $orderOutput = app(FinishOrderApplication::class)->finishOrder($orderIdInput);

        $this->assertInstanceOf(OrderOutput::class, $orderOutput);
    }
}
