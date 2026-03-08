<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Enums\PremiseStatus;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class PremiseStatusFilter extends Filter
{
    public $parameters = ['status'];

    public function run(Builder $builder): Builder
    {
        $statuses = $this->request->get('status');

        if (empty($statuses)) {
            return $builder;
        }

        return $builder->whereIn('status', (array)$statuses);
    }

    public function display(): array
    {
        return [
            Select::make('status')
                ->title('Статус помещения')
                ->options(PremiseStatus::mappedStatuses())
                ->multiple()
                ->placeholder('Выберите статусы')
                ->help('Фильтр по статусу помещения'),
        ];
    }

    /**
     * Значение фильтра для отображения
     */
    public function value(): string
    {
        $statuses = $this->request->get('status', []);

        if (empty($statuses)) {
            return '';
        }

        $labels = PremiseStatus::mappedStatuses();

        $selected = array_map(function ($status) use ($labels) {
            return $labels[$status] ?? $status;
        }, (array)$statuses);

        return $this->name() . ': ' . implode(', ', $selected);
    }

    public function name(): string
    {
        return 'Статус';
    }
}
