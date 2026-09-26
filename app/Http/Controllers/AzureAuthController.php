<?php

namespace App\Http\Controllers;

use Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AzureAuthController extends Controller
{
    public function redirectToAzure(Request $request)
    {
        if ($request->has('mobile')) {
            session(['mobile_auth_redirect' => true]);
        } else {
            session()->forget('mobile_auth_redirect');
        }

        return Socialite::driver('azure')
            ->stateless()
            ->with(['tenant' => env('AZURE_TENANT_ID')])
            ->redirect()
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    public function handleAzureCallback(Request $request)
    {
        // If user is already authenticated (e.g. back button, refreshed callback), take them straight to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        try {
            $azureUser = Socialite::driver('azure')->stateless()->user();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Azure OAuth callback failed: ' . $e->getMessage());

            if (Auth::check()) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('frontend.home')->with('error', __('messages.auth_failed_retry'));
        }

        $email = $azureUser->getEmail();
        $name = $azureUser->getName();

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name]
        );

        Auth::login($user);

        $user->update(['last_login_at' => now()]);

        if (session('mobile_auth_redirect')) {
            session()->forget('mobile_auth_redirect');
            $token = $user->createToken('mobile-app')->plainTextToken;
            return redirect('naegypt://auth-callback?token=' . urlencode($token));
        }

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
