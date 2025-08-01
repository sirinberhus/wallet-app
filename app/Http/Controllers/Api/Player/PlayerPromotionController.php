<?php

namespace App\Http\Controllers\Api\Player;

use App\Models\Promotion;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PlayerPromotionController extends Controller
{
    //Get /api/promotions -> list of all currently active promotions available to claim

    public function promotions() {

        $cacheKey = 'active:promotions';
        $ttl = 600;

        $promotions = Cache::remember($cacheKey,$ttl, function() {
           return Promotion::with('rewards')->where('is_active', 'true')->get();
        });

        return response()->json([$promotions]);
    }

    //Post /api/claimPromotion 

    public function claimPromotion(Request $request) {
        //Ensure the claim request has valid promotion code (created by bo)
        $validator = Validator::make($request->all(), [
            'promotion_code' => 'required|string|exists:promotions,code' //code must exist in promotions table -> code column
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(),422); // 422 means Unprocessable entitty
        }

        $player = Auth::user();
        // SELECT * FROM promotions WHERE code = '$request->promotion_code'
        //get the ->first() result (if find) 
        $promotion = Promotion::where('code', $request->promotion_code)->first();

        if(! $promotion->is_active) {
            return response([
                'message'=> 'This promotion is no longer active :(',

            ],400);
        }

        //check if the user has already this promotion
        if($player->promotions()
        ->where('promotion_id', $promotion->id)
        ->exists()) {
        return response()->json([
            'message'=> 'You have already claimed this promotion'
        ], 400);
    }
        $player->promotions()->attach($promotion->id, ['claimed_at'=> now()]);

        // Update balance and record the transaction

        $totalReward = 0;
        foreach($promotion->rewards as $reward) {
            $amount = $reward->amount;

            $balance = $player->balance;
            $balance->balance += $amount;
            $balance->save();

            Transaction::create([
                'player_id' => $player->id,
                'type' => 'PROMOTION',
                'amount' => $amount,
                'reference_id' => $promotion->id,
                'promotion_reward_id' => $reward->id,
                'processed_by' => null,
            ]);

            $totalReward += $amount;
        }
        // Invalidate the cache for this player's balance after changes.
        $balanceCacheKey = 'player:'.$player->id.':balance';
        Cache::forget($balanceCacheKey);

        return response()->json([
            'message'=> 'Promotion claimed successfully!',
            'promotion_name' => $promotion->name,
            'reward_amount_total' => $totalReward,
            'new_balance' => $player->balance->balance,
        ]);

    }

}
