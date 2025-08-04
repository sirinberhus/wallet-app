<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id','type', 'amount', 'currency'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
