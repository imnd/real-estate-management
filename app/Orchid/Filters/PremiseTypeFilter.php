<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Enums\PremiseType;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class PremiseTypeFilter extends Filter
{
    public $parameters = ['rooms'];

    public function run(Builder $builder): Builder
    {
        if (empty($type = $this->request->get('type'))) {
            return $builder;
        }

        return $builder->where('type', $type);
    }

    public function display(): array
    {
        return [
            Select::make('type')
                ->title('Тип помещения')
                ->options(PremiseType::mappedTypes())
                ->empty('Все типы', '')
                ->placeholder('Выберите тип помещения')
                ->value($this->request->get('type')),
        ];
    }

    public function value(): string
    {
        $type = $this->request->get('type');

        if (empty($type)) {
            return '';
        }

        $typeEnum = PremiseType::tryFrom($type);

        return $this->name() . ': ' . ($typeEnum?->label() ?? $type);
    }

    public function name(): string
    {
        return 'Тип помещения';
    }
}
