<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        
        return [
            'day_of_week' => $this->faker->randomElement($days),
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'status' => true,
            'local_id' => Local::factory(),
        ];
    }

    /**
     * Indica que el local está cerrado este día
     */
    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => false,
            ];
        });
    }

    /**
     * Define un horario específico
     */
    public function withTime(string $opening, string $closing): static
    {
        return $this->state(function (array $attributes) use ($opening, $closing) {
            return [
                'opening_time' => $opening,
                'closing_time' => $closing,
            ];
        });
    }

    /**
     * Define un día específico de la semana
     */
    public function forDay(string $day): static
    {
        return $this->state(function (array $attributes) use ($day) {
            return [
                'day_of_week' => $day,
            ];
        });
    }
}
