<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'inventory_id',
        'qty',
        'price'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchase_id', 'id');
    }
}
