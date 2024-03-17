<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Output;

use App\Application\Order\Output\OrderOutput;
use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Order\ValueObject\OrderPaymentDetails;
use App\Domain\Payment\Enum\PaymentStatus;
use App\Domain\Payment\ValueObject\ExternalPaymentId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Tests\TestCase;

class OrderOutputTest extends TestCase
{
    public function test_json_serialize_return_correct_ouutput(): void
    {
        $items = new OrderItemCollection([
            new OrderItem(
                id: new OrderItemId("111"),
                productId: new ProductId("1"),
                quantity: 2,
                priceInCents: 250000,
                discountInCents: 120000
            ),
            new OrderItem(
                id: new OrderItemId("222"),
                productId: new ProductId("2"),
                quantity: 2,
                priceInCents: 250000,
                discountInCents: 120000
            )
        ]);

        $itemsArray = iterator_to_array($items);

        $orderDetails = new OrderDetails(
            storeId: new StoreId("111"),
            items: $items,
            orderStatus: OrderStatus::AWAITING_PAYMENT,
            customerId: new CustomerId("111"),
            previsionDeliveryDate: new \DateTime(),
        );

        $orderPaymentDetails = new OrderPaymentDetails(
            externalPaymentId: new ExternalPaymentId("444"),
            paymentStatus: PaymentStatus::PENDING,
        );

        $order = new Order(
            orderId: new OrderId('111'),
            orderDetails: $orderDetails,
            createdAt: new \DateTime(),
            orderPaymentDetails: $orderPaymentDetails
        );

        $orderOutput = new OrderOutput($order);
        $jsonOutput = $orderOutput->jsonSerialize();
        $jsonOrderDetails = $jsonOutput['orderDetails'];
        $jsonOrderItems = $jsonOrderDetails['items'];
        $jsonOrderPaymentDetails = $jsonOutput['orderPaymentDetails'];

        $this->assertEquals($order->getOrderId()->getIdentifier(), $jsonOutput['orderId']);
        $this->assertEquals($order->getCreatedAt()->format('Y-m-d H:i:s'), $jsonOutput['createdAt']);
        $this->assertEquals($orderDetails->getStoreId()->getIdentifier(), $jsonOrderDetails['storeId']);
        $this->assertCount($orderDetails->getItems()->count(), $jsonOrderDetails['items']);
        $this->assertEquals($orderDetails->getOrderStatus()->value, $jsonOrderDetails['orderStatus']);
        $this->assertEquals($orderDetails->getCustomerId()->getIdentifier(), $jsonOrderDetails['customerId']);
        $this->assertEquals(
            $orderDetails->getPrevisionDeliveryDate()->format('Y-m-d H:i:s'),
            $jsonOrderDetails['previsionDeliveryDate']
        );
        $this->assertEquals($orderDetails->getTotalAmountInCents(), $jsonOrderDetails['amountInCents']);
        $this->assertEquals($orderDetails->getTotalAmountInReal(), $jsonOrderDetails['amountInReal']);

        $this->assertEquals(
            $jsonOrderItems[0]['orderItemId'],
            $itemsArray[0]->getId()->getIdentifier()
        );

        $this->assertEquals(
            $jsonOrderItems[0]['productId'],
            $itemsArray[0]->getProductId()->getIdentifier()
        );

        $this->assertEquals(
            $jsonOrderItems[0]['quantity'],
            $itemsArray[0]->getQuantity()
        );

        $this->assertEquals(
            $jsonOrderItems[0]['priceInCents'],
            $itemsArray[0]->getPriceInCents()
        );

        $this->assertEquals(
            $jsonOrderItems[0]['discountInCents'],
            $itemsArray[0]->getDiscountInCents()
        );

        $this->assertEquals(
            $jsonOrderPaymentDetails['externalPaymentId'],
            $orderPaymentDetails->getExternalPaymentId()->getIdentifier()
        );

        $this->assertEquals(
            $jsonOrderPaymentDetails['paymentStatus'],
            $orderPaymentDetails->getPaymentStatus()->value
        );

    }
}
