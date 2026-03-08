<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Building;

use App\Models\{Building, Complex};
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\{Input, Relation};
use Orchid\Screen\Screen;
use Orchid\Support\Facades\{Alert, Layout};

class BuildingEditScreen extends Screen
{
    public ?Building $building = null;

    protected CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(CacheService::class);
    }

    public function query(Building $building, Request $request): iterable
    {
        if (!$building->exists && $request->has('complex_id')) {
            $building->complex_id = $request->input('complex_id');
        }

        return compact('building');
    }

    public function name(): ?string
    {
        return $this->building->exists ? 'Редактирование здания' : 'Создание здания';
    }

    public function description(): ?string
    {
        return 'Информация о здании';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->building->exists)
                ->confirm('Вы уверены, что хотите удалить это здание?'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('building.complex_id')
                    ->title('Жилой комплекс')
                    ->fromModel(Complex::class, 'name')
                    ->required()
                    ->searchable()
                    ->help('Выберите жилой комплекс, к которому относится здание'),

                Input::make('building.name')
                    ->title('Название')
                    ->required()
                    ->placeholder('Введите название здания, например: "Корпус 1"'),

                Input::make('building.year_built')
                    ->title('Год постройки')
                    ->required()
                    ->placeholder('Введите год постройки, например: "1800"'),

                Input::make('building.floors_count')
                    ->title('Количество этажей')
                    ->type('number')
                    ->required()
                    ->min(1)
                    ->max(100)
                    ->value(1)
                    ->help('Укажите общее количество этажей в здании'),

            ])->title('Основная информация'),
        ];
    }

    public function save(Building $building, Request $request): RedirectResponse
    {
        $request->validate([
            'building.complex_id' => 'required|exists:complexes,id',
            'building.name' => 'required|string|max:255',
            'building.floors_count' => 'required|integer|min:1|max:100',
        ]);

        $building->fill($request->get('building'))->save();

        $this->cacheService->flush([
            CacheService::TAG_BUILDINGS,
            CacheService::TAG_SECTIONS,
            CacheService::TAG_STATISTICS,
        ]);

        Alert::info('Здание успешно сохранено');

        return redirect()->route('platform.building.list');
    }

    public function remove(Building $building): RedirectResponse
    {
        $building->delete();

        $this->cacheService->flush([
            CacheService::TAG_BUILDINGS,
            CacheService::TAG_SECTIONS,
            CacheService::TAG_PREMISES,
            CacheService::TAG_STATISTICS,
        ]);

        Alert::info('Здание успешно удалено');

        return redirect()->route('platform.building.list');
    }
}
