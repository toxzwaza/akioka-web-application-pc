<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreLunchOrder extends Model
{
    protected $guarded = [];

    public function lunch(): BelongsTo
    {
        return $this->belongsTo(Lunch::class, 'lunch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
