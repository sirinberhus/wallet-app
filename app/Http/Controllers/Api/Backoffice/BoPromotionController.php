<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\CreatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Services\PromotionService;
use Exception;
use Illuminate\Support\Facades\Cache;

class BoPromotionController extends Controller
{
    public function getPromotions()
    {

        $promotions = Promotion::with('rewards')->paginate(10);
        return PromotionResource::collection($promotions);
    }

    public function createPromotion(CreatePromotionRequest $request, PromotionService $promotionService)
    {

        $validatedData = $request->validated();

        try {

            $promotion = $promotionService->create($validatedData);

            return response()->json([
                'message' => 'Promotion and rewards created successfully',
                'promotion' => $promotion->load('rewards') // load rewards relationship
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to create promotion',
                'details' => $e->getMessage(),
            ], 500); //internal server error
        }

    }

    public function deletePromotion($id)
    {

        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response([
                'error' => 'Promotion Not Found!',
            ], 404);
        }

        $claimedPromotion = $promotion->players()->exists();
        $usedInTransactions = $promotion->rewards()->whereHas('transactions')->exists();

        if ($claimedPromotion || $usedInTransactions) {
            return response()->json([
                'error' => 'Promotion has been claimed by players or used in transactions and can not be deleted',
            ]);
        }

        try {

            $promotion->delete();

            return response()->json([
                'message' => 'Promotion Deleted Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete promotion',
                'details' => $e->getMessage(),
            ]);
        }
    }

    public function changeStatus(ChangeStatusRequest $request, $id)
    {

        $promotion = Promotion::find($id);

        if (!$promotion) {
            return response()->json([
                'error' => 'promotion not found'
            ], 404);
        }

        $validatedData = $request->validated();

        $promotion->is_active = $validatedData['is_active'];
        $promotion->save();

        $cachePromotionKey = 'active:promotions';
        Cache::forget($cachePromotionKey);

        return response()->json([
            'message' => 'Promotion status updated',
            'promotion' => [
                'id' => $promotion->id,
                'code' => $promotion->code,
                'is_active' => $promotion->is_active
            ]
        ]);

    }

}
