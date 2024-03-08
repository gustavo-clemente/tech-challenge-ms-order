<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Input;

use App\Application\Order\Input\OrderItemsInput;
use App\Domain\Order\Entity\Item\OrderItemCollection;
use App\Domain\Order\ValueObject\OrderId;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderItemsInputTest extends TestCase
{
    /**
     * @dataProvider provide_order_id_input
     */
    public function test_get_order_id_return_a_order_id(int|string $id): void
    {
        $orderIdInput = new OrderItemsInput($id, [
            "items" => [
                [
                    "productId" => '1',
                    "quantity" => 1
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

    public function test_get_order_items_return_order_item_collection(): void
    {
        $items = [
            [
                'productId' => '1',
                'quantity' => 1
            ],
            [
                'productId' => '2',
                'quantity' => 2
            ],
            [
                'productId' => '3',
                'quantity' => 3
            ]
        ];

        $orderItemsInput = new OrderItemsInput(1, [
            'items' => $items
        ]);

        $orderItems = $orderItemsInput->getOrderItems();

        $orderItemsArray = iterator_to_array($orderItems);

        $this->assertInstanceOf(OrderItemCollection::class, $orderItems);
        $this->assertCount(count($items), $orderItems);
        $this->assertEquals($items[0]['productId'], $orderItemsArray[0]->getProductId()->getIdentifier());
        $this->assertEquals($items[0]['quantity'], $orderItemsArray[0]->getQuantity());
        $this->assertEquals($items[1]['productId'], $orderItemsArray[1]->getProductId()->getIdentifier());
        $this->assertEquals($items[1]['quantity'], $orderItemsArray[1]->getQuantity());
        $this->assertEquals($items[2]['productId'], $orderItemsArray[2]->getProductId()->getIdentifier());
        $this->assertEquals($items[2]['quantity'], $orderItemsArray[2]->getQuantity());
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
            'item without productId' => [
                [

                    'items' => [
                        [
                            'productId' => "1",
                            'quantity' => 1
                        ],
                        [
                            'quantity' => 1
                        ],
                        [
                            'productId' => "3",
                            'quantity' => 1
                        ]
                    ]

                ]
            ],
            'item without quantity' => [
                [

                    'items' => [
                        [
                            'productId' => "1",
                            'quantity' => 1
                        ],
                        [
                            'productId' => "2",
                            'quantity' => 1
                        ],
                        [
                            'productId' => "3"
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
