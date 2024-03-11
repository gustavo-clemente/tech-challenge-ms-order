<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Order\Mapper\Eloquent;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\ValueObject\Item\OrderItemId;
use App\Domain\Order\ValueObject\OrderId;
use App\Domain\Product\ValueObject\ProductId;
use App\Infrastructure\Order\Mapper\Eloquent\EloquentOrderitemMapper;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class EloquentOrderItemMapperTest extends TestCase
{
    public function test_map_to_domain_return_correct_order_item(): void
    {
        $orderId = new OrderId(uniqid());

        $orderItemModel = $this->createOrderItemModelMock(
            orderItemId: uniqid(),
            orderId: $orderId->getIdentifier(),
            productId: uniqid(),
            quantity: 10,
            price_in_cents: 20000,
            discount_in_cents: 10000
        );

        $orderItem = app(EloquentOrderitemMapper::class)->mapToDomain($orderItemModel);

        $this->assertInstanceOf(OrderItem::class, $orderItem);
        $this->assertEquals($orderItemModel->id, $orderItem->getId()->getIdentifier());
        $this->assertEquals($orderItemModel->product_id, $orderItem->getProductId()->getIdentifier());
        $this->assertEquals($orderItemModel->quantity, $orderItem->getQuantity());
        $this->assertEquals($orderItemModel->price_in_cents, $orderItem->getPriceInCents());
        $this->assertEquals($orderItemModel->discount_in_cents, $orderItem->getDiscountInCents());
    }

    public function test_map_to_domain_collection_return_correct_order_item_collection(): void
    {
        $orderId = new OrderId(uniqid());

        $collection = new Collection([
            $this->createOrderItemModelMock(
                orderItemId: uniqid(),
                orderId: $orderId->getIdentifier(),
                productId: uniqid(),
                quantity: 10,
                price_in_cents: 20000,
                discount_in_cents: null
            ),
            $this->createOrderItemModelMock(
                orderItemId: uniqid(),
                orderId: $orderId->getIdentifier(),
                productId: uniqid(),
                quantity: 10,
                price_in_cents: 20000,
                discount_in_cents: 10000
            ),
            $this->createOrderItemModelMock(
                orderItemId: uniqid(),
                orderId: $orderId->getIdentifier(),
                productId: uniqid(),
                quantity: 10,
                price_in_cents: 20000,
                discount_in_cents: 10000
            )
        ]);

        $orderItemCollection = app(EloquentOrderitemMapper::class)->mapToDomainCollection($collection);

        $this->assertInstanceOf(OrderItemCollection::class, $orderItemCollection);
        $this->assertCount(count($collection), $orderItemCollection);
    }

    public function test_map_to_model_return_correct_order_item_model(): void
    {
        $orderId = new OrderId(uniqid());

        $orderItem = new OrderItem(
            id: new OrderItemId(uniqid()),
            productId: new ProductId(uniqid()),
            quantity: 10,
            priceInCents: 15000,
            discountInCents: 3000
        );

        $orderItemModel = app(EloquentOrderitemMapper::class)->mapToModel($orderId, $orderItem);

        $this->assertInstanceOf(OrderItemModel::class, $orderItemModel);
        $this->assertEquals($orderItem->getId()->getIdentifier(), $orderItemModel->id);
        $this->assertEquals($orderItem->getProductId()->getIdentifier(), $orderItemModel->product_id);
        $this->assertEquals($orderItem->getQuantity(), $orderItemModel->quantity);
        $this->assertEquals($orderItem->getPriceInCents(), $orderItemModel->price_in_cents);
        $this->assertEquals($orderItem->getDiscountInCents(), $orderItemModel->discount_in_cents);
    }

    public function test_map_to_model_collection_return_correct_order_item_model_collection(): void
    {
        $orderId = new OrderId(uniqid());

        $orderItemCollection = new OrderItemCollection([
            new OrderItem(
                id: new OrderItemId(uniqid()),
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 15000,
                discountInCents: 3000
            ),
            new OrderItem(
                id: new OrderItemId(uniqid()),
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 15000,
                discountInCents: 3000
            ),
            new OrderItem(
                id: new OrderItemId(uniqid()),
                productId: new ProductId(uniqid()),
                quantity: 10,
                priceInCents: 15000,
                discountInCents: 3000
            ),

        ]);

        $orderItemModelCollection = app(EloquentOrderitemMapper::class)
            ->mapToModelCollection($orderId, $orderItemCollection);

        $this->assertInstanceOf(Collection::class, $orderItemModelCollection);
        $this->assertCount($orderItemCollection->count(), $orderItemModelCollection);
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
}
