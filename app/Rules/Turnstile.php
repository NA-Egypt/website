<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        // In testing environment without explicit secret testing or if not set, pass
        if (app()->environment('testing') && empty(request()->header('X-Test-Turnstile-Verification'))) {
            return;
        }

        if (empty($secretKey)) {
            return;
        }

        if (empty($value) || !is_string($value)) {
            $fail(__('messages.turnstile_required'));
            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful() || !$response->json('success')) {
                Log::warning('Turnstile verification failed', [
                    'errors' => $response->json('error-codes'),
                    'ip' => request()->ip(),
                ]);
                $fail(__('messages.turnstile_failed'));
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile verification exception: ' . $e->getMessage());
            $fail(__('messages.turnstile_failed'));
        }
    }
}
