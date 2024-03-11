<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Order\Adapter\Repository\Eloquent;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use App\Infrastructure\Order\Adapter\Repository\Eloquent\EloquentOrderRepository;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentOrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_saves_on_database(): void
    {
        $order = new Order(
            orderDetails: new OrderDetails(
                storeId: new StoreId(uniqid()),
                customerId: new CustomerId(uniqid()),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId(uniqid()),
                        quantity: 10,
                        priceInCents: 50000
                    ),
                    new OrderItem(
                        productId: new ProductId(uniqid()),
                        quantity: 10,
                        priceInCents: 50000
                    )
                ])
            )
        );

        $orderCreated = app(EloquentOrderRepository::class)->createOrder($order);

        $this->assertInstanceOf(Order::class, $orderCreated);
        $this->assertNotNull($orderCreated->getOrderId());
        $this->assertNotNull($orderCreated->getCreatedAt());
        $this->assertCount(2, $orderCreated->getOrderDetails()->getItems());
        
        $this->assertAllItemsHaveId($orderCreated->getOrderDetails()->getItems());

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderCreated->getOrderId()->getIdentifier()
        ]);
    }

    public function test_get_by_id_returns_correct_order(): void
    {
        $orderModel = OrderModel::factory()->createOne();
        $orderId = new OrderId($orderModel->id);

        OrderItemModel::factory(3)->create([
            'order_id' => $orderModel->id
        ]);

        $order = app(EloquentOrderRepository::class)->getOrderById($orderId);
        $orderDetails = $order->getOrderDetails();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($orderModel->id, $order->getOrderId()->getIdentifier());
        $this->assertCount(3, $orderDetails->getItems());
        $this->assertEquals($orderModel->store_id, $orderDetails->getStoreId()->getIdentifier());
        $this->assertEquals($orderModel->customer_id, $orderDetails->getCustomerId()->getIdentifier());
        $this->assertEquals(
            $orderModel->prevision_delivery_date,
            $orderDetails->getPrevisionDeliveryDate()->format('Y-m-d H:i:s')
        );
        $this->assertEquals(
            $orderModel->created_at,
            $order->getCreatedAt()->format('Y-m-d H:i:s')
        );
        $this->assertEquals($orderModel->status, $orderDetails->getOrderStatus()->value);
    }

    public function test_checkout_order_change_order_status(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderId = new OrderId($orderModel->id);

        OrderItemModel::factory(3)->create([
            'order_id' => $orderModel->id
        ]);

        $order = app(EloquentOrderRepository::class)->checkoutOrder($orderId);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(OrderStatus::AWAITING_PAYMENT->value, $order->getOrderDetails()->getOrderStatus()->value);

        $this->assertDatabaseHas('orders', [
            'id' => $orderModel->id,
            'status' => OrderStatus::AWAITING_PAYMENT->value
        ]);
    }

    public function test_cancel_order_change_order_status(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderId = new OrderId($orderModel->id);

        OrderItemModel::factory(3)->create([
            'order_id' => $orderModel->id
        ]);

        $order = app(EloquentOrderRepository::class)->cancelOrder($orderId);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(OrderStatus::CANCELLED->value, $order->getOrderDetails()->getOrderStatus()->value);

        $this->assertDatabaseHas('orders', [
            'id' => $orderModel->id,
            'status' => OrderStatus::CANCELLED->value
        ]);
    }

    public function test_add_order_items_create_new_items(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderId = new OrderId($orderModel->id);
        $orderItemCollection = new OrderItemCollection([
            new OrderItem(
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 15000
            ),

            new OrderItem(
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 1200,
                discountInCents: 10000
            ),

            new OrderItem(
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 15000
            )
        ]);

        OrderItemModel::factory(3)->create([
            'order_id' => $orderModel->id
        ]);

        $order = app(EloquentOrderRepository::class)->addOrderItems($orderId, $orderItemCollection);
        $orderDetails = $order->getOrderDetails();

        $this->assertCount(6, $orderDetails->getItems());

        $this->assertDatabaseCount('order_items', 6);
    }

    public function test_remove_order_items_delete_items(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderItemModels = OrderItemModel::factory(6)->create([
            'order_id' => $orderModel->id
        ])->toArray();

        $orderId = new OrderId($orderModel->id);
        
        $orderItemidCollection = new OrderItemIdCollection([
            new OrderItemId($orderItemModels[0]['id']),
            new OrderItemId($orderItemModels[1]['id']),
            new OrderItemId($orderItemModels[2]['id'])
        ]);

        $order = app(EloquentOrderRepository::class)->removeOrderItems($orderId, $orderItemidCollection);
        $orderDetails = $order->getOrderDetails();

        $this->assertCount(3, $orderDetails->getItems());

        $this->assertDatabaseCount('order_items', 3);
    }

    private function assertAllItemsHaveId(OrderItemCollection $orderItemCollection)
    {
        foreach($orderItemCollection as $orderItem){
            $this->assertNotNull($orderItem->getId());
        }
    }
}
