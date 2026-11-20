<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class TurnstileVerifier
{
    public static function isEnabled(): bool
    {
        return ! empty(config('services.turnstile.secret_key'));
    }

    public static function verify(?string $token, ?string $remoteIp = null): bool
    {
        $secret = (string) config('services.turnstile.secret_key');

        if ($secret === '') {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $payload = [
                'secret' => $secret,
                'response' => $token,
            ];

            if (! empty($remoteIp)) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $payload);

            return $response->successful() && (bool) $response->json('success', false);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
