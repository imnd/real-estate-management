<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Models\Premise;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Range;

class PriceRangeFilter extends Filter
{
    public function name(): string
    {
        return 'Диапазон цен';
    }

    public function parameters(): array
    {
        return ['min_price', 'max_price'];
    }

    public function run(Builder $builder): Builder
    {
        return $builder
            ->when($this->request->get('min_price'), function ($query, $min) {
                $query->whereRaw('COALESCE(discount_price, base_price) >= ?', [$min]);
            })
            ->when($this->request->get('max_price'), function ($query, $max) {
                $query->whereRaw('COALESCE(discount_price, base_price) <= ?', [$max]);
            });
    }

    public function display(): array
    {
        $min = Premise::min('base_price');
        $max = Premise::max('base_price');

        return [
            Range::make('min_price')
                ->title('Мин. цена')
                ->min($min)
                ->max($max)
                ->value($this->request->get('min_price')),

            Range::make('max_price')
                ->title('Макс. цена')
                ->min($min)
                ->max($max)
                ->value($this->request->get('max_price')),
        ];
    }
}
