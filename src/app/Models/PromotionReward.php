<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\Transaction;

class PromotionReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'amount', 'currency'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function promotion() {
        return $this->belongsTo(Promotion::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
