<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class BoUserController extends Controller
{
    public function getUsers(Request $request)
    {

        $perPage = $request->get('per_page', 15);
        $users = Player::paginate($perPage);

        return response()->json($users);
    }

    public function getTransactions(Player $player)
    {

        $player = Player::with(['transactions.reward'])->find($player->id);

        if (!$player) {
            return response()->json([
                'error' => 'Player not found'
            ], 404);
        }

        return response()->json([
            'player_id' => $player->id,
            'username' => $player->username,
            'transactions' => $player->transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'reference_id' => $transaction->reference_id,
                    'reward_type' => $transaction->reward?->type,
                    'currency' => $transaction->reward?->currency,
                    'created_at' => $transaction->created_at->toDateTimeString(),
                ];
            })
        ]);
    }
}
