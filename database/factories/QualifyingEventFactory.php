<?php

namespace Database\Factories;

use App\Models\QualifyingEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualifyingEventFactory extends Factory
{
    protected $model = QualifyingEvent::class;

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
        $dateStart = $this->faker->dateTimeBetween($from, $to);
        $dateFinish = Carbon::instance($dateStart)->addDays(rand(7, 21));;
        return [
            'qualifying_event_title' => $this->faker->words(2, true),
            'qualifying_event_description' => $this->faker->sentence,
            'date_begining' => $dateStart,
            'date_finish' => $dateFinish,
        ];
    }
}
