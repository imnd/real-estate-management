<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Models\Section;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SectionListLayout extends Table
{
    protected $target = 'sections';

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('complexes.name', 'Жилой комплекс')
                ->sort()
                ->filter()
                ->render(fn(Section $section) => Link::make($section->building->complex->name ?? '')
                    ->route('platform.complex.edit', $section->building->complex)),

            TD::make('buildings.name', 'Здание')
                ->sort()
                ->filter()
                ->render(fn(Section $section) => Link::make($section->building->name ?? '')
                    ->route('platform.building.edit', $section->building)),

            TD::make('sections.name', 'Название')
                ->sort()
                ->filter()
                ->render(fn(Section $section) => Link::make($section->name)
                    ->route('platform.section.edit', $section)),

            TD::make('floors_count', 'Этажей')
                ->render(fn(Section $section) => $section->floors->count() ?? ''),

            TD::make('created_at', 'Создан')
                ->sort()
                ->render(fn(Section $section) => $section->created_at->format('d.m.Y H:i')),

            TD::make('actions', 'Действия')
                ->render(fn(Section $section) => "<div class='d-flex gap-2 justify-content-center'>" .
                    Link::make('')
                        ->icon('bs.pencil')
                        ->class('btn btn-link')
                        ->route('platform.section.edit', $section->id)
                    .
                    Button::make('')
                        ->icon('bs.trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Вы уверены, что хотите удалить эту секцию?')
                        ->method('remove', ['section' => $section->id])
                    . "</div>"
                ),
        ];
    }
}
