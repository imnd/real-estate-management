<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\Api\IndexPremiseRequest;

readonly class PremiseFilterDTO
{
    public function __construct(
        public ?int $complexId = null,
        public ?int $buildingId = null,
        public ?int $sectionId = null,
        public ?int $floorId = null,
        public ?string $status = null,
        public ?string $type = null,
        public ?float $priceFrom = null,
        public ?float $priceTo = null,
        public ?float $areaFrom = null,
        public ?float $areaTo = null,
        public ?int $rooms = null,
        public ?string $sortBy = 'created_at',
        public string $sortOrder = 'desc',
        public int $perPage = 15,
        public ?array $features = null,
    ) {
    }

    public static function fromRequest(IndexPremiseRequest $request): self
    {
        return new self(
            complexId: $request->validated('complex_id'),
            buildingId: $request->validated('building_id'),
            sectionId: $request->validated('section_id'),
            floorId: $request->validated('floor_id'),
            status: $request->validated('status'),
            type: $request->validated('type'),
            priceFrom: $request->validated('price_from') ? (float)$request->validated('price_from') : null,
            priceTo: $request->validated('price_to') ? (float)$request->validated('price_to') : null,
            areaFrom: $request->validated('area_from') ? (float)$request->validated('area_from') : null,
            areaTo: $request->validated('area_to') ? (float)$request->validated('area_to') : null,
            rooms: $request->validated('rooms'),
            sortBy: $request->validated('sort_by', 'created_at'),
            sortOrder: $request->validated('sort_order', 'desc'),
            perPage: $request->validated('per_page', 15),
            features: $request->validated('features'),
        );
    }

    public function getCacheKey(): string
    {
        return 'premises.' . md5(serialize($this->toArray()));
    }

    public function toArray(): array
    {
        return [
            'complex_id' => $this->complexId,
            'building_id' => $this->buildingId,
            'section_id' => $this->sectionId,
            'floor_id' => $this->floorId,
            'status' => $this->status,
            'type' => $this->type,
            'price_from' => $this->priceFrom,
            'price_to' => $this->priceTo,
            'area_from' => $this->areaFrom,
            'area_to' => $this->areaTo,
            'rooms' => $this->rooms,
            'sort_by' => $this->sortBy,
            'sort_order' => $this->sortOrder,
            'per_page' => $this->perPage,
            'features' => $this->features,
        ];
    }
}
