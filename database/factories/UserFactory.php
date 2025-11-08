<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::where('libelle', 'Client')->first();

        if(!$role){
            throw new \Exception('Role "client" is not defined');
        }
        
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'login' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'roleId' => $role->id,
        ];
    }

    /**
     * Define the state for a 'boutiquier' role.
     */
    public function boutiquier()
    {
        return $this->state(function (array $attributes) {
            return [
                'roleId' => Role::where('libelle', 'Boutiquier')->first()->id,
            ];
        });
    }

    /**
     * Define the state for an 'admin' role.
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'roleId' => Role::where('libelle', 'Admin')->first()->id,
            ];
        });
    }

    public function client()
    {
        return $this->state(function (array $attributes) {
            return [
                'roleId' => Role::where('libelle', 'Client')->first()->id,
            ];
        });
    }
}
