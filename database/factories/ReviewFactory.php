<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
            'date' => $this->faker->date(),
            'response' => null, // Sin respuesta del gerente por defecto
        ];
    }

    /**
     * Indica que la reseña tiene una respuesta del gerente
     */
    public function withManagerResponse(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'response' => $this->faker->paragraph(),
            ];
        });
    }
}
