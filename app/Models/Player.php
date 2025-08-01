<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; //child of Model doesn't need add Eloquent\Model
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\PlayerBalance;
use App\Models\Transaction;
use App\Models\Promotion;

class Player extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable; // allows using factories and notification

    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password']; //it wont be visible when you fetch user data 

    //JWT
    public function getJWTIdentifier() { // unique identifier for the user that will be stored in JWT's sub claim.
        return $this->getKey(); // it returns the model's primary key
    }

    public function getJWTCustomClaims() {
        return [];
    }


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

    public function promotions() {
        return $this->belongsToMany(Promotion::class, 'player_promotions')  //many to many  
        ->withPivot('claimed_at')
        ->withTimestamps();
    }

    
}
