<?php

namespace Database\Seeders;

use App\Models\Children;
use App\Models\EducationalEvent;
use App\Models\Employee;
use App\Models\Group;
use App\Models\Parrent;
use App\Models\Position;
use App\Models\QualifyingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    final public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PositionsSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $parrentRole = Role::where('name', 'user')->first();

        $superAdminUser = User::where('email', 'superAdmin@gmail.com')->first();
        $superAdminUser->assignRole($superAdminRole);

        $teacherPosition = Position::where('position_title', 'teacher')->first();
        $employees = [];
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()
                ->create();
            $user->assignRole($teacherRole);

            $employee = Employee::factory()
                ->for($user, 'user')
                ->for($teacherPosition, 'position')
                ->create();
            $employees[] = $employee->id;
            EducationalEvent::factory()->count(10)->create(['employee_id' => $employee->id]);
        }
        for ($i = 0; $i < 10; $i++) {
            $qualifEvent = QualifyingEvent::factory()->create();
            $randomParticipants = collect($employees)->random(rand(1, count($employees)));
            $qualifEvent->participants()->attach($randomParticipants);
        }
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        if ($month >= 9) {
            $enrollment_date = Carbon::create($year, 9, 1);
            $gradute_date = Carbon::create($year + 3, 8, 31);
        } else {
            $enrollment_date = Carbon::create($year - 1, 9, 1);
            $gradute_date = Carbon::create($year + 2, 8, 31);
        }
        $count = 1;
        for ($i = 0; $i < 5; $i++) {
            $group = Group::factory()
                ->create();
            $teacher1 = Employee::find($i + $count);
            $count++;
            $teacher2 = Employee::find($i+$count);

            $group->teachers()->attach($teacher1, [
                'date_start' => $enrollment_date->format('Y-m-d'),
                'date_finish' => $gradute_date->format('Y-m-d'),
            ]);
            $group->teachers()->attach($teacher2, [
                'date_start' => $enrollment_date->format('Y-m-d'),
                'date_finish' => $gradute_date->format('Y-m-d'),
            ]);

            $children = [];
            for ($k = 0; $k < 20; $k++) {
                $parentUser = User::factory()
                    ->create();
                $parentUser->assignRole($parrentRole);
                $parent = Parrent::factory()
                    ->for($parentUser, 'user')
                    ->create();
                $dirthDate = Carbon::create(now()->year - 3, rand(1, 12), rand(1, 28));
                $childUser = User::factory()
                    ->create([
                        'birth_date' => $dirthDate->format('Y-m-d'),
                        'birth_year' => $dirthDate->year,
                        'city' => $parent->user->city,
                        'street' => $parent->user->street,
                        'email' => null,
                        'password' => null
                    ]);

                $child = Children::factory()
                    ->for($childUser, 'user')
                    ->for($group, 'group')
                    ->create([
                        'enrollment_date' => $enrollment_date->format('Y-m-d'),
                        'enrollment_year' => $enrollment_date->year,
                        'graduation_date' => $gradute_date->format('Y-m-d'),
                        'graduation_year' => $gradute_date->year
                    ]);

                $child->parrent_relations()->attach($parent, ['relations' => 'parent']);
                $children[] = $child;
            }

            $educationalEvents = EducationalEvent::where('employee_id', $teacher1->id)
                ->orWhere('employee_id', $teacher2->id)
                ->whereDate('event_date', '<', now())
                ->get();

            foreach ($children as $child) {
                foreach ($educationalEvents as $event) {
                    $child->visited_educational_events()->attach($event->id, [
                        'estimation_mark' => rand(1, 5)
                    ]);
                }
            }
        }

    }
}
