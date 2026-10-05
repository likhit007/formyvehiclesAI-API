<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    /**
     * Return list of vehicle models.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VehicleModel::query()
            ->where('is_active', true)
            ->select(['id', 'brand_id', 'vehicle_type_id', 'name', 'vehicle_type', 'vehicle_category']);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', (int) $request->input('brand_id'));
        }

        if ($request->filled('vehicle_type_id')) {
            $vehicleTypeId = (int) $request->input('vehicle_type_id');
            $query->where('vehicle_type_id', $vehicleTypeId);
        }

        if ($request->filled('vehicle_type')) {
            $vehicleType = (string) $request->input('vehicle_type');
            $query->where(function ($q) use ($vehicleType) {
                $q->where('vehicle_type', $vehicleType)
                    ->orWhereHas('vehicleType', function ($typeQuery) use ($vehicleType) {
                        $typeQuery->where('slug', strtolower($vehicleType))
                            ->orWhere('name', $vehicleType);
                    });
            });
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $models = $query->orderBy('name')->get();

        return $this->apiResponse(false, 0, 'Vehicle models retrieved successfully.', $models);
    }
}
