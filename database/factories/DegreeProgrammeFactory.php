<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeProgrammeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'department_id' => rand(1, 10),
        ];
    }
}
