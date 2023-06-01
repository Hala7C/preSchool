<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $lang = 36.300564;
        $long = 33.636941;
        return [
            'fullName' => $this->faker->name(),
            'gender' =>  $this->faker->randomElement(['male' ,'female']),
            'motherName' =>  $this->faker->name(),
            'motherLastName' => $this->faker->name(),
            'birthday' => $this->faker->dateTime()->format('Y-m-d'),
            'phone' =>$this->faker->numerify('##########'),
            'location' => $this->faker->company(),
            'siblingNo' => $this->faker->numberBetween(0,10),
            'healthInfo' => $this->faker->text(),
            'bus_registry'=>true,
            'bus_id'=>null,
            'lng'=> $this->faker->latitude(
                $min = ($lang * 10000 - rand(0, 50)) / 10000,
                $max = ($lang * 10000 + rand(0, 50)) / 10000
            ),
            'lat'=>$this->faker->longitude(
                $min = ($long * 10000 - rand(0, 50)) / 10000,
                $max = ($long * 10000 + rand(0, 50)) / 10000
            )
        ];
    }
}
