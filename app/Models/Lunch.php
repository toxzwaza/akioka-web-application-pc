<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lunch extends Model
{
    protected $guarded = [];

    public function lunchOrders(): HasMany
    {
        return $this->hasMany(LunchOrder::class, 'lunch_id');
    }

    public function preLunchOrders(): HasMany
    {
        return $this->hasMany(PreLunchOrder::class, 'lunch_id');
    }
}
