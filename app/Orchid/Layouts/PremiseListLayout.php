<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use App\Models\Premise;
use App\Orchid\Filters\{AreaRangeFilter,
    BuildingFilter,
    ComplexFilter,
    PremiseStatusFilter,
    PremiseTypeFilter,
    PriceRangeFilter,
    TextSearchFilter,};
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PremiseListLayout extends Table
{
    protected $target = 'premises';

    protected function filters(): array
    {
        return [
            TextSearchFilter::class,
            ComplexFilter::class,
            BuildingFilter::class,
            PremiseStatusFilter::class,
            PremiseTypeFilter::class,
            AreaRangeFilter::class,
            PriceRangeFilter::class,
        ];
    }

    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->width('100px'),

            TD::make('premises.number', 'Номер помещения')
                ->sort()
                ->filter()
                ->render(fn(Premise $premise) => Link::make($premise->number)
                    ->route('platform.premise.edit', $premise))
                ->width('150px'),

            TD::make('premises.type', 'Тип помещения')
                ->sort()
                ->filter(TD::FILTER_SELECT, PremiseType::mappedTypes())
                ->render(fn(Premise $premise) => $this->renderType($premise))
                ->width('150px'),

            TD::make('complexes.name', 'Жилой комплекс')
                ->sort()
                ->filter()
                ->render(function (Premise $premise) {
                    $complex = $premise->floor->section->building->complex ?? null;
                    return $complex
                        ? Link::make($complex->name)
                            ->route('platform.complex.edit', $complex)
                        : '<span class="text-muted">—</span>';
                })
                ->width('200px'),

            TD::make('buildings.name', 'Здание')
                ->sort()
                ->filter()
                ->render(function (Premise $premise) {
                    $building = $premise->floor->section->building ?? null;
                    return $building
                        ? Link::make($building->name)
                            ->route('platform.building.edit', $building)
                        : '<span class="text-muted">—</span>';
                })
                ->width('150px'),

            TD::make('sections.name', 'Секция')
                ->sort()
                ->filter()
                ->render(function (Premise $premise) {
                    $section = $premise->floor->section ?? null;
                    return $section
                        ? Link::make($section->name)
                            ->route('platform.section.edit', $section)
                        : '<span class="text-muted">—</span>';
                })
                ->width('120px'),

            TD::make('floors.number', 'Этаж')
                ->sort()
                ->filter()
                ->render(fn(Premise $premise) => $premise->floor
                    ? Link::make('Этаж ' . $premise->floor->number)
                        ->route('platform.floor.edit', $premise->floor)
                    : '<span class="text-muted">—</span>')
                ->width('100px'),

            TD::make('rooms', 'Комнат')
                ->sort()
                ->filter(TD::FILTER_SELECT, [
                    '0' => 'Студия',
                    '1' => '1-комнатная',
                    '2' => '2-комнатная',
                    '3' => '3-комнатная',
                    '4' => '4-комнатная',
                    '5' => '5 и более',
                ])
                ->render(fn(Premise $premise) => $this->renderRoomsCount($premise))
                ->width('100px'),

            TD::make('total_area', 'Общая площадь')
                ->sort()
                ->render(fn(Premise $premise) => number_format((float)$premise->total_area ?? 0, 1) . ' м²')
                ->width('120px'),

            TD::make('living_area', 'Жилая площадь')
                ->sort()
                ->render(fn(Premise $premise) => number_format((float)$premise->living_area ?? 0, 1) . ' м²')
                ->width('120px'),

            TD::make('kitchen_area', 'Площадь кухни')
                ->sort()
                ->render(fn(Premise $premise) => number_format((float)$premise->kitchen_area ?? 0, 1) . ' м²')
                ->width('120px'),

            TD::make('base_price', 'Цена')
                ->sort()
                ->render(fn(Premise $premise) => $premise->base_price
                    ? number_format((float)$premise->base_price ?? 0, 0, '.', ' ') . ' ₽'
                    : '<span class="text-muted">—</span>')
                ->width('150px'),

            TD::make('discount_price', 'Цена со скидкой')
                ->sort()
                ->render(fn(Premise $premise) => $premise->discount_price
                    ? number_format((float)$premise->discount_price ?? 0, 0, '.', ' ') . ' ₽'
                    : '<span class="text-muted">—</span>')
                ->width('150px'),

            TD::make('price_per_meter', 'Цена за м²')
                ->render(fn(Premise $premise) => $premise->base_price && $premise->total_area > 0
                    ? number_format(
                        (float)$premise->total_area ? $premise->base_price / $premise->total_area : 0,
                        0,
                        '.',
                        ' '
                    ) . ' ₽'
                    : '<span class="text-muted">—</span>')
                ->width('120px'),

            TD::make('premises.status', 'Статус')
                ->sort()
                ->filter(TD::FILTER_SELECT, PremiseStatus::mappedStatuses())
                ->render(fn(Premise $premise) => $this->renderStatus($premise))
                ->width('130px'),

            TD::make('premises.created_at', 'Создан')
                ->sort()
                ->render(fn(Premise $premise) => $premise->created_at->format('d.m.Y'))
                ->width('120px'),

            TD::make('actions', 'Действия')
                ->render(fn(Premise $premise) => "<div class='d-flex gap-2 justify-content-center'>" .
                    Link::make('')
                        ->icon('bs.pencil')
                        ->class('btn btn-link')
                        ->route('platform.premise.edit', $premise->id)
                    .
                    Button::make('')
                        ->icon('bs.trash')
                        ->class('btn btn-link text-danger')
                        ->confirm('Вы уверены, что хотите удалить это помещение?')
                        ->method('remove', ['premise' => $premise->id])
                    . "</div>"
                ),
        ];
    }

    private function renderType(Premise $premise): string
    {
        return PremiseType::mappedTypes()[$premise->type] ?? $premise->type;
    }

    private function renderRoomsCount(Premise $premise): string
    {
        if ($premise->rooms === 0) {
            return '<span class="badge bg-info">Студия</span>';
        }

        $labels = [
            1 => '1-комн.',
            2 => '2-комн.',
            3 => '3-комн.',
            4 => '4-комн.',
        ];

        return $labels[$premise->rooms] ?? $premise->rooms . ' комн.';
    }

    private function renderStatus(Premise $premise): string
    {
        $statuses = [
            PremiseStatus::Available->value => ['success', PremiseStatus::Available->label()],
            PremiseStatus::Reserved->value => ['warning', PremiseStatus::Reserved->label()],
            PremiseStatus::Sold->value => ['danger', PremiseStatus::Sold->label()],
            PremiseStatus::NotForSale->value => ['secondary', PremiseStatus::NotForSale->label()],
        ];

        [$color, $label] = $statuses[$premise->status] ?? ['light', $premise->status];

        return "<span class='badge bg-$color'>$label</span>";
    }
}
