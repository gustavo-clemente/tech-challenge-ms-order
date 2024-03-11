<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Input;

use App\Application\Order\Input\OrderItemIdsInput;
use App\Application\Order\Input\OrderItemsInput;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\Entity\Item\OrderItemIdCollection;
use App\Domain\Order\ValueObject\OrderId;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderItemIdsInputTest extends TestCase
{
    /**
     * @dataProvider provide_order_id_input
     */
    public function test_get_order_id_return_a_order_id(int|string $id): void
    {
        $orderIdInput = new OrderItemIdsInput($id, [
            "items" => [
                [
                    "itemId" => '1'
                ]
            ]
        ]);

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

    public function test_get_order_items_id_return_order_item_id_collection(): void
    {
        $items = [
            [
                'itemId' => '1'
            ],
            [
                'itemId' => '2'
            ],
            [
                'itemId' => '3'
            ]
        ];

        $orderItemsInput = new OrderItemIdsInput(1, [
            'items' => $items
        ]);

        $orderItemIds = $orderItemsInput->getOrderItemIds();

        $orderItemidsArray = iterator_to_array($orderItemIds);

        $this->assertInstanceOf(OrderItemIdCollection::class, $orderItemIds);
        $this->assertCount(count($items), $orderItemIds);
        $this->assertEquals($items[0]['itemId'], $orderItemidsArray[0]->getIdentifier());
        $this->assertEquals($items[1]['itemId'], $orderItemidsArray[1]->getIdentifier());
        $this->assertEquals($items[2]['itemId'], $orderItemidsArray[2]->getIdentifier());
    }

    /**
     * @dataProvider provide_invalid_data_input
     */
    public function test_throw_exception_when_data_is_invalid(array $data): void
    {
        $this->expectException(ValidationException::class);

        $orderItemsInput = new OrderItemsInput(1, $data);

        $this->assertNull($orderItemsInput);
    }

    public static function provide_invalid_data_input(): array
    {
        return [
            'item without itemId' => [
                [

                    'items' => [
                        [
                            'itemId' => "1"
                        ],
                        [
                            'itemId' => "2"
                        ],
                        [
                            'otherField' => 1
                        ]
                    ]

                ]
            ],
            'without item field' => [
                [
                    
                ]
            ]
        ];
    }
}
