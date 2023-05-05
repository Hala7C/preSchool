<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{

    public function definition()
    {
        return [
            'title'     => $this->faker->name(),
            'semester' => $this->faker->randomElement(['s1' ,'s2']),
            'number'    => $this->faker->numberBetween(1,20),
        ];
    }
}
