<?php

namespace App\Http\Controllers\Api\Backoffice;

use Exception;
use App\Models\Player;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BoPromotionController extends Controller
{
    public function getPromotions() {

        $promotions = Promotion::with('rewards')->paginate(10);
        return response()->json($promotions);
    }

    public function createPromotion(Request $request) {

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:promotions,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from', //make sure valid_to dont start before valid_from
            'is_active' => 'boolean',
            'rewards' => 'required|array|min:1',
            // * means check that every <item> in each object of the rewards array.
            'rewards.*.type' => 'required|string|in:CASH,BONUS_SPIN,FREE_BET,OTHER',
            'rewards.*.amount' => 'required|numeric|min:0',
            'rewards.*.currency' => 'required|string|size:3'
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],422); // Unprocessable entity
        }

        // This method returns an array of the data that was validated
        $validated = $validator->validated();

        // start transaction manually, if anything fails during creation,nothing is saved- rollback
        DB::beginTransaction();
        try{
            $promotion = Promotion::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'valid_from' => $validated['valid_from'],
                'valid_to' => $validated['valid_to'],
                'is_active' => $validated['is_active'],
                'created_by' => Auth::guard('bo-api')->id(), // promotion creator who is creating this promotion
            ]);

            // create rewards  -- look Promotion model rewards() 
            foreach($validated['rewards'] as $rewardData) {    //Eloquent Serialization
                $promotion->rewards()->create($rewardData);
            } 

            DB::commit();

            return response()->json([
                'message' => 'Promotion and rewards created successfully',
                'promotion' => $promotion->load('rewards') // load rewards relationship
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create promotion',
                'details' => $e->getMessage(),
            ],500); //internal server error
        }

    }

    public function deletePromotion($id) {

        $promotion = Promotion::find($id);

        if(!$promotion) {
            return response([
                'error' => 'Promotion Not Found!',
            ],404);
        }

        $claimedPromotion = $promotion->players()->exists();
        $usedInTransactions = $promotion->rewards()->whereHas('transactions')->exists();

        if($claimedPromotion || $usedInTransactions) {
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

    public function changeStatus(Request $request, $id) {

        $promotion = Promotion::find($id);

        if(!$promotion) {
            return response()->json([
                'error' => 'promotion not found'
            ],404);
        }

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean'
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ],422); // Unprocessable
        }

        $promotion->is_active = $request->is_active;
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
