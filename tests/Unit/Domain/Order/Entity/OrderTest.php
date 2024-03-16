<?php

declare(strict_types= 1);

namespace Tests\Unit\Domain\Order\Entity;

use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Exception\EmptyOrderException;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Store\Entity\StoreId;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_cannot_be_created_without_a_item(): void
    {
        $this->expectException(EmptyOrderException::class);

        $order = new Order(
            orderDetails:new OrderDetails(
                storeId: new StoreId("111"),
                items: new OrderItemCollection([])
            )
        );

        $this->assertNull($order);
    }
}
