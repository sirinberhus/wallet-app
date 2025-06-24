<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PlayerBalance;
use App\Models\Transaction;
use App\Models\Promotion;

class Player extends Model
{
    use HasFactory, Notifiable; // allows using factories and notification

    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password']; //it wont be visible when you fetch user data 

    //relationships

    public function balance() {
        return $this->hasOne(PlayerBalance::class)   // one to one 
        ->withDefault([
            'balance' => 0
        ]);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class); // one to many
    }

    public function claimedPromotions() {
        return $this->belongsToMany(Promotion::class, 'player_promotions')  //many to many 
        ->withPivot('claimed_at')
        ->withTimestamps();
    }

    
}
