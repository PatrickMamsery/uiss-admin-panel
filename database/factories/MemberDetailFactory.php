<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MemberDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => rand(3, 10),
            "reg_no" => '2022-04-'. rand(1000, 9999),
            "area_of_interest" => $this->faker->word,
            "university_id" => rand(1, 10),
            "college_id" => rand(1, 10),
            "department_id" => rand(1, 10),
            "degree_programme_id" => rand(1, 10),
        ];
    }
}
