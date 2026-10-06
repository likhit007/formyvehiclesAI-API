<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $vehicles = $request->user()
            ->vehicles()
            ->with([
                'vehicleType:id,name,slug,icon',
                'brand:id,name,vehicle_type,logo',
                'vehicleModel:id,name,vehicle_type,vehicle_category',
            ])
            ->latest('id')
            ->get();

        return $this->apiResponse(false, 0, 'Vehicles retrieved successfully.', $vehicles);
    }

    /**
     * Store a newly created vehicle for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $currentYear = (int) date('Y');

        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:20'],
            'vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'vehicle_model_id' => ['nullable', 'integer', 'exists:vehicle_models,id'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'model_name' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 1)],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'gear_type' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'seating_capacity' => ['nullable', 'string', 'max:50'],
            'mileage_km' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'is_taxi' => ['nullable', 'boolean'],
            'image_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['registration_number'] = strtoupper(trim($data['registration_number']));

        if (! empty($data['vehicle_type_id']) && empty($data['vehicle_type'])) {
            $vType = VehicleType::find($data['vehicle_type_id']);
            if ($vType) {
                $data['vehicle_type'] = $vType->name;
            }
        }

        if (! empty($data['vehicle_model_id'])) {
            $selectedModel = VehicleModel::find($data['vehicle_model_id']);
            if ($selectedModel) {
                if (empty($data['model_name'])) {
                    $data['model_name'] = $selectedModel->name;
                }
                if (empty($data['vehicle_type_id']) && $selectedModel->vehicle_type_id) {
                    $data['vehicle_type_id'] = $selectedModel->vehicle_type_id;
                }
                if (empty($data['vehicle_type']) && $selectedModel->vehicle_type) {
                    $data['vehicle_type'] = $selectedModel->vehicle_type;
                }
                if (empty($data['category']) && $selectedModel->vehicle_category) {
                    $data['category'] = $selectedModel->vehicle_category;
                }
            }
        }

        $vehicle = $request->user()->vehicles()->create($data);
        $vehicle->load([
            'vehicleType:id,name,slug,icon',
            'brand:id,name,vehicle_type,logo',
            'vehicleModel:id,name,vehicle_type,vehicle_category',
        ]);

        return $this->apiResponse(false, 0, 'Vehicle added successfully.', ['is_vehicle_added' => true, 'vehicle' => $vehicle]);
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $vehicle = $request->user()
            ->vehicles()
            ->with(['vehicleType', 'brand', 'vehicleModel'])
            ->find($id);

        if (! $vehicle) {
            return $this->apiResponse(true, 404, 'Vehicle not found.', null);
        }

        return $this->apiResponse(false, 0, 'Vehicle retrieved successfully.', $vehicle);
    }

    /**
     * Update the specified vehicle.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $vehicle = $request->user()->vehicles()->find($id);

        if (! $vehicle) {
            return $this->apiResponse(true, 404, 'Vehicle not found.', null);
        }

        $currentYear = (int) date('Y');

        $data = $request->validate([
            'registration_number' => ['sometimes', 'required', 'string', 'max:20'],
            'vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'brand_id' => ['sometimes', 'required', 'integer', 'exists:brands,id'],
            'vehicle_model_id' => ['nullable', 'integer', 'exists:vehicle_models,id'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'model_name' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 1)],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'gear_type' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:50'],
            'seating_capacity' => ['nullable', 'string', 'max:50'],
            'mileage_km' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'is_taxi' => ['nullable', 'boolean'],
            'image_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if (isset($data['registration_number'])) {
            $data['registration_number'] = strtoupper(trim($data['registration_number']));
        }

        if (! empty($data['vehicle_type_id']) && empty($data['vehicle_type'])) {
            $vType = VehicleType::find($data['vehicle_type_id']);
            if ($vType) {
                $data['vehicle_type'] = $vType->name;
            }
        }

        $vehicle->update($data);
        $vehicle->load([
            'vehicleType:id,name,slug,icon',
            'brand:id,name,vehicle_type,logo',
            'vehicleModel:id,name,vehicle_type,vehicle_category',
        ]);

        return $this->apiResponse(false, 0, 'Vehicle updated successfully.', $vehicle);
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $vehicle = $request->user()->vehicles()->find($id);

        if (! $vehicle) {
            return $this->apiResponse(true, 404, 'Vehicle not found.', null);
        }

        $vehicle->delete();

        return $this->apiResponse(false, 0, 'Vehicle deleted successfully.', null);
    }
}
