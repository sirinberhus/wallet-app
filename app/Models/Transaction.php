<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id','type', 'amount', 'reference_id', 'promotion_reward_id', 'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function reward()
    {
        return $this->belongsTo(PromotionReward::class, 'promotion_reward_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(BackofficeAgent::class, 'processed_by');
    }
}
