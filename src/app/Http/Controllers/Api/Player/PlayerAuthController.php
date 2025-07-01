<?php

namespace App\Http\Controllers\Api\Player;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PlayerAuthController extends Controller
{

    public function __construct(){

        $this->middleware('auth:api', ['except'=> ['login', 'register']]);
    }

    public function register(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:players',
            'email' => 'required|email|unique:players', //check the players table
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 means the server understands the request, but the input data is invalid
        }

        $player = Player::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($player);

        return response()->json([
            'message' => 'Player registered successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ], 201); // 201 means created
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email', 
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credantials = $request->only('email', 'password');

        if(! $token = Auth::guard('api')->attempt($credantials)) {
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
