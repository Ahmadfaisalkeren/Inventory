<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'inventory_id',
        'qty',
        'price',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }

}
