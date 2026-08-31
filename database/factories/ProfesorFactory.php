<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profesor>
 */
class ProfesorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // nombres/apellidos ya no viven en 'profesors': los genera
            // UserFactory (name/lastname) para el usuario relacionado.
            'telefono' => $this->faker->phoneNumber(),
            // 'especialidad' => $this->faker->word(),
            // Asegúrate de que 'user_id' sea opcional o se genere correctamente
            'user_id' => User::factory(), // Si usas factory para User
        ];
    }
}
