<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function stockSuppliers()
    {
        return $this->hasMany(StockSupplier::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    // メインの取引先を取得
    public function getMainSupplierAttribute()
    {
        $suppliers = $this->stockSuppliers;
        
        if ($suppliers->count() === 1) {
            return $suppliers->first();
        }
        
        // 複数ある場合はmain_flgが1のものを取得
        $mainSupplier = $suppliers->where('main_flg', 1)->first();
        
        // main_flgが1のものがない場合は最初のものを返す
        return $mainSupplier ?: $suppliers->first();
    }

    // メインの取引先名を取得
    public function getMainSupplierNameAttribute()
    {
        $mainSupplier = $this->main_supplier;
        return $mainSupplier ? $mainSupplier->supplier->name : null;
    }

    // メインの取引先IDを取得
    public function getMainSupplierIdAttribute()
    {
        $mainSupplier = $this->main_supplier;
        return $mainSupplier ? $mainSupplier->supplier->id : null;
    }

    // メインの取引先番号を取得
    public function getMainSupplierNoAttribute()
    {
        $mainSupplier = $this->main_supplier;
        return $mainSupplier ? $mainSupplier->supplier->supplier_no : null;
    }
}
