<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockStorage extends Model
{
    protected $guarded = [];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function storageAddress(): BelongsTo
    {
        return $this->belongsTo(StorageAddress::class, 'storage_address_id');
    }
}
