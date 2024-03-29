<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CollegeFactory extends Factory
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
            'university_id' => rand(1, 10),
        ];
    }
}
