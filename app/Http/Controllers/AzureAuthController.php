<?php

namespace App\Http\Controllers;

use Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AzureAuthController extends Controller
{
    public function redirectToAzure(Request $request)
    {
        $host = $request->getHost();
        $locale = LaravelLocalization::getCurrentLocale();

        // If the user initiates login on staging (egyptna.org), redirect to production as a bridge
        if ($host === 'egyptna.org') {
            return redirect()->away("https://naegypt.org/{$locale}/login/microsoft?from=https://egyptna.org");
        }

        // On production, if we have a 'from' parameter, we pass it via encrypted state
        $stateData = [];
        if ($request->has('from')) {
            $stateData['from'] = $request->query('from');
            $stateData['locale'] = $locale;
        }

        \Log::info('redirectToAzure: request host: ' . $host . ' from param: ' . $request->query('from') . ' stateData: ' . json_encode($stateData));

        $azure = Socialite::driver('azure')->stateless();

        if (!empty($stateData)) {
            $azure->with(['state' => encrypt($stateData)]);
        }

        return $azure->with(['tenant' => env('AZURE_TENANT_ID')])
            ->redirect()
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    public function handleAzureCallback(Request $request)
    {
        \Log::info('handleAzureCallback: Request all params: ' . json_encode($request->all()));
        $azureUser = Socialite::driver('azure')->stateless()->user();
        $email = $azureUser->getEmail();
        $name = $azureUser->getName();

        // Check if there is a pending redirect back to staging encoded in state
        $state = $request->query('state');
        \Log::info('handleAzureCallback: state query param: ' . ($state ?? 'null'));
        $redirectBack = null;
        $locale = LaravelLocalization::getCurrentLocale();

        if ($state) {
            try {
                $stateData = decrypt($state);
                if (is_array($stateData)) {
                    $redirectBack = $stateData['from'] ?? null;
                    $locale = $stateData['locale'] ?? $locale;
                }
            } catch (\Exception $e) {
                // Ignore decryption failures
            }
        }

        if ($redirectBack && str_contains($redirectBack, 'egyptna.org')) {
            $expires = time() + 300; // 5 minutes validity
            $signature = hash_hmac('sha256', $email . '|' . $name . '|' . $expires, config('app.key'));

            $bridgeUrl = rtrim($redirectBack, '/') . '/' . $locale . '/login/bridge?' . http_build_query([
                'email' => $email,
                'name' => $name,
                'expires' => $expires,
                'signature' => $signature,
            ]);

            return redirect()->away($bridgeUrl);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function handleBridgeCallback(Request $request)
    {
        $email = $request->query('email');
        $name = $request->query('name');
        $expires = $request->query('expires');
        $signature = $request->query('signature');

        if (!$email || !$expires || !$signature) {
            abort(403, 'Invalid bridge request parameters.');
        }

        if (time() > (int) $expires) {
            abort(403, 'The login link has expired.');
        }

        $expectedSignature = hash_hmac('sha256', $email . '|' . $name . '|' . $expires, config('app.key'));

        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid signature.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name]
            );
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();

        // Microsoft logout URL with redirect back to dashboard
        $logoutUrl = 'https://login.microsoftonline.com/478baa9e-715e-47cb-adb3-60cd287349ca/oauth2/v2.0/logout';

        return redirect($logoutUrl.'?post_logout_redirect_uri='.urlencode(route('frontend.home')));
    }
}
