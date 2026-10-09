<?php

namespace App\Http\Controllers\Api;

use App\Enums\VehicleWorkCycle;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleWork;
use App\Models\VehicleWorkType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleWorkController extends Controller
{
    /**
     * Display a listing of vehicle works for the user or a specific vehicle.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = VehicleWork::query()
            ->when($user, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with([
                'vehicle:id,registration_number,model_name',
                'workType:id,name,slug,icon',
            ]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', (int) $request->input('vehicle_id'));
        }

        $works = $query->latest('id')->get();

        return $this->apiResponse(false, 0, 'Vehicle works retrieved successfully.', $works);
    }

    /**
     * Store details of work to be done regarding a vehicle.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'type_id' => ['required', 'integer', 'exists:vehicle_work_types,id'],
            'date' => ['required', 'string', 'date_format:d-m-Y,j-n-Y'],
            'cycle' => ['required', 'integer', Rule::enum(VehicleWorkCycle::class)],
        ]);

        $user = $request->user();
        $vehicle = $user
            ? $user->vehicles()->find($data['vehicle_id'])
            : Vehicle::find($data['vehicle_id']);

        if (! $vehicle) {
            return $this->apiResponse(
                true,
                404,
                'Vehicle not found.',
                [
                    'isSuccess' => false,
                    'statusMessage' => 'Vehicle not found or does not belong to the authenticated user.',
                ],
            );
        }

        try {
            $workDate = Carbon::createFromFormat('d-m-Y', $data['date'])->format('Y-m-d');
        } catch (\Throwable) {
            $workDate = Carbon::parse($data['date'])->format('Y-m-d');
        }

        VehicleWork::create([
            'user_id' => $vehicle->user_id,
            'vehicle_id' => $vehicle->id,
            'type_id' => (int) $data['type_id'],
            'title' => trim($data['title']),
            'date' => $workDate,
            'cycle' => (int) $data['cycle'],
        ]);

        return $this->apiResponse(
            false,
            0,
            'Vehicle work details added successfully.',
            [
                'isSuccess' => true,
                'statusMessage' => 'Vehicle work details added successfully.',
            ],
        );
    }

    /**
     * Return list of active vehicle work types.
     */
    public function types(Request $request): JsonResponse
    {
        $query = VehicleWorkType::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'icon']);

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $workTypes = $query->orderBy('id')->get();

        return $this->apiResponse(false, 0, 'Vehicle work types retrieved successfully.', $workTypes);
    }

    /**
     * Return vehicle info types with static response.
     */
    public function infoTypes(): JsonResponse
    {
        return $this->apiResponse(
            false,
            0,
            'Vehicle info types retrieved successfully.',
            [
                'types' => [
                    [
                        'id' => 1,
                        'name' => 'Alert',
                    ],
                ],
            ],
        );
    }

    /**
     * Return list of added vehicle works.
     * Filter by vehicle id if provided; if null, return all vehicle works for the user.
     */
    public function infoList(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['nullable', 'integer'],
            'vehicle_id' => ['nullable', 'integer'],
        ]);

        $vehicleId = $request->input('id') ?? $request->input('vehicle_id');
        $user = $request->user();

        if (! empty($vehicleId)) {
            $vehicle = $user->vehicles()->find($vehicleId);

            if (! $vehicle) {
                return $this->apiResponse(
                    true,
                    404,
                    'Vehicle not found.',
                    [
                        'status' => [],
                    ],
                );
            }
        }

        $works = $user->vehicleWorks()
            ->when(! empty($vehicleId), function ($q) use ($vehicleId) {
                $q->where('vehicle_id', $vehicleId);
            })
            ->with('type')
            ->latest('id')
            ->get();

        $status = $works->map(function (VehicleWork $work) {
            return [
                'id' => $work->id,
                'vehicle_id' => $work->vehicle_id,
                'type' => $work->type ? [
                    'id' => $work->type->id,
                    'name' => $work->type->name,
                    'slug' => $work->type->slug,
                    'icon' => $work->type->icon,
                ] : null,
                'title' => $work->title,
                'date' => $work->date ? $work->date->format('d-m-Y') : '',
                'cycle' => $work->cycle instanceof VehicleWorkCycle ? $work->cycle->value : (int) $work->cycle,
            ];
        })->values()->all();

        return $this->apiResponse(
            false,
            0,
            'Vehicle work info list retrieved successfully.',
            [
                'status' => $status,
            ],
        );
    }
}
