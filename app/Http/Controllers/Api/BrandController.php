<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Return list of brands.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'vehicle_type', 'logo']);

        if ($request->filled('vehicle_type_id') || $request->filled('vehicle_type')) {
            $vehicleTypeId = $request->filled('vehicle_type_id') ? (int) $request->input('vehicle_type_id') : null;
            $vehicleType = $request->filled('vehicle_type') ? (string) $request->input('vehicle_type') : null;

            $query->where(function ($q) use ($vehicleTypeId, $vehicleType) {
                // Match via brand_vehicle_type relationship
                $q->whereHas('vehicleTypes', function ($typeQuery) use ($vehicleTypeId, $vehicleType) {
                    if ($vehicleTypeId) {
                        $typeQuery->where('vehicle_types.id', $vehicleTypeId);
                    }
                    if ($vehicleType) {
                        $typeQuery->orWhere('vehicle_types.slug', strtolower($vehicleType))
                            ->orWhere('vehicle_types.name', $vehicleType);
                    }
                })
                // Or match via models under this brand
                    ->orWhereHas('models', function ($modelQuery) use ($vehicleTypeId, $vehicleType) {
                        if ($vehicleTypeId) {
                            $modelQuery->where('vehicle_type_id', $vehicleTypeId);
                        }
                        if ($vehicleType) {
                            $modelQuery->orWhere('vehicle_type', $vehicleType);
                        }
                    })
                // Or direct vehicle_type column match on brand (for legacy or direct match)
                    ->orWhere(function ($brandColQuery) use ($vehicleType) {
                        if ($vehicleType) {
                            $brandColQuery->where('vehicle_type', $vehicleType);
                        }
                    });
            });
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $brands = $query->orderBy('name')->get();

        return $this->apiResponse(false, 0, 'Brands retrieved successfully.', $brands);
    }
}
