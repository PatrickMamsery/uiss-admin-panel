<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->randomElement(['chairman', 'secretary', 'treasurer', 'vice-chairman', 'vice-secretary']),
            'description' => $this->faker->sentence(),
        ];
    }
}
