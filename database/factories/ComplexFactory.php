<?php

namespace Database\Factories;

use App\Enums\ComplexStatus;
use App\Models\Complex;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplexFactory extends Factory
{
    protected $model = Complex::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Residential Complex',
            'description' => $this->faker->paragraphs(3, true),
            'address' => $this->faker->address(),
            'status' => $this->faker->randomElement(ComplexStatus::getList()),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
