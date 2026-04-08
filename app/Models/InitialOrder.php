<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function orderRequest(): BelongsTo
    {
        return $this->belongsTo(OrderRequest::class, 'order_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'order_user_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function stockProcess(): BelongsTo
    {
        return $this->belongsTo(StockProcess::class, 'stock_process_id');
    }

    public function faxParameter(): BelongsTo
    {
        return $this->belongsTo(FaxParameter::class, 'fax_parameter_id');
    }

    public function splitOrderQuantities(): HasMany
    {
        return $this->hasMany(SplitOrderQuantity::class, 'initial_order_id');
    }
}
