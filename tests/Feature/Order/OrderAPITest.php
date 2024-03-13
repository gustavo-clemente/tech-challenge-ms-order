<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrderAPITest extends TestCase
{
    const BASE_URL = "/order";

    use RefreshDatabase;

    public function test_post_order_creates_order_with_items(): void
    {
        $payload = [
            "orderDetails" => [
                "storeId" => uniqid(),
                "customerId" => uniqid(),
                "items" => [
                    [
                        "productId" => uniqid(),
                        "quantity" => 1
                    ],

                    [
                        "productId" => uniqid(),
                        "quantity" => 2
                    ],

                    [
                        "productId" => uniqid(),
                        "quantity" => 3
                    ]
                ]
            ]
        ];

        $response = $this->postJson(self::BASE_URL, $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount("orders", 1);
        $this->assertDatabaseCount("order_items", 3);

        $response->assertJson(
            fn (AssertableJson $json) =>
              $json->hasAll([
                'orderId',
                'orderDetails',
                'createdAt',
                'orderPaymentDetails'
            ])->has(
                'orderDetails',
                fn (AssertableJson $json) =>
                  $json->hasAll([
                    'storeId',
                    'customerId',
                    'items',
                    'orderStatus',
                    'previsionDeliveryDate'
                  ])->has(
                    'items',
                    3
                  )
            )
        );
    }
}
