<?php

declare(strict_types=1);

namespace Database\Factories;

use Alirezasedghi\LaravelImageFaker\ImageFaker;
use Alirezasedghi\LaravelImageFaker\Services\Picsum;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'section_id' => null,
            'number' => $this->faker->numberBetween(0, 125),
            'plan_image' => (new ImageFaker(new Picsum()))->image(storage_path('app/private/floors')),
        ];
    }

    public function forSection(Section $section): static
    {
        return $this->state(fn(array $attributes) => [
            'section_id' => $section->id,
            'building_id' => $section->building_id,
        ]);
    }

    public function forBuilding(Building $building): static
    {
        return $this->state(fn(array $attributes) => [
            'building_id' => $building->id,
            'section_id' => null,
        ]);
    }
}
