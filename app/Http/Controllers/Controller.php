<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Return a standardized API json response.
     */
    protected function apiResponse(bool $hasError, int $errorCode, string $message, mixed $data): JsonResponse
    {
        return response()->json([
            'hasError' => $hasError,
            'errorCode' => $errorCode,
            'message' => $message,
            'data' => $data,
        ], $hasError ? $errorCode : 200);
    }
}
