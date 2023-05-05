<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Homework>
 */
class HomeworkFactory extends Factory
{

    public function definition()
    {
        return [
            'page_number'=>$this->faker->numberBetween(1,300),
            'description'=>$this->faker->text(),
        ];
    }
}
