<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Return list of states.
     */
    public function index(Request $request): JsonResponse
    {
        $query = State::query()->select(['id', 'name', 'code']);

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $states = $query->orderBy('name')->get();

        return $this->apiResponse(false, 0, 'States retrieved successfully.', $states);
    }
}
