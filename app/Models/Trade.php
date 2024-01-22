<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'result',
        'direction',
        'current_price',
        'result_price',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function setDirectionAttribute($value)
    {
        $this->attributes['direction'] = strtolower($value);
    }
}
