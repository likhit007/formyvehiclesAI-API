<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppPolicyType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenericController extends Controller
{
    /**
     * Return app policy (Terms or Privacy Policy) based on type.
     */
    public function appPolicy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => [
                'required',
                'integer',
                Rule::in([
                    AppPolicyType::Terms->value,
                    AppPolicyType::PrivacyPolicy->value,
                ]),
            ],
        ]);

        $policyType = AppPolicyType::from((int) $data['type']);

        return $this->apiResponse(
            false,
            0,
            "{$policyType->label()} retrieved successfully.",
            [
                'content' => $policyType->content(),
            ],
        );
    }
}
