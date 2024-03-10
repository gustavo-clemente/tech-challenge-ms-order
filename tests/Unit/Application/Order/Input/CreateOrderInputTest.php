<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\Input;

use App\Application\Order\Input\CreateOrderInput;
use App\Domain\Order\Entity\Order;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateOrderInputTest extends TestCase
{
    public function test_get_order_return_a_order(): void
    {
        $orderDetails = [
            'storeId' => '1',
            'items' => [
                [
                    "productId" => '1',
                    "quantity" => 1
                ],
                [
                    "productId" => '2',
                    "quantity" => 1
                ]
            ]
        ];

        $data = [
            'orderDetails' => $orderDetails
        ];

        $createOrderInput = new CreateOrderInput($data);

        $order = $createOrderInput->getOrder();

        $this->assertInstanceOf(Order::class, $order);

        $orderItems = $order->getOrderDetails()->getItems();
        $orderItems = iterator_to_array($orderItems);
        $storeId = $order->getOrderDetails()->getStoreId();

        $this->assertEquals($orderDetails['storeId'], $storeId->getIdentifier());
        $this->assertCount(count($orderDetails['items']), $orderItems);

        $this->assertEquals($orderDetails['items'][0]['quantity'], $orderItems[0]->getQuantity());
        $this->assertEquals($orderDetails['items'][0]['productId'], $orderItems[0]->getProductId()->getIdentifier());
        $this->assertEquals($orderDetails['items'][1]['quantity'], $orderItems[1]->getQuantity());
        $this->assertEquals($orderDetails['items'][1]['productId'], $orderItems[1]->getProductId()->getIdentifier());
    }

    /**
     * @dataProvider provide_invalid_data
     */
    public function test_throw_exception_when_data_is_invalid(array $data): void
    {
        $this->expectException(ValidationException::class);

        $inputCreateOrder = new CreateOrderInput($data);

        $this->assertNull($inputCreateOrder);
    }

    public static function provide_invalid_data(): array
    {
        return [
            'without storeId' => [
                'orderDetails' => [
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
                            'productId' => "3",
                            'quantity' => 1
                        ]
                    ]
                ]
            ],
            'item without quantity' => [
                'orderDetails' => [
                    'storeId' => '1',
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

            'item without productId' => [
                'orderDetails' => [
                    'storeId' => '1',
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

            'without item field' => [
                [
                    'orderDetails' => [
                        'storeId' => '1'
                    ]
                ]
            ],

            'without orderDetails field' => [
                [
                    
                ]
            ],
        ];
    }
}
