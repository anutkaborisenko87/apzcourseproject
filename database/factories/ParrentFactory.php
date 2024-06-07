<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ParrentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    final public function definition(): array
    {
        return [
            'phone'=> $this->faker->phoneNumber,
        ];
    }
}
