<?php

namespace App\Http\Controllers\Api\Player;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PlayerController extends Controller
{
    // Get /api/me -> returns authenticated player's profile info

    public function viewProfile() {
       
        $player = Auth::user();  // don't need to add auth:api since we are using middleware in routes
        
        $cacheKey = 'player:'.$player->id.':profile'; // a unique cache key for each player's profile
        $ttl = 600; // how long chache the data (60 * 10 ten minutes)

        $playerProfile = Cache::remember($cacheKey,$ttl, function() use ($player) { //closure function
            return $player->toArray(); // if cache is empty or not yet created, fetch from db
        });
        return response()->json($playerProfile);

    } 

    // Get /api/balance 

    public function viewBalance() {

        $player = Auth::user();
        $cacheKey = 'player:'.$player->id.':balance';
        $ttl = 600;

        $playerBalance = Cache::remember($cacheKey,$ttl, function() use ($player) {
                 return  $player->balance;
        });

        return response()->json([
            'balance'=> $player->balance
        ]);
    }
}
