<?php

namespace Database\Factories;

use App\Models\ProductReview;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'responded_by' => null, // Sin respuesta del gerente por defecto
        ];
    }

    /**
     * Indica que la reseña fue respondida por un gerente
     */
    public function withManagerResponse(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'responded_by' => User::factory(),
            ];
        });
    }
}
