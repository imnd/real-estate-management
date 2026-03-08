<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PremiseResource extends JsonResource
{
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'type' => $this->resource->type,
            'type_label' => PremiseType::mappedTypes()[$this->resource->type],
            'rooms' => $this->resource->rooms,
            'areas' => [
                'total' => $this->resource->total_area,
                'living' => $this->resource->living_area,
                'kitchen' => $this->resource->kitchen_area,
            ],
            'status' => $this->resource->status,
            'status_label' => PremiseStatus::mappedStatuses()[$this->resource->status],
            'prices' => [
                'base' => $this->resource->base_price,
                'discount' => (float)$this->resource->discount_price,
                'base_per_sqm' => (float)$this->resource->base_price_per_sqm,
                'discount_per_sqm' => (float)$this->resource->discount_price_per_sqm,
                'discount_percentage' => $this->calculateDiscountPercentage(),
            ],
            'floor' => $this->resource->floor,
            'section' => $this->whenLoaded('floor.section', function () {
                return [
                    'id' => $this->resource->floor->section->id,
                    'name' => $this->resource->floor->section->name,
                ];
            }),
            'building' => $this->when($this->resource->building, function () {
                return [
                    'id' => $this->resource->building->id,
                    'name' => $this->resource->building->name,
                    'complex' => $this->when($this->resource->complex, function () {
                        return [
                            'id' => $this->resource->complex->id,
                            'name' => $this->resource->complex->name,
                            'address' => $this->resource->complex->address,
                            'coordinates' => [
                                'lat' => (float)$this->resource->complex->latitude,
                                'lng' => (float)$this->resource->complex->longitude,
                            ],
                        ];
                    }),
                ];
            }),
            'features' => $this->resource->features ?? [],
            'gallery' => $this->whenLoaded('attachments', function () {
                return $this->resource->attachments
                    ->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'url' => $attachment->url(),
                            'thumbnail' => $attachment->url('thumb'),
                            'sort_order' => $attachment->sort,
                        ];
                    })
                    ->values();
            }),
        ];
    }

    private function calculateDiscountPercentage(): ?int
    {
        if (!$this->resource->discount_price || $this->resource->base_price <= 0) {
            return null;
        }

        $discount = (1 - $this->resource->discount_price / $this->resource->base_price) * 100;
        return (int)round($discount);
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'meta' => [
                'api_version' => 'v1',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
