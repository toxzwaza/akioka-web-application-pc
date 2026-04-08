<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InitialOrder extends Model
{
    protected $guarded = [];

    /**
     * 納品書（delivery_initial_order 中間テーブル経由）
     */
    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(Delivery::class, 'delivery_initial_order', 'initial_order_id', 'delivery_id')
            ->withTimestamps();
    }
}
