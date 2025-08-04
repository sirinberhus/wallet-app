<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Promotion;
use App\Models\Transaction;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PromotionService 
{

    /**
     * @param Player $player The player who claiming the promotion.
     * @param Promotion $promotion The promotion being claimed.
     * @return array An array containing  the results of the operation.
     */
    public function claim(Player $player, Promotion $promotion): array
    {
        $resultData = DB::transaction(function () use ($player, $promotion) {
            $player->promotions()->attach($promotion->id, ['claimed_at' => now()]);

            $totalReward = 0;
            $transactionReferences = [];

            foreach($promotion->rewards as $reward) {
                $amount = $reward->amount;

                $balance = $player->balance; //get relationship from the $player model
                $balance->balance += $amount; // $balance is an object of PlayerBalance
                $balance->save();

                $referenceId = 'player_'.$player->id.'_promo_'.$promotion->id.'_reward_'.$reward->id;

                Transaction::create([
                    'player_id' => $player->id,
                    'type' => 'PROMOTION',
                    'amount' => $amount,
                    'reference_id' => $referenceId,
                    'promotion_reward_id' => $reward->id,
                    'processed_by' => null
                ]);

                $transactionReferences[] = $referenceId;
                $totalReward += $amount;   
            }

            // finally, return the data we need from within the transaction
            return [
                'total_reward' => $totalReward,
                'transaction_references' => $transactionReferences
            ];
        }); 

        $balanceCachKey = 'player:'.$player->id.':balance';
        Cache::forget($balanceCachKey);

        $resultData['new_balance'] = $player->balance->balance;

        return $resultData;
    }

    /**
     * @param array $validatedData The validated data from request.
     * @return Promotion The newly created promotion model, with rewards loaded.
     */
    public function create(array $validatedData): Promotion
    {
        $promotion = DB::transaction(function () use ($validatedData) {
            $promotion = Promotion::create([
                'code' => $validatedData['code'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'valid_from' => $validatedData['valid_from'],
                'valid_to' => $validatedData['valid_to'],
                'is_active' => $validatedData['is_active'],
                'created_by' => Auth::guard('bo-api')->id(), // promotion creator who is creating this promotion
            ]);

            if(isset($validatedData['rewards'])) {
                foreach ($validatedData['rewards'] as $rewardData) {
                    $promotion->rewards()->create($rewardData);
                }
            }

            return $promotion;
        });

        return $promotion->load('rewards');
    }
}