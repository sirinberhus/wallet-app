<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Promotion;
use App\Models\Transaction;

class BackofficeAgent extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];

    protected $hidden = [
        'password'
    ];

    //JWT
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {  // for include additional info-claim in the JWT token
        return ['is_admin' => $this->is_admin];
    }

    public function createdPromotions() {
        return $this->hasMany(Promotion::class, 'created_by');
    }

    public function processedTransactions() {
        return $this->hasMany(Transaction::class, 'processed_by');
    }
}
