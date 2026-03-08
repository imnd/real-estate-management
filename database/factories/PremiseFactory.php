<?php

declare(strict_types=1);

namespace Database\Factories;

use Alirezasedghi\LaravelImageFaker\ImageFaker;
use Alirezasedghi\LaravelImageFaker\Services\Picsum;
use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use App\Models\Floor;
use App\Models\Premise;
use Illuminate\Database\Eloquent\Factories\Factory;

class PremiseFactory extends Factory
{
    protected $model = Premise::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(PremiseType::cases());
        $roomsCount = match ($type) {
            PremiseType::Studio => 0,
            PremiseType::Apartment => $this->faker->numberBetween(1, 4),
            PremiseType::Penthouse => $this->faker->numberBetween(3, 6),
            PremiseType::Commercial => 0,
        };

        return [
            'floor_id' => Floor::factory(),
            'number' => (string)$this->faker->unique()->numberBetween(1, 999),
            'type' => $type->value,
            'rooms' => $roomsCount,
            'total_area' => match ($type) {
                PremiseType::Studio => $this->faker->numberBetween(20, 40),
                PremiseType::Apartment => $this->faker->numberBetween(30, 120),
                PremiseType::Penthouse => $this->faker->numberBetween(100, 300),
                PremiseType::Commercial => $this->faker->numberBetween(50, 500),
            },
            'living_area' => match ($type) {
                PremiseType::Studio => $this->faker->numberBetween(20, 40),
                PremiseType::Apartment => $this->faker->numberBetween(30, 120),
                PremiseType::Penthouse => $this->faker->numberBetween(100, 300),
                PremiseType::Commercial => $this->faker->numberBetween(50, 500),
            },
            'kitchen_area' => match ($type) {
                PremiseType::Studio => $this->faker->numberBetween(6, 10),
                PremiseType::Apartment => $this->faker->numberBetween(10, 12),
                PremiseType::Penthouse => $this->faker->numberBetween(50, 100),
                PremiseType::Commercial => $this->faker->numberBetween(50, 500),
            },
            'base_price' => $this->faker->numberBetween(30000, 500000),
            'discount_price' => $this->faker->numberBetween(30000, 500000),
            'status' => $this->faker->randomElement(PremiseStatus::getList()),
            'plan_image' => (new ImageFaker(new Picsum()))->image(storage_path('app/private/floors')),
        ];
    }

    /**
     * Assign the premise to a specific floor.
     */
    public function forFloor(Floor $floor): static
    {
        return $this->state(fn(array $attributes) => [
            'floor_id' => $floor->id,
        ]);
    }

    /**
     * Indicate that the premise is available.
     */
    public function available(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PremiseStatus::Available,
        ]);
    }

    /**
     * Indicate that the premise is sold.
     */
    public function sold(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'sold',
        ]);
    }

    /**
     * Indicate that the premise is reserved.
     */
    public function reserved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'reserved',
        ]);
    }

    /**
     * Indicate that the premise is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    /**
     * Set a specific type for the premise.
     */
    public function ofType(PremiseType $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $roomsCount = match ($type) {
                PremiseType::Studio => 0,
                PremiseType::Apartment => $this->faker->numberBetween(1, 4),
                PremiseType::Penthouse => $this->faker->numberBetween(3, 6),
                PremiseType::Commercial => 0,
            };

            $area = match ($type) {
                PremiseType::Studio => $this->faker->numberBetween(20, 40),
                PremiseType::Apartment => $this->faker->numberBetween(30, 120),
                PremiseType::Penthouse => $this->faker->numberBetween(100, 300),
                PremiseType::Commercial => $this->faker->numberBetween(50, 500),
            };

            return [
                'type' => $type->value,
                'rooms' => $roomsCount,
                'area' => $area,
            ];
        });
    }
}
