<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Player;

class PlayerBalance extends Model
{
    use HasFactory;

    protected $primaryKey = 'player_id';
    public $incrementing = false; // For avoid auto-increment

    protected $fillable = ['balance'];

    public function player() {
        return $this->belongsTo(Player::class);
    }
}
