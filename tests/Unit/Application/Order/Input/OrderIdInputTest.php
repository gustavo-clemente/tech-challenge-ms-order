<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Input;
use App\Application\Order\Input\OrderIdInput;
use App\Domain\Order\ValueObject\OrderId;
use Tests\TestCase;

class OrderIdInputTest extends TestCase
{
    /**
     * @dataProvider provide_order_id_input
     */
    public function test_get_order_id_return_a_order_id_domain_object(int|string $id): void
    {
        $orderIdInput = new OrderIdInput($id);

        $orderId = $orderIdInput->getOrderId();

        $this->assertInstanceOf(OrderId::class, $orderId);
        $this->assertEquals($id, $orderId->getIdentifier());
    }

    public static function provide_order_id_input(): array
    {
        return [
            'with string id' => [
                '1'
            ],
            'with integer id' => [
                1
            ]
        ];
    }
}
