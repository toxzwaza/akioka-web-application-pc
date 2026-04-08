<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockSupplierPrice extends Model
{
    protected $guarded = [];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function stockSupplier(): BelongsTo
    {
        return $this->belongsTo(StockSupplier::class, 'stock_supplier_id');
    }
}
