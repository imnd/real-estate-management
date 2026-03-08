<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Complex;

use App\Enums\ComplexStatus;
use App\Models\Complex;
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\{Input, Select, TextArea, Upload};
use Orchid\Screen\Screen;
use Orchid\Support\Facades\{Alert, Layout};

class ComplexEditScreen extends Screen
{
    public ?Complex $complex = null;

    protected CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(CacheService::class);
    }

    public function query(Complex $complex): iterable
    {
        return compact('complex');
    }

    public function name(): ?string
    {
        return $this->complex->exists ? 'Редактирование комплекса' : 'Создание комплекса';
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
                ->canSee($this->complex->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('complex.name')
                    ->title('Название')
                    ->required()
                    ->placeholder('Введите название комплекса'),

                TextArea::make('complex.description')
                    ->title('Описание')
                    ->rows(5)
                    ->placeholder('Введите описание комплекса'),

                Input::make('complex.address')
                    ->title('Адрес')
                    ->required()
                    ->placeholder('Введите адрес'),

                Select::make('complex.status')
                    ->title('Статус')
                    ->options(ComplexStatus::mappedTypes())
                    ->required(),

                Input::make('complex.latitude')
                    ->title('Широта')
                    ->type('number')
                    ->step('0.00000001')
                    ->placeholder('55.755826'),

                Input::make('complex.longitude')
                    ->title('Долгота')
                    ->type('number')
                    ->step('0.00000001')
                    ->placeholder('37.617299'),

                Upload::make('complex.attachment')
                    ->title('Галерея')
                    ->acceptedFiles('image/*')
                    ->maxFiles(10)
                    ->group('gallery'),
            ]),
        ];
    }

    public function save(Complex $complex, Request $request): RedirectResponse
    {
        $request->validate([
            'complex.name' => 'required|string|max:255',
            'complex.address' => 'required|string|max:500',
            'complex.status' => 'required|in:' . implode(',', ComplexStatus::getList()),
        ]);

        $complex->fill($request->input('complex'))->save();

        $this->cacheService->flush([
            CacheService::TAG_COMPLEXES,
            CacheService::TAG_BUILDINGS,
            CacheService::TAG_STATISTICS
        ]);

        $complex->attachment()->sync($request->input('complex.attachment', []));

        Alert::info('Комплекс успешно сохранен');

        return redirect()->route('platform.complex.list');
    }

    public function remove(Complex $complex): RedirectResponse
    {
        $complex->delete();

        $this->cacheService->flush([
            CacheService::TAG_COMPLEXES,
            CacheService::TAG_BUILDINGS,
            CacheService::TAG_SECTIONS,
            CacheService::TAG_PREMISES,
            CacheService::TAG_STATISTICS,
        ]);

        Alert::info('Комплекс успешно удален');

        return redirect()->route('platform.complex.list');
    }
}
