<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerLoginRequest;
use App\Http\Requests\PlayerRegistrationRequest;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlayerAuthController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(PlayerRegistrationRequest $request)
    {

        $validatedData = $request->validated();

        $player = Player::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = JWTAuth::fromUser($player);

        return response()->json([
            'message' => 'Player registered successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ], 201); // 201 means created
    }

    public function login(PlayerLoginRequest $request)
    {

        $validatedData = $request->validated();

        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ];

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized: Invalid credentials'
            ], 401); // 401 means credentials are missing or wrong - Unauthorized
        }

        return response()->json([
            'message' => 'Player logged in successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
