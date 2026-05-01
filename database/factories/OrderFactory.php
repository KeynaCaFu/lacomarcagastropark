<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . $this->faker->unique()->numberBetween(100000, 999999),
            'quantity' => $this->faker->numberBetween(1, 5),
            'additional_notes' => $this->faker->sentence(),
            'preparation_time' => $this->faker->numberBetween(15, 60),
            'date' => $this->faker->date(),
            'time' => $this->faker->time('H:i:s'),
            'total_amount' => $this->faker->randomFloat(2, 10, 200),
            'status' => 'Delivered',
            'origin' => 'Web',
            'local_id' => Local::factory(),
        ];
    }
}
