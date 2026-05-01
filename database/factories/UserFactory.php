<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role_id' => 1, // Cliente por defecto
            'status' => 'Active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica que el usuario es un gerente
     */
    public function manager()
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 2, // Gerente/Manager
        ]);
    }

    /**
     * Indica que el usuario es administrador
     */
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 3, // Admin
        ]);
    }

    /**
     * Indica que el usuario está inactivo
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Inactive',
        ]);
    }
}
