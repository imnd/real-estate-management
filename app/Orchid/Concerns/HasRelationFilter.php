<?php

namespace App\Orchid\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasRelationFilter
{
    protected function applyRelationFilter(Builder $query): Builder
    {
        if (!$filter = request()->input('filter')) {
            return $query;
        }

        foreach ($this->getRelationFilters() as $filterKey => $dbField) {
            if (is_numeric($filterKey)) {
                $searchMode = 'whereLike';
                $filterKey = $dbField;
            } else {
                $searchMode = [
                    'where' => 'where',
                    'like' => 'whereLike',
                    'in' => 'whereIn',
                ][$dbField];
                $dbField = $filterKey;
            }

            if ($val = $filter[$filterKey] ?? null) {
                if ($searchMode === 'whereLike') {
                    $val = "%$val%";
                }
                $query->$searchMode($dbField, $val);
            }
        }

        return $query;
    }

    abstract protected function getRelationFilters(): array;
}
