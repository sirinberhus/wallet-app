<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Promotion;
use App\Models\Transaction;

class BackofficeAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email' 'password', 'is_admin'
    ];

    protected $hidden = [
        'password'
    ];

    public function createdPromotions() {
        return $this->hasMany(Promotion::class, 'created_by');
    }

    public function processedTransactions() {
        return $this->hasMany(Transaction::class, 'processed_by');
    }
}
