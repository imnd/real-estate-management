<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PremiseCollection extends ResourceCollection
{
    public $collects = PremiseResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
            ],
            'links' => [
                'first' => $this->resource->url(1),
                'last' => $this->resource->url($this->resource->lastPage()),
                'prev' => $this->resource->previousPageUrl(),
                'next' => $this->resource->nextPageUrl(),
            ],
            'filters' => $this->when($request->has('filter'), function () use ($request) {
                return [
                    'applied' => $request->get('filter', []),
                    'available' => [
                        'complex_id' => 'Filter by complex ID',
                        'status' => 'Filter by status (available, reserved, sold, not_for_sale)',
                        'type' => 'Filter by type (apartment, studio, penthouse, commercial)',
                        'price_from' => 'Minimum price',
                        'price_to' => 'Maximum price',
                        'area_from' => 'Minimum area',
                        'area_to' => 'Maximum area',
                        'rooms' => 'Number of rooms',
                    ],
                ];
            }),
        ];
    }
}
