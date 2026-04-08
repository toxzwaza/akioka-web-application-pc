<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryOperation extends Model
{
    protected $guarded = [];

    public function records(): HasMany
    {
        return $this->hasMany(InventoryOperationRecord::class, 'inventory_operation_id');
    }
}
