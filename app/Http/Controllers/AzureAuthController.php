<?php

namespace App\Http\Controllers;

use Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AzureAuthController extends Controller
{
    public function redirectToAzure()
    {
        return Socialite::driver('azure')
            ->with(['tenant' => env('AZURE_TENANT_ID')])
            ->redirect()
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    public function handleAzureCallback()
    {
        $azureUser = Socialite::driver('azure')->user();
//dd($azureUser);
        $email = $azureUser->getEmail();
        $domain = substr(strrchr($email, "@"), 1);

//        if ($domain !== env('ALLOWED_DOMAIN')) {
//            abort(403, 'Only @naegypt.org emails are allowed.');
//        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $azureUser->getName()]
        );

        Auth::login($user);

//        return redirect()->intended('/dashboard');
        return redirect()->route('dashboard');

    }

//    public function logout()
//    {
//        Auth::logout();
//
//        // If you want to log out of Microsoft's session as well, redirect to their logout endpoint
//        return redirect('https://login.microsoftonline.com/'.env('AZURE_TENANT_ID').'/oauth2/v2.0/logout?post_logout_redirect_uri='.urlencode(route('dashboard')));
//
//        // For just local logout (without Microsoft logout), use:
////         return redirect()->route('dashboard');
//    }

    public function logout()
    {
        Auth::logout();

        // Microsoft logout URL with redirect back to dashboard
        $logoutUrl = 'https://login.microsoftonline.com/478baa9e-715e-47cb-adb3-60cd287349ca/oauth2/v2.0/logout';

        return redirect($logoutUrl.'?post_logout_redirect_uri='.urlencode(route('frontend.home')));
    }
}
