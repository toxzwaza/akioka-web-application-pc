<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $guarded = [];

    public function stockSuppliers(): HasMany
    {
        return $this->hasMany(StockSupplier::class, 'stock_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_id');
    }

    public function getMainSupplierAttribute()
    {
        return $this->stockSuppliers->firstWhere('main_flg', 1)?->supplier;
    }

    public function getMainSupplierNameAttribute()
    {
        return $this->mainSupplier?->name;
    }

    public function getMainSupplierIdAttribute()
    {
        return $this->mainSupplier?->id;
    }

    public function getMainSupplierNoAttribute()
    {
        return $this->mainSupplier?->supplier_no;
    }

    public function orderRequests(): HasMany
    {
        return $this->hasMany(OrderRequest::class, 'stock_id');
    }

    public function stockImages(): HasMany
    {
        return $this->hasMany(StockImage::class, 'stock_id');
    }

    public function documentStocks(): HasMany
    {
        return $this->hasMany(DocumentStock::class, 'stock_id');
    }

    public function stockSupplierPrices(): HasMany
    {
        return $this->hasMany(StockSupplierPrice::class, 'stock_id');
    }

    public function objectRequests(): HasMany
    {
        return $this->hasMany(ObjectRequest::class, 'stock_id');
    }

    public function productAliases(): HasMany
    {
        return $this->hasMany(ProductAlias::class, 'stock_id');
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class, 'stock_id');
    }

    public function stockRequestOrders(): HasMany
    {
        return $this->hasMany(StockRequestOrder::class, 'stock_id');
    }
}
