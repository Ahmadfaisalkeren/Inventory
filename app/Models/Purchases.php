<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'total_price',
        'user_id',
    ];

    public function name()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id', 'id');
    }
}
