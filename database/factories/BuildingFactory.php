<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Premise;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'complex_id' => Complex::factory(),
            'name' => $this->faker->randomElement([
                'Building 1',
                'Building 2',
                'Building 3',
                'Building A',
                'Building B',
                'Building C',
                'Residential House No.1',
                'Residential House No.2',
                'Tower A',
                'Tower B',
                'Parking Complex',
                'Shopping Center',
                'Office Building',
                'Club House',
                'Residence',
            ]),
            'floors_count' => $this->faker->numberBetween(3, 25),
            'year_built' => $this->faker->numberBetween(1900, Date('Y')),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the building is in construction status.
     */
    public function construction(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'construction',
        ]);
    }

    /**
     * Indicate that the building is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Set a specific number of floors for the building.
     */
    public function withFloorsCount(int $count): static
    {
        return $this->state(fn(array $attributes) => [
            'floors_count' => $count,
        ]);
    }

    /**
     * Ensure the building has an address.
     */
    public function withAddress(): static
    {
        return $this->state(fn(array $attributes) => [
            'address' => $this->faker->address(),
        ]);
    }

    /**
     * Ensure the building has no address (use complex address).
     */
    public function withoutAddress(): static
    {
        return $this->state(fn(array $attributes) => [
            'address' => null,
        ]);
    }

    /**
     * Assign the building to a specific complex.
     */
    public function forComplex(Complex $complex): static
    {
        return $this->state(fn(array $attributes) => [
            'complex_id' => $complex->id,
        ]);
    }

    /**
     * Create the building with sections.
     */
    public function withSections(int $count = 3): static
    {
        return $this->afterCreating(function (Building $building) use ($count) {
            Section::factory()
                ->count($count)
                ->forBuilding($building)
                ->create();
        });
    }

    /**
     * Create the building with sections and floors.
     */
    public function withFloors(): static
    {
        return $this->afterCreating(function (Building $building) {
            // Create 1-3 sections
            $sections = Section::factory()
                ->count(rand(1, 3))
                ->forBuilding($building)
                ->create();

            // Create floors for each section
            foreach ($sections as $section) {
                for ($floorNum = 1; $floorNum <= $building->floors_count; $floorNum++) {
                    \App\Models\Floor::factory()
                        ->forSection($section)
                        ->withFloorNumber($floorNum)
                        ->create();
                }
            }
        });
    }

    /**
     * Create the building with all relations (sections, floors, premises).
     */
    public function withAllRelations(): static
    {
        return $this->afterCreating(function (Building $building) {
            // Create 1-3 sections
            $sections = Section::factory()
                ->count(rand(1, 3))
                ->forBuilding($building)
                ->create();

            // Create floors for each section
            foreach ($sections as $section) {
                for ($floorNum = 1; $floorNum <= $building->floors_count; $floorNum++) {
                    $floor = \App\Models\Floor::factory()
                        ->forSection($section)
                        ->withFloorNumber($floorNum)
                        ->create();

                    // Create premises for each floor
                    Premise::factory()
                        ->count(rand(4, 12))
                        ->forFloor($floor)
                        ->create();
                }
            }
        });
    }

    /**
     * Create a skyscraper (tall building).
     */
    public function skyscraper(): static
    {
        return $this->state(fn(array $attributes) => [
            'floors_count' => $this->faker->numberBetween(30, 60),
            'name' => $this->faker->randomElement([
                    'Skyscraper',
                    'Tower',
                    'High-rise',
                    'Business Center',
                ]) . ' ' . $this->faker->numberBetween(1, 5),
        ]);
    }

    /**
     * Create a low-rise building.
     */
    public function lowRise(): static
    {
        return $this->state(fn(array $attributes) => [
            'floors_count' => $this->faker->numberBetween(1, 3),
            'name' => $this->faker->randomElement([
                'Townhouse',
                'Cottage',
                'Duplex',
                'Low-rise Building',
            ]),
        ]);
    }
}
