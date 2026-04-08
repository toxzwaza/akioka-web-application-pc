<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryOperationRecord extends Model
{
    protected $guarded = [];

    public function inventoryOperation(): BelongsTo
    {
        return $this->belongsTo(InventoryOperation::class, 'inventory_operation_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function stockStorage(): BelongsTo
    {
        return $this->belongsTo(StockStorage::class, 'stock_storage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
