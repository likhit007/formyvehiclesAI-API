<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    /**
     * Return list of active vehicle types.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VehicleType::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'icon']);

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $vehicleTypes = $query->orderBy('id')->get();

        return $this->apiResponse(false, 0, 'Vehicle types retrieved successfully.', $vehicleTypes);
    }
}
