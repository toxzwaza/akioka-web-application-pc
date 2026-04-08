<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageAddress extends Model
{
    protected $guarded = [];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function stockStorages(): HasMany
    {
        return $this->hasMany(StockStorage::class, 'storage_address_id');
    }
}
