<?php

namespace Database\Factories\Order;

use App\Domain\Order\Enum\OrderStatus;
use App\Infrastructure\Order\Model\Eloquent\OrderModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = OrderModel::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'store_id' => $this->faker->uuid(),
            'customer_id' => $this->faker->uuid(),
            'prevision_delivery_date' => Carbon::now(),
            'status' => OrderStatus::CREATED->value,
            'total_in_cents' => $this->faker->randomNumber(6)
        ];
    }
}
