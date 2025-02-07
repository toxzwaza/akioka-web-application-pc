<?php

namespace App\Http\Controllers;

use App\Models\InventoryOperationRecord;
use App\Models\StockStorage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RetentionController extends Controller
{
    //
    public function index()
    {
        // 直近一年間の出庫データを取得
        $one_year_shipment_records_array = InventoryOperationRecord::select('stock_id')->where('inventory_operation_id', 2)
            ->where('created_at', '>=', now()->subYear())
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('stock_id')
            ->pluck('stock_id')
            ->toArray();


        $stocks = StockStorage::select('stocks.id as stock_id', 'stocks.name', 'stocks.s_name', 'stocks.img_path', 'storage_addresses.address', 'locations.id as location_id', 'locations.name as location_name')
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')
            ->join('locations', 'locations.id', 'storage_addresses.location_id')
            ->whereNotIn('stocks.id', $one_year_shipment_records_array)
            ->get();

        // $滞留品のリストを表示
        return Inertia::render('Stock/Retention/Index', ['stocks' => $stocks]);
    }
}
