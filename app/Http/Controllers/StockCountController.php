<?php

namespace App\Http\Controllers;

use App\Models\StockStorage;
use App\Models\StorageAddress;
use Illuminate\Http\Request;

class StockCountController extends Controller
{
    //
    public function export_data()
    {
        $stock_storage_data = StockStorage::select('stock_id', 'stocks.name', 'stocks.s_name', 'stocks.img_path', 'stock_storages.id as stock_storage_id', 'quantity', 'storage_address_id')
            ->where('stocks.del_flg', 0)
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->orderBy('storage_address_id', 'asc')
            ->get()
            ->groupBy('storage_address_id')
            ->mapWithKeys(function ($items, $key) {
                return [$key => array_map(function ($item) {
                    if (strpos($item['img_path'], 'http') === false) {
                        $item['img_path'] = 'https://akioka.cloud/' . $item['img_path'];
                    }
                    return $item;
                }, $items->toArray())];
            })
            ->where('stocks.del_flg', 0)
            ->toArray();


        return response()->json($stock_storage_data);
    }

    public function export_storage_address_data(){

        $storage_address_data = StorageAddress::select('uid', 'id')->where('uid', '!=', null)->get();

        return response()->json($storage_address_data);
    }
}
