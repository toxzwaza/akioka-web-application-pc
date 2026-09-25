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

    // 棚卸しデータをCSV形式で出力（export_dataと同じデータに倉庫・アドレス名を付与）
    public function export_csv()
    {
        $rows = StockStorage::select(
            'locations.name as location_name',
            'storage_addresses.address',
            'stock_storages.storage_address_id',
            'stock_storages.id as stock_storage_id',
            'stocks.id as stock_id',
            'stocks.name',
            'stocks.s_name',
            'stocks.img_path',
            'stock_storages.quantity'
        )
            ->join('stocks', 'stocks.id', 'stock_storages.stock_id')
            ->join('storage_addresses', 'storage_addresses.id', 'stock_storages.storage_address_id')
            ->join('locations', 'locations.id', 'storage_addresses.location_id')
            ->where('stocks.del_flg', 0)
            ->orderBy('locations.name', 'asc')
            ->orderBy('storage_addresses.address', 'asc')
            ->orderBy('stocks.name', 'asc')
            ->get();

        $filename = 'stock_count_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM付きUTF-8（Excelで文字化けさせないため）
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['倉庫', 'アドレス', '品名', '品番', '在庫数', '物品ID', '格納先在庫ID', '格納先アドレスID', '画像URL']);
            foreach ($rows as $row) {
                $img_path = $row->img_path;
                if ($img_path && strpos($img_path, 'http') === false) {
                    $img_path = 'https://akioka.cloud/' . $img_path;
                }
                fputcsv($out, [
                    $row->location_name,
                    $row->address,
                    $row->name,
                    $row->s_name,
                    $row->quantity,
                    $row->stock_id,
                    $row->stock_storage_id,
                    $row->storage_address_id,
                    $img_path,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
