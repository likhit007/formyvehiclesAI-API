<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\State;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected JwtService $jwtService,
    ) {}

    public function signup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'state_name' => ['nullable', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'mobile_number' => ['required', 'string', 'regex:/^[0-9+()\-\s]+$/', 'max:20'],
            'terms_accepted' => ['required', 'boolean'],
        ]);

        $mobileNumber = $this->normalizeMobileNumber($data['mobile_number']);

        if (User::where('mobile_number', $mobileNumber)->exists()) {
            return $this->apiResponse(true, 409, 'User already exists. Please login instead.', null);
        }

        $stateId = $data['state_id'] ?? null;
        if (empty($stateId) && ! empty($data['state_name'])) {
            $state = State::firstOrCreate(
                ['name' => trim($data['state_name'])],
                [
                    'code' => $this->stateCodeFromName($data['state_name']),
                ],
            );
            $stateId = $state->id;
        }

        if (empty($stateId)) {
            $state = State::firstOrCreate(
                ['name' => 'Other'],
                ['code' => 'OTH'],
            );
            $stateId = $state->id;
        }

        $user = User::create([
            'name' => $data['name'],
            'state_id' => $stateId,
            'country_code' => $data['country_code'],
            'mobile_number' => $mobileNumber,
            'terms_accepted' => (bool) $data['terms_accepted'],
        ]);

        // $otpCode = $this->generateOtp();
        $otpCode = '1234';
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
            return $this->apiResponse(true, 404, 'User not found.', null);
        }

        // $otpCode = $this->generateOtp();
        $otpCode = '1234';
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
            ->latest('id')
            ->first();

        if (! $otp) {
            return $this->apiResponse(true, 401, 'Invalid or expired OTP.', null);
        }

        $otp->update(['consumed' => true]);

        $user = $otp->user ?? User::find($otp->user_id);

        if (! $user) {
            return $this->apiResponse(true, 404, 'User not found.', null);
        }

        $accessToken = $this->jwtService->generateAccessToken($user);
        $refreshToken = $this->jwtService->generateRefreshToken($user);

        return $this->apiResponse(false, 0, 'OTP verified successfully.', [
            'token' => $accessToken,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtService->getTtlInSeconds(),
            'user' => $user->toArray(),
        ]);
    }

    /**
     * Refresh the access and refresh tokens.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token') ?? $request->bearerToken();

        if (! $refreshToken) {
            return $this->apiResponse(true, 400, 'Refresh token is required.', null);
        }

        $payload = $this->jwtService->validateRefreshToken((string) $refreshToken);

        if (! $payload) {
            return $this->apiResponse(true, 401, 'Invalid or expired refresh token.', null);
        }

        $user = User::find($payload['sub']);

        if (! $user) {
            return $this->apiResponse(true, 404, 'User not found.', null);
        }

        $newAccessToken = $this->jwtService->generateAccessToken($user);
        $newRefreshToken = $this->jwtService->generateRefreshToken($user);

        return $this->apiResponse(false, 0, 'Token refreshed successfully.', [
            'token' => $newAccessToken,
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->jwtService->getTtlInSeconds(),
            'user' => $user->toArray(),
        ]);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->apiResponse(false, 0, 'User profile retrieved successfully.', [
            'user' => $user?->toArray(),
        ]);
    }

    protected function storeOtp(User $user, string $mobileNumber, string $otpCode): void
    {
        OtpVerification::create([
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
}
