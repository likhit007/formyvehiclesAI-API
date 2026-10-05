<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class JwtService
{
    /**
     * Generate an access token (alias for backward compatibility).
     *
     * @param  array<string, mixed>  $customClaims
     */
    public function generateToken(User $user, ?int $ttlInMinutes = null, array $customClaims = []): string
    {
        return $this->generateAccessToken($user, $ttlInMinutes, $customClaims);
    }

    /**
     * Generate a signed JWT access token for the given user.
     *
     * @param  array<string, mixed>  $customClaims
     */
    public function generateAccessToken(User $user, ?int $ttlInMinutes = null, array $customClaims = []): string
    {
        $ttl = $ttlInMinutes ?? (int) config('jwt.ttl', 60);

        return $this->createToken($user, $ttl, array_merge(['type' => 'access'], $customClaims));
    }

    /**
     * Generate a signed JWT refresh token for the given user.
     *
     * @param  array<string, mixed>  $customClaims
     */
    public function generateRefreshToken(User $user, ?int $ttlInMinutes = null, array $customClaims = []): string
    {
        $ttl = $ttlInMinutes ?? (int) config('jwt.refresh_ttl', 43200);

        return $this->createToken($user, $ttl, array_merge(['type' => 'refresh'], $customClaims));
    }

    /**
     * Create a signed JWT token with the specified TTL and claims.
     *
     * @param  array<string, mixed>  $claims
     */
    protected function createToken(User $user, int $ttlInMinutes, array $claims = []): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + ($ttlInMinutes * 60);

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = array_merge([
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ], $claims);

        $headerEncoded = $this->base64UrlEncode((string) json_encode($header));
        $payloadEncoded = $this->base64UrlEncode((string) json_encode($payload));

        $signature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $this->getSecret(), true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
    }

    /**
     * Validate and decode the JWT token payload.
     *
     * @return array<string, mixed>|null
     */
    public function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header64, $payload64, $signature64] = $parts;

        $expectedSignature = hash_hmac('sha256', "{$header64}.{$payload64}", $this->getSecret(), true);
        $providedSignature = $this->base64UrlDecode($signature64);

        if (! hash_equals($expectedSignature, $providedSignature)) {
            return null;
        }

        $header = json_decode($this->base64UrlDecode($header64), true);
        if (! is_array($header) || ($header['alg'] ?? null) !== 'HS256') {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payload64), true);
        if (! is_array($payload)) {
            return null;
        }

        $now = time();

        if (isset($payload['exp']) && $payload['exp'] < $now) {
            return null;
        }

        if (isset($payload['iat']) && $payload['iat'] > ($now + 60)) {
            return null;
        }

        return $payload;
    }

    /**
     * Validate that the token is a valid refresh token.
     *
     * @return array<string, mixed>|null
     */
    public function validateRefreshToken(string $token): ?array
    {
        $payload = $this->validateToken($token);

        if (! $payload || ($payload['type'] ?? null) !== 'refresh' || empty($payload['sub'])) {
            return null;
        }

        return $payload;
    }

    /**
     * Retrieve the User corresponding to the given JWT token.
     */
    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);

        if (! $payload || empty($payload['sub'])) {
            return null;
        }

        return User::find($payload['sub']);
    }

    /**
     * Authenticate an HTTP request using the Bearer token.
     * Only accepts access tokens.
     */
    public function authenticateRequest(Request $request): ?User
    {
        $token = $request->bearerToken();

        if (! $token) {
            return null;
        }

        $payload = $this->validateToken($token);

        if (! $payload || empty($payload['sub'])) {
            return null;
        }

        if (($payload['type'] ?? 'access') !== 'access') {
            return null;
        }

        return User::find($payload['sub']);
    }

    /**
     * Get the access token TTL in seconds.
     */
    public function getTtlInSeconds(): int
    {
        return ((int) config('jwt.ttl', 60)) * 60;
    }

    /**
     * Get the refresh token TTL in seconds.
     */
    public function getRefreshTtlInSeconds(): int
    {
        return ((int) config('jwt.refresh_ttl', 43200)) * 60;
    }

    /**
     * URL-safe Base64 encode.
     */
    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URL-safe Base64 decode.
     */
    public function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Resolve the signing secret key.
     */
    protected function getSecret(): string
    {
        $secret = config('jwt.secret', config('app.key'));

        if (is_string($secret) && str_starts_with($secret, 'base64:')) {
            $decoded = base64_decode(substr($secret, 7));
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return (string) $secret;
    }
}
