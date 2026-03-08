<?php

declare(strict_types=1);

namespace App\Orchid\Screens\PremiseHistory;

use App\Models\PremiseHistory;
use App\Orchid\Concerns\HasRelationFilter;
use App\Orchid\Concerns\HasRelationSort;
use Orchid\Screen\Screen;

abstract class PremiseHistoryScreen extends Screen
{
    use HasRelationSort, HasRelationFilter;

    protected function getRelationFilters(): array
    {
        return [
            'users.name',
        ];
    }

    protected function getRelationSorts(): array
    {
        return [
            'users.name',
        ];
    }

    protected function getBaseQuery()
    {
        $query = PremiseHistory::with([
            'premise.floor.section.building.complex',
            'user'
        ])
            ->select('premise_history.*')
            ->join('users', 'users.id', '=', 'premise_history.user_id')
            ->orderBy('changed_at', 'desc')
            ->filters();

        $this->applyRelationFilter($query);
        $this->applyRelationSort($query);

        return $query;
    }
}
