<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PromotionReward;
use App\Models\Player;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'is_active', 'valid_from', 'valid_to', 'created_by'
    ];

    protected $dates = ['valid_from', 'valid_to'];

    public function rewards() {
        return $this->hasMany(PromotionReward::class);
    }

    public function creator() {
        return $this->belongsToMany(Player::class, 'player_promotions')
        ->withPivot('claimed_at')
        ->withTimestamps();
    }
}
