<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildrenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    final public function definition(): array
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        if ($month >= 9) {
            $enrollment_date = Carbon::create($year, 9, 1);
            $gradute_date = Carbon::create($year + 3, 8, 31);
        } else {
            $enrollment_date = Carbon::create($year - 1, 9, 1);
            $gradute_date = Carbon::create($year + 2, 8, 31);
        }
        return [
            'medical_card_number'=> $this->faker->numberBetween(100000, 999999),
            'enrollment_date' => $enrollment_date->format('Y-m-d'),
            'enrollment_year' => $enrollment_date->year,
            'graduation_date' => $gradute_date->format('Y-m-d'),
            'graduation_year' => $gradute_date->year,
        ];
    }
}
