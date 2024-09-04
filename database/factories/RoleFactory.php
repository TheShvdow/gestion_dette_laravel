<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->randomElement(['client', 'admin', 'boutiquier']),
        ];
    }

    public function client()
    {
        return $this->state(function (array $attributes) {
            return [
                'libelle' => 'Client',
            ];
        });
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'libelle' => 'Admin',
            ];
        });
    }
    public function boutiquier()
    {
        return $this->state(function (array $attributes) {
            return [
                'libelle' => 'Boutiquier',
            ];
        });
    }
}
