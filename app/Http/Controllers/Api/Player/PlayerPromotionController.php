<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimPromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Services\PromotionService;
use Exception;
use Illuminate\Http\JsonResponse;
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
            return Promotion::with('rewards')->where('is_active', true)->get();
        });
        

        return PromotionResource::collection($promotions);
    }

    //Post /api/claimPromotion

    public function claimPromotion(ClaimPromotionRequest $request, PromotionService $promotionService)
    {
        $validatedData = $request->validated();

        /**
         * @var \App\Models\Player $player
         * */
        $player = Auth::user();

        $promotion = Promotion::where('code', $validatedData['promotion_code'])->first();

        if(! $promotion) {
            return response(['message' => 'Promotion not found',], 404);
        }

        if (! $promotion->is_active) {
            return response(['message' => 'This promotion is no longer active :('], 400);
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
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
