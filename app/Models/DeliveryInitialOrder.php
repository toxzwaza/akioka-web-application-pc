<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryInitialOrder extends Model
{
    protected $table = 'delivery_initial_order';

    protected $guarded = [];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    public function initialOrder(): BelongsTo
    {
        return $this->belongsTo(InitialOrder::class, 'initial_order_id');
    }
}
