<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AzureAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // Get user from Azure via provided access token
            $azureUser = Socialite::driver('azure')->userFromToken($request->access_token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('errors.invalid_azure_token'),
                'code' => 'unauthenticated',
                'errors' => null,
            ], 401);
        }

        $email = $azureUser->getEmail();
        if (! $email) {
            return response()->json([
                'message' => __('errors.azure_email_missing'),
                'code' => 'azure_email_missing',
                'errors' => null,
            ], 400);
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $azureUser->getName(),
                'password' => bcrypt(Str::random(16)),
            ]
        );

        $user->update(['last_login_at' => now()]);

        // Issue Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->noContent();
    }
}
