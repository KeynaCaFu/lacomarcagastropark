<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->word(),
            'price' => $this->faker->numberBetween(50, 500),
            'status' => 'Available',
        ];
    }
}
