<?php

declare(strict_types= 1);

namespace Tests\Unit\Domain\Order\Item;

use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Exception\InvalidItemQuantityException;
use App\Domain\Product\ValueObject\ProductId;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    public function test_it_cant_be_created_with_quantity_less_than_zero(): void
    {
        $this->expectException(InvalidItemQuantityException::class);

        $orderItem = new OrderItem(
            productId: new ProductId("111"),
            quantity:0,
            priceInCents: 12000
        );

        $this->assertNull($orderItem);
    }
}
