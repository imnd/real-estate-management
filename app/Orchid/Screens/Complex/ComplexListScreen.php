<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Complex;

use App\Enums\CacheTtlType;
use App\Models\Complex;
use App\Orchid\Layouts\ComplexListLayout;
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class ComplexListScreen extends Screen
{
    protected CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(CacheService::class);
    }

    public function query(): iterable
    {
        $filterParams = request()->all();

        $cacheKey = $this->cacheService->generateFilterKey('complexes_list', $filterParams);

        $complexes = $this->cacheService->remember(
            $cacheKey,
            [CacheService::TAG_COMPLEXES, CacheService::TAG_FILTERS],
            function () {
                return Complex::withCount('buildings')
                    ->when(request()->filled('filter'), fn($q) => $q->filters())
                    ->when(request()->filled('sort'), fn($q) => $q->filters())
                    ->defaultSort('id', 'desc')
                    ->paginate();
            },
            CacheTtlType::Complexes
        );

        return compact('complexes');
    }

    public function name(): ?string
    {
        return 'Жилые комплексы';
    }

    public function description(): ?string
    {
        return 'Управление жилыми комплексами';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать')
                ->icon('plus')
                ->route('platform.complex.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ComplexListLayout::class,
        ];
    }

    public function remove(Complex $complex): RedirectResponse
    {
        $complex->delete();

        $this->cacheService->flush([CacheService::TAG_COMPLEXES]);

        Alert::info('Комплекс успешно удален');

        return redirect()->route('platform.complex.list');
    }
}
