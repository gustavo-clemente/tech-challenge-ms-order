<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Order\Service;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatus;
use App\Domain\Order\Exception\CheckoutOrderStatusException;
use App\Domain\Order\Exception\OrderNotCancelableException;
use App\Domain\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Port\Repository\OrderRepository;
use App\Domain\Order\Service\OrderService;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductCollection;
use App\Domain\Product\Entity\ProductIdCollection;
use App\Domain\Product\Port\MsAdapter\ProductMsAdapter;
use App\Domain\Product\ValueObject\ProductCategoryId;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    public function test_createOrder_return_order(): void
    {
        $productIds = [
            new ProductId('111'),
            new ProductId('222')
        ];

        $productCollection = new ProductCollection([
            new Product(
                productId: $productIds[0],
                categoryId: new ProductCategoryId("1"),
                name: "Product 1",
                priceInCents: 20000
            ),
            new Product(
                productId: $productIds[1],
                categoryId: new ProductCategoryId("2"),
                name: "Product 2",
                priceInCents: 30000
            ),
        ]);

        $productIdsColection = new ProductIdCollection($productIds);

        $order = new Order(
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: $productIds[0],
                        quantity:1
                    ),
                    new OrderItem(
                        productId: $productIds[1],
                        quantity:1
                    )
                ])
            )
        );

        $this->mock(ProductMsAdapter::class, function(MockInterface $mock) use($productCollection){
            $mock
              ->shouldReceive('getProductsById')
              ->once()
              ->andReturn($productCollection);
        });
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive("createOrder")
              ->with($order)
              ->once()
              ->andReturn($order);
            });

        $order = app(OrderService::class)->createOrder($order);

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_populate_order_Items_price_prices_populate_order_with_prices(): void
    {
        $productsForTest = [
            [
                "id" => new ProductId('111'),
                "price" => 20000
            ],
            [
                "id" => new ProductId('222'),
                "price" => 3000
            ]
        ];

        $productCollection = new ProductCollection([
            new Product(
                productId: $productsForTest[0]['id'],
                categoryId: new ProductCategoryId("1"),
                name: "Product 1",
                priceInCents: $productsForTest[0]['price']
            ),
            new Product(
                productId: $productsForTest[1]['id'],
                categoryId: new ProductCategoryId("2"),
                name: "Product 2",
                priceInCents: $productsForTest[1]['price']
            ),
        ]);

        $order = new Order(
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: $productsForTest[0]['id'],
                        quantity:1
                    ),
                    new OrderItem(
                        productId: $productsForTest[1]['id'],
                        quantity:1
                    )
                ])
            )
        );

        $this->mock(ProductMsAdapter::class, function(MockInterface $mock) use($productCollection){
            $mock
              ->shouldReceive('getProductsById')
              ->once()
              ->andReturn($productCollection);
        });
      
        $orderPopulated = app(OrderService::class)->populateOrderItemsPrice($order);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertCount($order->getOrderDetails()->getItems()->count(), $orderPopulated->getOrderDetails()->getItems());

        foreach($orderPopulated->getOrderDetails()->getItems() as $index => $item){
            $this->assertEquals($productsForTest[$index]['price'], $item->getPriceInCents());
        }
    }

    public function test_get_order_by_id_return_order(): void
    {
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity:1,
                        priceInCents: 10000
                    )
                ])
            )
        );
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);
        });

        $order = app(OrderService::class)->getOrderById($order->getOrderId());

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_get_order_by_id_throw_exception_when_order_id_is_not_found(): void
    {
        $this->expectException(OrderNotFoundException::class);

        $orderId = new OrderId('111');

        $this->mock(OrderRepository::class, function (MockInterface $mock) use($orderId){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($orderId)
              ->andReturn(null);
        });

        $order = app(OrderService::class)->getOrderById($orderId);

        $this->assertNull($order);
    }

    public function test_checkout_order_return_order(): void
    {
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity:1,
                        priceInCents: 10000
                    )
                    ]),
                orderStatus: OrderStatus::CREATED
            )
        );
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

              $mock
              ->shouldReceive('checkoutOrder')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

        });

        $order = app(OrderService::class)->checkoutOrder($order->getOrderId());

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_checkout_order_throw_exception_when_order_id_is_not_found(): void
    {
        $this->expectException(OrderNotFoundException::class);

        $orderId = new OrderId("111");
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($orderId){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($orderId)
              ->andReturn(null);

              $mock
              ->shouldNotReceive('checkoutOrder');
        });

        $order = app(OrderService::class)->checkoutOrder($orderId);

        $this->assertNull($order);
    }

    /**
     * @dataProvider provide_order_status_for_checkout_test
     */
    public function test_checkout_order_throw_exception_when_status_diferent_from_created(
        OrderStatus $orderStatus
        ): void {
        $this->expectException(CheckoutOrderStatusException::class);
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity:1,
                        priceInCents: 10000
                    )
                    ]),
                orderStatus: $orderStatus
            )
        );
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

              $mock
              ->shouldNotReceive('checkoutOrder');
        });

        $order = app(OrderService::class)->checkoutOrder($order->getOrderId());

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_cancel_order_return_cancelled_order(): void
    {
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity:1,
                        priceInCents: 10000
                    )
                    ]),
                orderStatus: OrderStatus::CREATED
            )
        );

        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

              $mock
              ->shouldReceive('cancelOrder')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

        });

        $order = app(OrderService::class)->cancelOrder($order->getOrderId());

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_cancel_order_should_throw_when_order_id_not_found(): void
    {
        $this->expectException(OrderNotFoundException::class);

        $orderId = new OrderId("111");
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($orderId){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($orderId)
              ->andReturn(null);

              $mock
              ->shouldNotReceive('cancelOrder');
        });

        $order = app(OrderService::class)->cancelOrder($orderId);

        $this->assertNull($order);
    }

    /**
     * @dataProvider provide_order_status_for_checkout_test
     */
    public function test_cancel_order_throw_exception_when_status_diferent_from_created(
        OrderStatus $orderStatus
        ): void {
        $this->expectException(OrderNotCancelableException::class);
        $order = new Order(
            orderId: new OrderId("111"),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: new OrderItemCollection([
                    new OrderItem(
                        productId: new ProductId('111'),
                        quantity:1,
                        priceInCents: 10000
                    )
                    ]),
                orderStatus: $orderStatus
            )
        );
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

              $mock
              ->shouldNotReceive('cancelOrder');
        });

        $order = app(OrderService::class)->cancelOrder($order->getOrderId());

        $this->assertInstanceOf(Order::class, $order);
    }

    public static function provide_order_status_for_checkout_test(): array
    {
        $cases = [];

        foreach(OrderStatus::cases() as $orderStatus){
            if($orderStatus === OrderStatus::CREATED) {
                continue;
            }

            $cases[$orderStatus->name] = [$orderStatus];
        }

        return $cases;
    }

    public function test_add_order_items_return_order(): void
    {
        $orderItems = new OrderItemCollection([
            new OrderItem(
                productId: new ProductId('111'),
                quantity:1,
                priceInCents:1000
            )
        ]);

        $order = new Order(
            orderId: new OrderId('111'),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: $orderItems
            )
        );

        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order, $orderItems){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

            $mock
              ->shouldReceive('addOrderItems')
              ->once()
              ->with($order->getOrderId(), $orderItems)
              ->andReturn($order);
        });

        $order = app(OrderService::class)->addOrderItems($order->getOrderId(), $orderItems);

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_add_order_items_throw_when_order_id_not_found(): void
    {
        $this->expectException(OrderNotFoundException::class);

        $orderId = new OrderId("111");

        $orderItems = new OrderItemCollection([
            new OrderItem(
                productId: new ProductId('111'),
                quantity:1,
                priceInCents:1000
            )
        ]);
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($orderId){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($orderId)
              ->andReturn(null);

              $mock
              ->shouldNotReceive('addOrderItems');
        });

        $order = app(OrderService::class)->addOrderItems($orderId, $orderItems);

        $this->assertNull($order);
    }

    public function test_remove_order_items_returns_order(): void
    {
        $orderItems = new OrderItemCollection([
            new OrderItem(
                productId: new ProductId('111'),
                quantity:1,
                priceInCents:1000
            )
        ]);

        $orderItemsIds = new OrderItemIdCollection([
            new OrderItemId(uniqid())
        ]);

        $order = new Order(
            orderId: new OrderId('111'),
            orderDetails: new OrderDetails(
                storeId: new StoreId('111'),
                items: $orderItems
            )
        );

        $this->mock(OrderRepository::class, function (MockInterface $mock) use($order, $orderItemsIds){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($order->getOrderId())
              ->andReturn($order);

            $mock
              ->shouldReceive('removeOrderItems')
              ->once()
              ->with($order->getOrderId(), $orderItemsIds)
              ->andReturn($order);
        });

        $order = app(OrderService::class)->removeOrderItems($order->getOrderId(), $orderItemsIds);

        $this->assertInstanceOf(Order::class, $order);
    }

    public function test_remove_order_items_throw_when_order_id_not_found(): void
    {
        $this->expectException(OrderNotFoundException::class);

        $orderId = new OrderId("111");

        $orderItemsIds = new OrderItemIdCollection([
            new OrderItemId(uniqid())
        ]);
        
        $this->mock(OrderRepository::class, function (MockInterface $mock) use($orderId){
            $mock
              ->shouldReceive('getOrderById')
              ->once()
              ->with($orderId)
              ->andReturn(null);

              $mock
              ->shouldNotReceive('removeOrderItems');
        });

        $order = app(OrderService::class)->removeOrderItems($orderId, $orderItemsIds);

        $this->assertNull($order);
    }
}
