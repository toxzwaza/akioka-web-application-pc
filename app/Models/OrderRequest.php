<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderRequest extends Model
{
    protected $guarded = [];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function orderRequestApprovals(): HasMany
    {
        return $this->hasMany(OrderRequestApproval::class, 'order_request_id');
    }

    public function requestUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'request_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stockProcess(): BelongsTo
    {
        return $this->belongsTo(StockProcess::class, 'stock_process_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
