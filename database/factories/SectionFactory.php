<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => $this->faker->randomElement([
                'Section A',
                'Section B',
                'Section C',
                'Entrance 1',
                'Entrance 2',
                'Entrance 3',
                'Block 1',
                'Block 2',
                'Block 3',
                'Wing 1',
                'Wing 2',
            ]),
        ];
    }

    /**
     * Assign the section to a specific building.
     */
    public function forBuilding(Building $building): static
    {
        return $this->state(fn(array $attributes) => [
            'building_id' => $building->id,
        ]);
    }
}
