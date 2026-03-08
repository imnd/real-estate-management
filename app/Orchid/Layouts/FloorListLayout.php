<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Models\Floor;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FloorListLayout extends Table
{
    protected $target = 'floors';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('number', 'Этаж')
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->render(fn(Floor $floor) => Link::make('Этаж ' . $floor->number)
                    ->route('platform.floor.edit', $floor)),

            TD::make('sections.name', 'Секция')
                ->sort()
                ->filter()
                ->render(fn(Floor $floor) => $floor->section->name ?? ''),

            TD::make('buildings.name', 'Здание')
                ->sort()
                ->filter()
                ->render(fn(Floor $floor) => $floor->building->name ?? $floor->section->building->name ?? ''),

            TD::make('complexes.name', 'Комплекс')
                ->sort()
                ->filter()
                ->render(
                    fn(Floor $floor
                    ) => $floor->building->complex->name ?? $floor->section->building->complex->name ?? ''
                ),

            TD::make('premises_count', 'Помещений')
                ->render(fn(Floor $floor) => $floor->premises_count),

            TD::make('created_at', 'Создан')
                ->sort()
                ->render(fn(Floor $floor) => $floor->created_at->format('d.m.Y H:i')),

            TD::make('actions', 'Действия')
                ->render(fn(Floor $floor) => "<div class='d-flex gap-2 justify-content-center'>" .
                    Link::make('')
                        ->icon('bs.pencil')
                        ->class('btn btn-link')
                        ->route('platform.floor.edit', $floor->id)
                    .
                    Button::make('')
                        ->icon('bs.trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Вы уверены, что хотите удалить этот этаж?')
                        ->method('remove', ['floor' => $floor->id])
                    . "</div>"
                ),
        ];
    }
}
