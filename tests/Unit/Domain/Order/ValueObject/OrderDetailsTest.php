<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Order\ValueObject;

use App\Domain\Customer\Entity\CustomerId;
use App\Domain\Order\Entity\Item\OrderItem;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Exception\Item\ItemWithouPriceException;
use App\Domain\Order\ValueObject\OrderDetails;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Store\Entity\StoreId;
use Tests\TestCase;

class OrderDetailsTest extends TestCase
{
    public function test_get_amount_in_cents_returns_correct_amount(): void
    {
        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 15000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 1,
                    priceInCents: 2000
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 5,
                    priceInCents: 20000
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInCents();

        $this->assertEquals(147000, $totalAmount);
    }

    public function test_get_amount_in_cents_with_discount_returns_correct_amount(): void
    {
        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 15000,
                    discountInCents: 25000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 1,
                    priceInCents: 2000,
                    discountInCents: 1000
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 5,
                    priceInCents: 20000,
                    discountInCents: 16000
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInCents();

        $this->assertEquals(105000, $totalAmount);
    }

    public function test_get_amount_in_cents_throw_when_item_dont_have_price(): void
    {
        $this->expectException(ItemWithouPriceException::class);

        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 15000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 1
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 5,
                    priceInCents: 20000
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInCents();

        $this->assertNull($totalAmount);
    }

    public function test_get_amount_in_real_with_discount_returns_correct_amount(): void
    {
        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 18000,
                    discountInCents: 14000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 9,
                    priceInCents: 100000
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 2,
                    priceInCents: 123456,
                    discountInCents: 112233
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInReal();

        $this->assertEquals(10746.79, $totalAmount);
    }

    public function test_get_amount_in_real_returns_correct_amount(): void
    {
        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 18000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 9,
                    priceInCents: 100000
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 2,
                    priceInCents: 123456
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInReal();

        $this->assertEquals(12009.12, $totalAmount);
    }

    public function test_get_total_amount_throw_when_item_dont_have_price(): void
    {
        $this->expectException(ItemWithouPriceException::class);

        $orderDetails = new OrderDetails(
            storeId: new StoreId("1"),
            customerId: new CustomerId("1"),
            items: new OrderItemCollection([
                new OrderItem(
                    productId: new ProductId("1"),
                    quantity: 3,
                    priceInCents: 15000
                ),
                new OrderItem(
                    productId: new ProductId("2"),
                    quantity: 1,
                    priceInCents: 2000
                ),
                new OrderItem(
                    productId: new ProductId("3"),
                    quantity: 5
                )
            ])
        );

        $totalAmount = $orderDetails->getTotalAmountInReal();

        $this->assertNull($totalAmount);
    }
}
