<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'user_id',
        'total_price'
    ];

    public function name()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetails::class);
    }

}
