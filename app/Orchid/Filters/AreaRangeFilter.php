<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class AreaRangeFilter extends Filter
{
    public $parameters = [
        'area_min',
        'area_max',
    ];

    public function run(Builder $builder): Builder
    {
        $min = $this->request->get('area_min');
        $max = $this->request->get('area_max');

        if (is_numeric($min) && $min > 0) {
            $builder->where('area', '>=', (float)$min);
        }

        if (is_numeric($max) && $max > 0) {
            $builder->where('area', '<=', (float)$max);
        }

        return $builder;
    }

    public function display(): array
    {
        return [
            Input::make('area_min')
                ->title('Площадь от')
                ->type('number')
                ->min(0)
                ->max(1000)
                ->step(1)
                ->placeholder('от м²')
                ->value($this->request->get('area_min')),

            Input::make('area_max')
                ->title('Площадь до')
                ->type('number')
                ->min(0)
                ->max(1000)
                ->step(1)
                ->placeholder('до м²')
                ->value($this->request->get('area_max')),
        ];
    }

    public function value(): string
    {
        $min = $this->request->get('area_min');
        $max = $this->request->get('area_max');

        if (!is_numeric($min) && !is_numeric($max)) {
            return '';
        }

        if (is_numeric($min) && is_numeric($max)) {
            return $this->name() . ': ' . (float)$min . ' - ' . (float)$max . ' м²';
        } elseif (is_numeric($min)) {
            return $this->name() . ': от ' . (float)$min . ' м²';
        } elseif (is_numeric($max)) {
            return $this->name() . ': до ' . (float)$max . ' м²';
        }

        return '';
    }

    public function name(): string
    {
        return 'Площадь';
    }
}
