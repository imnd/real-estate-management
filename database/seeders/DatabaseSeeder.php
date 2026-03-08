<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Complex::factory(5)
            ->create()
            ->each(function ($complex) {
                Building::factory(rand(2, 5))
                    ->create(['complex_id' => $complex->id])
                    ->each(function ($building) {
                        Section::factory(rand(1, 3))
                            ->create(['building_id' => $building->id])
                            ->each(function ($section) use ($building) {
                                for ($floorNum = 1; $floorNum <= $building->floors_count; $floorNum++) {
                                    $floor = Floor::factory()
                                        ->create([
                                            'section_id' => $section->id,
                                        ]);

                                    Premise::factory(rand(4, 10))
                                        ->create(['floor_id' => $floor->id]);
                                }
                            });
                    });
            });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
