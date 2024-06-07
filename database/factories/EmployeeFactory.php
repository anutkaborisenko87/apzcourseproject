<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
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
            'contract_number'=> $this->faker->numberBetween(100000, 999999),
            'employment_date' => date('Y-m-d')
        ];
    }
}
