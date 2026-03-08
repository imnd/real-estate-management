<?php

namespace App\Orchid\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasRelationSort
{
    protected function applyRelationSort(Builder $query): Builder
    {
        if (!$sort = request()->input('sort')) {
            return $query;
        }

        foreach ($this->getRelationSorts() as $sortKey => $dbField) {
            if (is_numeric($sortKey)) {
                $sortKey = $dbField;
            }

            if ($sort === $sortKey) {
                $query->orderBy($dbField, 'asc');
                return $query;
            }

            if ($sort === "-{$sortKey}") {
                $query->orderBy($dbField, 'desc');
                return $query;
            }
        }

        return $query;
    }

    abstract protected function getRelationSorts(): array;
}
