<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'start_at' => Carbon::now()->addDays(fake()->numberBetween(1, 7)),
            'location' => fake()->address(),
            'is_active' => true,
            'image_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Evento activo con fecha próxima (dentro de 24 horas)
     */
    public function activeUpcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_at' => now()->addHours(fake()->numberBetween(1, 23)),
        ]);
    }

    /**
     * Evento activo pero que ya pasó (dentro de 24 horas atrás)
     */
    public function activeExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_at' => now()->subHours(fake()->numberBetween(1, 23)),
        ]);
    }

    /**
     * Evento inactivo
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Evento con fecha lejana en el futuro
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_at' => now()->addDays(fake()->numberBetween(8, 30)),
        ]);
    }

    /**
     * Evento con fecha en el pasado (más de 24 horas atrás)
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_at' => now()->subDays(fake()->numberBetween(2, 10)),
        ]);
    }

    /**
     * Evento con imagen URL
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_url' => '/storage/events/' . fake()->slug() . '.jpg',
        ]);
    }

    /**
     * Evento con título y descripción personalizados
     */
    public function withCustomData(string $title, string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'description' => $description,
        ]);
    }

    /**
     * Evento con fecha específica
     */
    public function withStartDate(Carbon $startDate): static
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => $startDate,
        ]);
    }

    /**
     * Evento con ubicación específica
     */
    public function withLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => $location,
        ]);
    }
}
