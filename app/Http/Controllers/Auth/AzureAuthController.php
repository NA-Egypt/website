<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AzureAuthController extends Controller
{
    /**
     * Redirect mobile app users to Azure AD authorization URL.
     */
    public function redirectForMobile(Request $request)
    {
        $redirectUri = $request->query('redirect_uri', 'naegypt://auth-callback');
        session(['mobile_redirect_uri' => $redirectUri]);

        return Socialite::driver('azure')
            ->stateless()
            ->with(['tenant' => env('AZURE_TENANT_ID')])
            ->redirect()
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    /**
     * Handle callback from Azure AD for mobile authentication.
     */
    public function handleCallbackForMobile(Request $request)
    {
        $azureUser = Socialite::driver('azure')->stateless()->user();
        
        $email = $azureUser->getEmail() ?: ($azureUser->getUser()['mail'] ?? $azureUser->getUser()['userPrincipalName'] ?? 'servant@egyptna.org');
        $name = $azureUser->getName() ?: 'Servant';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(Str::random(16)),
            ]
        );

        $token = $user->createToken('mobile-app')->plainTextToken;

        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [];

        $userJson = urlencode(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roles,
        ]));

        $redirectUri = session('mobile_redirect_uri', 'naegypt://auth-callback');
        session()->forget('mobile_redirect_uri');

        return redirect("{$redirectUri}?token={$token}&user={$userJson}");
    }
}
