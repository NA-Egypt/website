<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

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
            return response()->json(['error' => 'Invalid Azure Token'], 401);
        }

        $email = $azureUser->getEmail();
        if (!$email) {
            return response()->json(['error' => 'Email not found from Azure token'], 400);
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $azureUser->getName(),
                'password' => bcrypt(Str::random(16))
            ]
        );

        // Issue Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
