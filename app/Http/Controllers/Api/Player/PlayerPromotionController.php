<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimPromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Services\PromotionService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PlayerPromotionController extends Controller
{
    //Get /api/promotions -> list of all currently active promotions available to claim

    public function promotions()
    {

        $cacheKey = 'active:promotions';
        $ttl = 600;

        $promotions = Cache::remember($cacheKey, $ttl, function () {
            return Promotion::with('rewards')->where('is_active', 'true')->get();
        });

        return PromotionResource::collection($promotions);
    }

    //Post /api/claimPromotion

    public function claimPromotion(ClaimPromotionRequest $request, PromotionService $promotionService)
    {

        //Ensure the claim request has valid promotion code (created by bo)
        $validatedData = $request->validated();

        /**
         * @var \App\Models\Player $player
         * */
        $player = Auth::user();

        // SELECT * FROM promotions WHERE code = '$request->promotion_code'
        //get the ->first() result (if find)
        $promotion = Promotion::where('code', $validatedData['promotion_code'])->first();

        if (! $promotion->is_active) {
            return response([
                'message' => 'This promotion is no longer active :(',

            ], 400);
        }

        //check if the user has already this promotion
        if ($player->promotions()
        ->where('promotion_id', $promotion->id)
        ->exists()) {
            return response()->json([
                'message' => 'You have already claimed this promotion'
                ], 400);

        }

        try {

            $result = $promotionService->claim($player, $promotion); //dependency injection

            return response()->json([
            'message' => 'Promotion claimed successfully!',
            'promotion_name' => $promotion->name,
            'reward_amount_total' => $result['total_reward'],
            'new_balance' => $result['new_balance'],
            'reference_id' => $result['transaction_references']
        ]);
        } catch (Exception $e) {

            return response()->json([
                'error' => 'An unexpected error occured while claiming the promotion.',
                'details' => $e->getMessage()
            ]);
        }

    }

}
