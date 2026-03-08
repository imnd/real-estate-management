<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\StatisticsCollection;
use App\Http\Resources\StatisticsResource;
use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Section;

class StatisticsController extends BaseApiController
{
    public function index(): StatisticsCollection
    {
        return new StatisticsCollection(collect());
    }

    public function complex(Complex $complex): StatisticsResource
    {
        return new StatisticsResource($complex);
    }

    public function building(Building $building): StatisticsResource
    {
        return new StatisticsResource($building);
    }

    public function section(Section $section): StatisticsResource
    {
        return new StatisticsResource($section);
    }

    public function floor(Floor $floor): StatisticsResource
    {
        return new StatisticsResource($floor);
    }
}
