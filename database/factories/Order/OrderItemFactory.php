<?php

namespace Database\Factories\Order;

use App\Infrastructure\Order\Model\Eloquent\OrderItemModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItemModel::class;
    public function definition(): array
    {
        return [
            'product_id' => $this->faker->uuid(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'price_in_cents' => $this->faker->numberBetween(10000, 20000),
            'discount_in_cents' => $this->faker->numberBetween(5000, 8000)
        ];
    }
}
