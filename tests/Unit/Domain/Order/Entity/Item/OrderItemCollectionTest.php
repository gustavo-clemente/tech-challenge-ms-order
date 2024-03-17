<?php

declare(strict_types= 1);

namespace Tests\Unit\Domain\Order\Entity\Item;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Exception\Item\DuplicateProductItemException;
use App\Domain\Product\ValueObject\ProductId;
use Tests\TestCase;

class OrderItemCollectionTest extends TestCase
{
    public function test_order_item_collection_cannot_be_created_with_duplicate_product_id(): void
    {
        $this->expectException(DuplicateProductItemException::class);

        $orderItems = [
            new OrderItem(
                productId: new ProductId("111"),
                quantity:1,
                priceInCents: 12000
            ),
            new OrderItem(
                productId: new ProductId("111"),
                quantity:1,
                priceInCents: 12000
            ),
            new OrderItem(
                productId: new ProductId("333"),
                quantity:3,
                priceInCents: 12000
            )
        ];

        $orderItemCollection = new OrderItemCollection($orderItems);

        $this->assertNull($orderItemCollection);
    }
}
