<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class StatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match (true) {
            $this->resource instanceof Complex => $this->getComplexStats(),
            $this->resource instanceof Building => $this->getBuildingStats(),
            $this->resource instanceof Section => $this->getSectionStats(),
            $this->resource instanceof Floor => $this->getFloorStats(),
            default => parent::toArray($request),
        };
    }

    private function getComplexStats(): array
    {
        $buildingIds = $this->buildings()->pluck('id');
        $sectionIds = Section::whereIn('building_id', $buildingIds)->pluck('id');
        $floorIds = Floor::whereIn('section_id', $sectionIds)->pluck('id');
        $premises = Premise::whereIn('floor_id', $floorIds);

        return [
            'complex' => ['id' => $this->id, 'name' => $this->name],
            'buildings' => ['total' => $buildingIds->count()],
            'sections' => ['total' => $sectionIds->count()],
            'floors' => ['total' => $floorIds->count()],
            'premises' => $this->formatPremisesStats($premises),
        ];
    }

    private function formatPremisesStats($query): array
    {
        return [
            'total' => (clone $query)->count(),
            'total_area' => (float)(clone $query)->sum('total_area'),
            'total_value' => (float)(clone $query)->sum('base_price'),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')->get()->pluck('count', 'status'),
            'by_type' => (clone $query)->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')->get()->pluck('count', 'type'),
        ];
    }

    private function getBuildingStats(): array
    {
        $sectionIds = $this->sections()->pluck('id');
        $floorIds = Floor::whereIn('section_id', $sectionIds)->pluck('id');
        $premises = Premise::whereIn('floor_id', $floorIds);

        return [
            'building' => [
                'id' => $this->id,
                'name' => $this->name,
                'complex' => $this->complex->only(['id', 'name']),
            ],
            'sections' => ['total' => $sectionIds->count()],
            'floors' => ['total' => $floorIds->count()],
            'premises' => array_merge(
                $this->formatPremisesStats($premises),
                ['by_floor' => $this->getPremisesByFloor($premises)]
            ),
        ];
    }

    private function getPremisesByFloor($query)
    {
        return (clone $query)
            ->join('floors', 'premises.floor_id', '=', 'floors.id')
            ->select('floors.number', DB::raw('count(*) as count'))
            ->groupBy('floors.number')
            ->orderBy('floors.number')
            ->get();
    }

    private function getSectionStats(): array
    {
        $floorIds = $this->floors()->pluck('id');
        $premises = Premise::whereIn('floor_id', $floorIds);

        return [
            'section' => [
                'id' => $this->id,
                'name' => $this->name,
                'building' => $this->building->only(['id', 'name']),
                'complex' => $this->building->complex->only(['id', 'name']),
            ],
            'floors' => $floorIds->count(),
            'premises' => array_merge(
                $this->formatPremisesStats($premises),
                ['by_floor' => $this->getPremisesByFloor($premises)]
            ),
        ];
    }

    private function getFloorStats(): array
    {
        $premises = $this->premises();

        return [
            'floor' => [
                'id' => $this->id,
                'number' => $this->number,
                'section' => $this->section->only(['id', 'name']),
                'building' => $this->section->building->only(['id', 'name']),
                'complex' => $this->section->building->complex->only(['id', 'name']),
            ],
            'premises' => array_merge(
                $this->formatPremisesStats($premises),
                [
                    'available' => $this->getDetailedStatus($premises, 'available'),
                    'sold' => $this->getDetailedStatus($premises, 'sold'),
                    'reserved' => $this->getDetailedStatus($premises, 'reserved'),
                ]
            ),
        ];
    }

    private function getDetailedStatus($query, string $status): array
    {
        $filtered = (clone $query)->where('status', $status);
        return [
            'count' => $filtered->count(),
            'area' => (float)$filtered->sum('total_area'),
        ];
    }
}
