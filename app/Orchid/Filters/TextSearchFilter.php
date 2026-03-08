<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class TextSearchFilter extends Filter
{
    public $parameters = ['search'];

    public function run(Builder $builder): Builder
    {
        $search = $this->request->get('search');

        if (empty($search)) {
            return $builder;
        }

        return $builder->where(function ($query) use ($search) {
            $query->where('number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('floor.section.building.complex', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('floor.section.building', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function display(): array
    {
        return [
            Input::make('search')
                ->title('Поиск')
                ->type('text')
                ->placeholder('Номер помещения, описание или комплекс...')
                ->value($this->request->get('search')),
        ];
    }

    public function value(): string
    {
        $search = $this->request->get('search');

        if (empty($search)) {
            return '';
        }

        return $this->name() . ': "' . $search . '"';
    }

    public function name(): string
    {
        return 'Поиск';
    }
}
