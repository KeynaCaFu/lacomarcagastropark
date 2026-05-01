<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => $this->faker->unique()->numberBetween(1000, 9999),
            'quantity' => $this->faker->numberBetween(1, 5),
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'status' => Order::STATUS_DELIVERED,
            'origin' => Order::ORIGIN_WEB,
            'local_id' => 1,
            'total_amount' => $this->faker->numberBetween(50, 500),
        ];
    }
}
