<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function signup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'state_id' => ['nullable', 'string'],
            'state_name' => ['nullable', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'mobile_number' => ['required', 'string', 'regex:/^[0-9+()\-\s]+$/', 'max:20'],
            'terms_accepted' => ['required', 'boolean'],
        ]);

        $mobileNumber = $this->normalizeMobileNumber($data['mobile_number']);

        if (User::where('mobile_number', $mobileNumber)->exists()) {
            return $this->apiResponse(true, 409, 'User already exists. Please login instead.', []);
        }

        $stateId = $data['state_id'] ?? null;
        if (empty($stateId) && ! empty($data['state_name'])) {
            $state = State::firstOrCreate(
                ['name' => trim($data['state_name'])],
                [
                    'id' => (string) Str::uuid(),
                    'code' => $this->stateCodeFromName($data['state_name']),
                ],
            );
            $stateId = $state->id;
        }

        if (empty($stateId)) {
            $state = State::firstOrCreate(
                ['name' => 'Other'],
                ['id' => (string) Str::uuid(), 'code' => 'OTH'],
            );
            $stateId = $state->id;
        }

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'state_id' => $stateId,
            'country_code' => $data['country_code'],
            'mobile_number' => $mobileNumber,
            'terms_accepted' => (bool) $data['terms_accepted'],
        ]);

        $otpCode = $this->generateOtp();
        $this->storeOtp($user, $mobileNumber, $otpCode);

        return $this->apiResponse(false, 0, 'OTP sent successfully.', [
            'user' => $user->toArray(),
            'otp' => [
                'code' => $otpCode,
            ],
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'country_code' => ['required', 'string', 'max:10'],
            'mobile_number' => ['required', 'string', 'regex:/^[0-9+()\-\s]+$/', 'max:20'],
        ]);

        $mobileNumber = $this->normalizeMobileNumber($data['mobile_number']);
        $user = User::where('mobile_number', $mobileNumber)->first();

        if (! $user) {
            return $this->apiResponse(true, 404, 'User not found.', []);
        }

        $otpCode = $this->generateOtp();
        $this->storeOtp($user, $mobileNumber, $otpCode);

        return $this->apiResponse(false, 0, 'OTP sent successfully.', [
            'user' => $user->toArray(),
            'otp' => [
                'code' => $otpCode,
            ],
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mobile_number' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:4'],
        ]);

        $mobileNumber = $this->normalizeMobileNumber($data['mobile_number']);
        $otp = OtpVerification::where('mobile_number', $mobileNumber)
            ->where('code', $data['code'])
            ->where('consumed', false)
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();

        if (! $otp) {
            return $this->apiResponse(true, 401, 'Invalid or expired OTP.', []);
        }

        $otp->update(['consumed' => true]);

        $user = User::find($otp->user_id);

        return $this->apiResponse(false, 0, 'OTP verified successfully.', [
            'user' => $user?->toArray(),
        ]);
    }

    protected function storeOtp(User $user, string $mobileNumber, string $otpCode): void
    {
        OtpVerification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'mobile_number' => $mobileNumber,
            'code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
            'consumed' => false,
        ]);
    }

    protected function generateOtp(): string
    {
        return (string) random_int(1000, 9999);
    }

    protected function normalizeMobileNumber(string $mobileNumber): string
    {
        return preg_replace('/\D+/', '', $mobileNumber) ?? '';
    }

    protected function stateCodeFromName(string $name): string
    {
        $cleanName = preg_replace('/[^A-Za-z]/', '', $name) ?: 'ST';

        return strtoupper(substr($cleanName, 0, 3));
    }

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
