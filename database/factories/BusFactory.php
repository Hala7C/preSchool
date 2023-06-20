<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'capacity'=>$this->faker->randomElement(['6','8','14','16','22','24']),
            'number'=>$this->faker->numerify("####"),
            // 'bus_supervisor_id'=>1
        ];
    }
}
