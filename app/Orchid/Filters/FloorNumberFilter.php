<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;

class FloorNumberFilter extends Filter
{
    public $parameters = ['floor.number'];

    public function name(): string
    {
        return 'Этаж';
    }

    public function run(Builder $builder): Builder
    {
        $value = $this->request->input('floor.number');

        if (!$value) {
            return $builder;
        }

        return $builder->whereHas('floor', function ($query) use ($value) {
            $query->where('number', $value);
        });
    }

    public function display(): array
    {
        return [];
    }
}
