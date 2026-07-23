<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use JsonException;

class JwtService
{
    public function issue(User $user): string
    {
        $now = now()->timestamp;
        $payload = [
            'iss' => config('app.url'),
            'sub' => (string) $user->getKey(),
            'role' => $user->role,
            'iat' => $now,
            'exp' => $now + (int) config('jwt.ttl', 3600),
        ];
        $segments = [
            $this->encode(['alg' => 'HS256', 'typ' => 'JWT']),
            $this->encode($payload),
        ];
        $segments[] = $this->base64UrlEncode(hash_hmac('sha256', implode('.', $segments), $this->secret(), true));

        return implode('.', $segments);
    }

    /**
     * @return array{sub: string, role: string, exp: int}
     *
     * @throws AuthenticationException
     */
    public function decode(string $token): array
    {
        try {
            $segments = explode('.', $token);
            if (count($segments) !== 3) {
                throw new AuthenticationException('Invalid token.');
            }
            [$encodedHeader, $encodedPayload, $encodedSignature] = $segments;
            $header = $this->decodeSegment($encodedHeader);
            $payload = $this->decodeSegment($encodedPayload);
            $expected = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $this->secret(), true);
            $signature = $this->base64UrlDecode($encodedSignature);

            if (($header['alg'] ?? null) !== 'HS256' || ! hash_equals($expected, $signature)) {
                throw new AuthenticationException('Invalid token.');
            }
            if (! isset($payload['sub'], $payload['role'], $payload['exp']) || (int) $payload['exp'] <= now()->timestamp) {
                throw new AuthenticationException('Token has expired.');
            }

            return $payload;
        } catch (JsonException) {
            throw new AuthenticationException('Invalid token.');
        }
    }

    private function secret(): string
    {
        $key = (string) config('app.key');

        return str_starts_with($key, 'base64:') ? (string) base64_decode(substr($key, 7), true) : $key;
    }

    /**
     * @param  array<string, mixed>  $value
     */
    private function encode(array $value): string
    {
        return $this->base64UrlEncode(json_encode($value, JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeSegment(string $value): array
    {
        return json_decode($this->base64UrlDecode($value), true, flags: JSON_THROW_ON_ERROR);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if ($decoded === false) {
            throw new AuthenticationException('Invalid token.');
        }

        return $decoded;
    }
}
