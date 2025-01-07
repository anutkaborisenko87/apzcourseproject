<?php

namespace Database\Factories;

use App\Models\EducationalEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationalEventFactory extends Factory
{
    protected $model = EducationalEvent::class;

    public function definition(): array
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        if ($month >= 9) {
            $from = Carbon::create($year, 9, 1);
            $to = Carbon::create($year + 1, 8, 31);
        } else {
            $from = Carbon::create($year - 1, 9, 1);
            $to = Carbon::create($year, 8, 31);
        }
        return [
            'subject' => $this->faker->sentence(3),
            'event_date' => $this->faker->dateTimeBetween($from, $to),
            'didactic_materials' => $this->faker->sentence(5),
            'developed_skills' => $this->faker->words(3, true),
            'event_description' => $this->faker->paragraph,
            'employee_id' => null
        ];
    }
}
