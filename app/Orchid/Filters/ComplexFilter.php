<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Enums\CacheTtlType;
use App\Models\Complex;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class ComplexFilter extends Filter
{
    protected CacheService $cacheService;

    public function __construct()
    {
        parent::__construct();
        $this->cacheService = app(CacheService::class);
    }

    public function name(): string
    {
        return 'Жилой комплекс';
    }

    public function parameters(): array
    {
        return ['complex'];
    }

    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('floor.building.complex', function ($query) {
            $query->where('id', $this->request->get('complex'));
        });
    }

    public function display(): array
    {
        $complexes = $this->cacheService->remember(
            'complexes_list',
            [CacheService::TAG_COMPLEXES],
            function () {
                return Complex::orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();
            },
            CacheTtlType::Complexes
        );

        return [
            Select::make('complex')
                ->options($complexes)
                ->empty('Все комплексы', '')
                ->value($this->request->get('complex'))
                ->title('Выберите комплекс'),
        ];
    }
}
