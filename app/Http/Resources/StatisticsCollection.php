<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class StatisticsCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'general' => [
                'total_complexes' => Complex::count(),
                'total_buildings' => Building::count(),
                'total_sections' => Section::count(),
                'total_floors' => Floor::count(),
                'total_premises' => Premise::count(),
            ],
            'premises_by_status' => Premise::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')->get()->pluck('count', 'status'),
            'premises_by_type' => Premise::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')->get()->pluck('count', 'type'),
            'total_area' => (float)Premise::sum('total_area'),
            'total_value' => (float)Premise::sum('base_price'),
            'average_price_per_m2' => (float)Premise::avg(DB::raw('base_price / NULLIF(total_area, 0)')),
        ];
    }
}
