<?php

namespace App\Http\Controllers\Api\Backoffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BoAuthController extends Controller
{
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 means data input is invalid
        }

        $credentials = $request->only('email', 'password');

        if(! $token = Auth::guard('bo-api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized: Invalid credentials'
            ], 401); // Unauthorized
        }

        $user = Auth::guard('bo-api')->user();
        if(! $user->is_admin) {
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

    public function viewProfile() {
        
        $user = Auth::user();
        return response()->json($user);
    }
}
