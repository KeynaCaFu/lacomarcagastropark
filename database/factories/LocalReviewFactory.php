<?php

namespace Database\Factories;

use App\Models\LocalReview;
use App\Models\Review;
use App\Models\Local;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LocalReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'local_id' => Local::factory(),
            'user_id' => User::factory(),
        ];
    }
}
