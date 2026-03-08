<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\PremiseFilterDTO;
use App\Http\Requests\Api\IndexPremiseRequest;
use App\Http\Requests\Api\StorePremiseRequest;
use App\Http\Requests\Api\UpdatePremiseRequest;
use App\Http\Resources\PremiseCollection;
use App\Http\Resources\PremiseResource;
use App\Models\Premise;
use App\Services\PremiseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PremiseController extends BaseApiController
{
    public function __construct(
        private readonly PremiseService $premiseService
    ) {
    }

    public function index(IndexPremiseRequest $request): PremiseCollection
    {
        $filter = PremiseFilterDTO::fromRequest($request);
        $premises = $this->premiseService->getFilteredPremises($filter);

        return new PremiseCollection($premises);
    }

    public function show(Premise $premise): PremiseResource
    {
        $premise->load([
            'floor.section.building.complex',
            'attachments'
        ]);

        return new PremiseResource($premise);
    }

    public function store(StorePremiseRequest $request): PremiseResource|JsonResponse
    {
        try {
            $data = $request->validated();
            $premise = $this->premiseService->createPremise($data);

            return new PremiseResource($premise);
        } catch (Exception $e) {
            Log::error('Error creating premise: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while creating the premise',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['server' => [$e->getMessage()]]
            );
        }
    }

    public function update(UpdatePremiseRequest $request, Premise $premise): PremiseResource|JsonResponse
    {
        try {
            $data = $request->validated();
            $updatedPremise = $this->premiseService->updatePremise($premise, $data);

            return new PremiseResource($updatedPremise);
        } catch (Exception $e) {
            Log::error('Error updating premise: ' . $e->getMessage(), [
                'premise_id' => $premise->id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while updating the premise',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['server' => [$e->getMessage()]]
            );
        }
    }

    public function destroy(Premise $premise): JsonResponse
    {
        try {
            $this->premiseService->deletePremise($premise);

            return $this->successResponse();
        } catch (Exception $e) {
            Log::error('Error deleting premise: ' . $e->getMessage(), [
                'premise_id' => $premise->id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'An error occurred while deleting the premise',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['server' => [$e->getMessage()]]
            );
        }
    }
}
