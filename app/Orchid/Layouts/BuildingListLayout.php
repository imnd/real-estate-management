<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Models\Building;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BuildingListLayout extends Table
{
    protected $target = 'buildings';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('complexes.name', 'Жилой комплекс')
                ->sort()
                ->filter()
                ->render(
                    fn(Building $building) => Link::make($building->complex->name ?? '')->route(
                        'platform.complex.edit',
                        $building->complex
                    )
                ),

            TD::make('name', 'Название')
                ->sort()
                ->filter()
                ->render(fn(Building $building) => Link::make($building->name)
                    ->route('platform.building.edit', $building)),

            TD::make('floors_count', 'Этажей')
                ->sort(),

            TD::make('year_built', 'Год постройки')
                ->sort(),

            TD::make('created_at', 'Создан')
                ->sort()
                ->render(fn(Building $building) => $building->created_at->format('d.m.Y H:i')),

            TD::make('actions', 'Действия')
                ->render(fn(Building $building) => "<div class='d-flex gap-2 justify-content-center'>" .
                    Link::make('')
                        ->icon('bs.pencil')
                        ->route('platform.building.edit', $building->id)
                        ->class('btn btn-link') .
                    Button::make('')
                        ->icon('trash')
                        ->method('remove', ['building' => $building->id])
                        ->confirm('Вы уверены, что хотите удалить это здание?') .
                    "</div>"
                ),
        ];
    }
}
