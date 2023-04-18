<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
                'fullName'     => $this->faker->name(),
                'gender' => $this->faker->randomElement(['male' ,'female']),
                'birthday'    => $this->faker->dateTime()->format('Y-m-d'),
                'phone' => $this->faker->numerify('##########'),
                'location' => $this->faker->company(),
                'degree' => $this->faker->randomElement(['bachalor', 'bachalors', 'master'])
        ];
    }
}
