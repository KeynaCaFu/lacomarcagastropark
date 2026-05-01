<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'description' => $this->faker->sentence(8),
            'category' => $this->faker->randomElement(['Appetizers', 'Main Course', 'Desserts', 'Beverages']),
            'tag' => $this->faker->word(),
            'product_type' => $this->faker->randomElement(['Food', 'Drink', 'Dessert']),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'photo' => $this->faker->imageUrl(),
            'status' => 'Available',
        ];
    }
}
