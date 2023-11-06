<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'title' => $this->faker->name(),
            'description' => $this->faker->text(),
            'file' => $this->faker->url(),
            'date_document' => $this->faker->date(),
            'status' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
