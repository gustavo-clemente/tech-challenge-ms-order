<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use App\Infrastructure\Order\Mapper\Eloquent\EloquentOrderMapper;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentOrderMapperTest extends TestCase
{
    /**
     * @dataProvider provide_order_status
     */
    public function test_map_to_domain_returns_correct_order(OrderStatus $status): void
    {
        $orderModel = $this->createOrderMock(
            id: uniqid(),
            storeId: uniqid(),
            customerId: uniqid(),
            previsionDeliveryDate: Carbon::now(),
            items: new Collection([
                $this->createOrderItemModelMock(
                    orderItemId: uniqid(),
                    orderId: uniqid(),
                    productId: uniqid(),
                    quantity: 10,
                    price_in_cents: 20000,
                    discount_in_cents: null
                ),
                $this->createOrderItemModelMock(
                    orderItemId: uniqid(),
                    orderId: uniqid(),
                    productId: uniqid(),
                    quantity: 10,
                    price_in_cents: 20000,
                    discount_in_cents: null
                ),
                $this->createOrderItemModelMock(
                    orderItemId: uniqid(),
                    orderId: uniqid(),
                    productId: uniqid(),
                    quantity: 10,
                    price_in_cents: 20000,
                    discount_in_cents: null
                ),
            ]),
            status: $status->value,
            createdAt: Carbon::now(),
        );

        $order = app(EloquentOrderMapper::class)->mapToDomain($orderModel);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($orderModel->id, $order->getOrderId()->getIdentifier());
        $this->assertEquals($orderModel->store_id, $order->getOrderDetails()->getStoreId()->getIdentifier());
        $this->assertEquals($orderModel->customer_id, $order->getOrderDetails()->getCustomerId()->getIdentifier());
        $this->assertEquals(
            $orderModel->prevision_delivery_date,
            $order->getOrderDetails()->getPrevisionDeliveryDate()->format('Y-m-d H:i:s')
        );
        $this->assertEquals($orderModel->status, $order->getOrderDetails()->getOrderStatus()->value);
        $this->assertCount($orderModel->items->count(), $order->getOrderDetails()->getItems());
        $this->assertEquals(
            $orderModel->created_at,
            $order->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @dataProvider provide_order_status
     */
    public function test_map_to_model_returns_correct_order_model(OrderStatus $status): void
    {
        $order = new Order(
            orderId: new OrderId(uniqid()),
            orderDetails: new OrderDetails(
                storeId: new StoreId(uniqid()),
                customerId: new CustomerId(uniqid()),
                items: new OrderItemCollection([
                    new OrderItem(
                        id: new OrderItemId(uniqid()),
                        productId: new ProductId(uniqid()),
                        quantity: 10,
                        priceInCents: 50000,
                        discountInCents: 30000
                    )
                ]),
                orderStatus: $status,
                previsionDeliveryDate: new DateTime(),
            )
        );

        $orderModel = app(EloquentOrderMapper::class)->mapToModel($order);

        $this->assertInstanceOf(OrderModel::class, $orderModel);

        $this->assertEquals($order->getOrderId()->getIdentifier(), $orderModel->id);
        $this->assertEquals($order->getOrderDetails()->getStoreId()->getIdentifier(), $orderModel->store_id);
        $this->assertEquals($order->getOrderDetails()->getCustomerId()->getIdentifier(), $orderModel->customer_id);
        $this->assertEquals(
            $order->getOrderDetails()->getPrevisionDeliveryDate()->format('Y-m-d H:i:s'),
            $orderModel->prevision_delivery_date
        );
        $this->assertEquals($order->getOrderDetails()->getOrderStatus()->value, $orderModel->status);
    }

    private function createOrderMock(
        string $id,
        string $storeId,
        string $customerId,
        Carbon $previsionDeliveryDate,
        Collection $items,
        string $status,
        Carbon $createdAt,
    ): OrderModel {
        /** @var OrderModel */
        $ordertModel = $this
            ->getMockBuilder(OrderModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ordertModel
            ->method('__get')
            ->willReturnCallback(function ($property) use (
                $id,
                $storeId,
                $customerId,
                $previsionDeliveryDate,
                $items,
                $status,
                $createdAt
            ) {
                return match ($property) {
                    'id' => $id,
                    'store_id' => $storeId,
                    'customer_id' => $customerId,
                    'prevision_delivery_date' => $previsionDeliveryDate,
                    'items' => $items,
                    'status' => $status,
                    'created_at' => $createdAt
                };
            });

        return $ordertModel;
    }

    private function createOrderItemModelMock(
        string $orderItemId,
        string $productId,
        string $orderId,
        int $quantity,
        int $price_in_cents,
        ?int $discount_in_cents
    ): OrderItemModel {
        /** @var OrderItemModel */
        $orderItemModel = $this
            ->getMockBuilder(OrderItemModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderItemModel
            ->method('__get')
            ->willReturnCallback(function ($property) use (
                $orderItemId,
                $orderId,
                $productId,
                $quantity,
                $price_in_cents,
                $discount_in_cents
            ) {
                return match ($property) {
                    'id' => $orderItemId,
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_in_cents' => $price_in_cents,
                    'discount_in_cents' => $discount_in_cents
                };
            });

        return $orderItemModel;
    }

    public static function provide_order_status(): array
    {
        $testCases = [];

        foreach (OrderStatus::cases() as $case) {
            $testCases[$case->value] = [$case];
        }

        return $testCases;
    }
}
