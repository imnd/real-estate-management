<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Enums\ComplexStatus;
use App\Models\Complex;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ComplexListLayout extends Table
{
    protected $target = 'complexes';

    protected function filters(): array
    {
        return [
        ];
    }

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('name', 'Название')
                ->sort()
                ->filter()
                ->render(fn(Complex $complex) => Link::make($complex->name)
                    ->route('platform.complex.edit', $complex)),

            TD::make('address', 'Адрес')
                ->sort()
                ->filter(),

            TD::make('status', 'Статус')
                ->sort()
                ->filter(TD::FILTER_SELECT, ComplexStatus::mappedTypes())
                ->render(fn(Complex $complex) => match ($complex->status) {
                    ComplexStatus::Planning->value => '<span class="badge bg-warning">' . ComplexStatus::Planning->label(
                        ) . '</span>',
                    ComplexStatus::Construction->value => '<span class="badge bg-info">' . ComplexStatus::Construction->label(
                        ) . '</span>',
                    ComplexStatus::Completed->value => '<span class="badge bg-success">' . ComplexStatus::Completed->label(
                        ) . '</span>',
                }),

            TD::make('created_at', 'Создан')
                ->sort()
                ->render(fn(Complex $complex) => $complex->created_at->format('d.m.Y H:i')),

            TD::make('actions', 'Действия')
                ->render(fn(Complex $complex) => "<div class='d-flex gap-2 justify-content-center'>" .
                    Link::make('')
                        ->icon('bs.pencil')
                        ->class('btn btn-link')
                        ->route('platform.complex.edit', $complex->id)
                    .
                    Button::make('')
                        ->icon('bs.trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Вы уверены, что хотите удалить этот комплекс?')
                        ->method('remove', ['complex' => $complex->id])
                    . "</div>"
                )
        ];
    }
}
