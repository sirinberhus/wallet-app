<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackofficeLoginRequest;
use Illuminate\Support\Facades\Auth;

class BoAuthController extends Controller
{
    public function login(BackofficeLoginRequest $request)
    {

        $validatedData = $request->validated();

        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ];

        if (! $token = Auth::guard('bo-api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized: Invalid credentials'
            ], 401); // Unauthorized
        }

        $user = Auth::guard('bo-api')->user();
        if (! $user->is_admin) {
            return response()->json([
                'error' => 'Access Denied: Not an admin user'
            ], 403); // forbidden
        }

        return response()->json([
            'message' => 'Backoffice Agent logged in successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('bo-api')->factory()->getTTL() * 60 // TTL means time-to-live
        ]);

    }

    public function viewProfile()
    {

        $user = Auth::user();
        return response()->json($user);
    }
}
