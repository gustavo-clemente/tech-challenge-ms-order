<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use App\Domain\Order\Enum\OrderStatus;
use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrderAPITest extends TestCase
{
    const BASE_URL = "/api/order";

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
                    'amountInCents',
                    'amountInReal',
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

    public function test_get_order_return_correct_order_with_items(): void
    {
        $orderModel = OrderModel::factory()->createOne();

        $orderModelId = $orderModel->id;

        OrderItemModel::factory(2)->create([
            'order_id' => $orderModelId
        ]);

        $baseUrl = self::BASE_URL;

        $response = $this->getJson("{$baseUrl}/{$orderModelId}");

        $response->assertStatus(Response::HTTP_OK);

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
                    'amountInCents',
                    'amountInReal',
                    'storeId',
                    'customerId',
                    'items',
                    'orderStatus',
                    'previsionDeliveryDate'
                  ])->has(
                    'items',
                    2
                  )
            )
        );
    }

    public function test_checkout_order_change_order_status(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderModelId = $orderModel->id;

        OrderItemModel::factory(2)->create([
            'order_id' => $orderModelId
        ]);

        $baseUrl = self::BASE_URL;

        $response = $this->patch("{$baseUrl}/{$orderModelId}/checkout");

        $response->assertStatus(Response::HTTP_OK);

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
                    'amountInCents',
                    'amountInReal',
                    'storeId',
                    'customerId',
                    'items',
                    'orderStatus',
                    'previsionDeliveryDate'
                  ])->has(
                    'items',
                    2
                  )
            )
        );

        $this->assertDatabaseCount("orders", 1);
        $this->assertDatabaseCount("order_items", 2);
        $this->assertDatabaseHas("orders", [
            'id' => $orderModelId,
            'status' => OrderStatus::AWAITING_PAYMENT->value
        ]);
    }

    public function test_cancel_order_change_order_status(): void
    {
        $orderModel = OrderModel::factory()->createOne([
            'status' => OrderStatus::CREATED->value
        ]);

        $orderModelId = $orderModel->id;

        OrderItemModel::factory(2)->create([
            'order_id' => $orderModelId
        ]);

        $baseUrl = self::BASE_URL;

        $response = $this->delete("{$baseUrl}/{$orderModelId}");

        $response->assertStatus(Response::HTTP_OK);

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
                    'amountInCents',
                    'amountInReal',
                    'storeId',
                    'customerId',
                    'items',
                    'orderStatus',
                    'previsionDeliveryDate'
                  ])->has(
                    'items',
                    2
                  )
            )
        );

        $this->assertDatabaseCount("orders", 1);
        $this->assertDatabaseCount("order_items", 2);
        $this->assertDatabaseHas("orders", [
            'id' => $orderModelId,
            'status' => OrderStatus::CANCELLED->value
        ]);
    }
}
