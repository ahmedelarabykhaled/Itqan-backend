<?php

namespace App\Http\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialTokenVerifier
{
    /**
     * Verify a social provider token and return the payload.
     *
     * @return array{email: string, sub: string}|null
     */
    public function verify(string $provider, string $token): ?array
    {
        return match ($provider) {
            'google' => $this->verifyGoogleToken($token),
            'apple' => $this->verifyAppleToken($token),
            default => null,
        };
    }

    /**
     * Verify a Google ID token using the Google API Client.
     *
     * @return array{email: string, sub: string}|null
     */
    private function verifyGoogleToken(string $token): ?array
    {
        try {
            $client = new GoogleClient([
                'client_id' => config('services.google.client_id'),
            ]);

            $payload = $client->verifyIdToken($token);

            if (! $payload) {
                return null;
            }

            return [
                'email' => $payload['email'] ?? null,
                'sub' => $payload['sub'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('Google token verification failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify an Apple Sign In identity token (JWT).
     *
     * @return array{email: string, sub: string}|null
     */
    private function verifyAppleToken(string $token): ?array
    {
        try {
            $publicKeys = $this->getApplePublicKeys();

            if (empty($publicKeys)) {
                return null;
            }

            $decoded = JWT::decode($token, $publicKeys);

            if ($decoded->iss !== 'https://appleid.apple.com') {
                return null;
            }

            if ($decoded->aud !== config('services.apple.client_id')) {
                return null;
            }

            return [
                'email' => $decoded->email ?? null,
                'sub' => $decoded->sub ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('Apple token verification failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch and cache Apple's public keys for JWT verification.
     *
     * @return array<string, \Firebase\JWT\Key>
     */
    private function getApplePublicKeys(): array
    {
        $jwks = Cache::remember('apple_public_keys', now()->addHours(6), function () {
            $response = Http::get('https://appleid.apple.com/auth/keys');

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if (! $jwks || empty($jwks['keys'])) {
            return [];
        }

        return JWK::parseKeySet($jwks, 'RS256');
    }
}
