<?php

namespace App\Http\Controllers;

use App\Models\Stock;
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

    // 棚卸しデータをCSV形式で出力（export_dataと同じ項目構成。格納先未登録の物品も含む）
    public function export_csv()
    {
        $rows = Stock::select(
            'stocks.id as stock_id',
            'stocks.name',
            'stocks.s_name',
            'stocks.img_path',
            'stock_storages.id as stock_storage_id',
            'stock_storages.quantity',
            'stock_storages.storage_address_id'
        )
            ->leftJoin('stock_storages', 'stock_storages.stock_id', 'stocks.id')
            ->where('stocks.del_flg', 0)
            // 格納先登録済みをアドレスID順で先に、未登録の物品を末尾にまとめる
            ->orderByRaw('stock_storages.storage_address_id IS NULL asc')
            ->orderBy('stock_storages.storage_address_id', 'asc')
            ->orderBy('stocks.id', 'asc')
            ->get();

        $filename = 'stock_count_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM付きUTF-8（Excelで文字化けさせないため）
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['物品ID', '品名', '品番', '画像URL', '格納先在庫ID', '在庫数', '格納先アドレスID']);
            foreach ($rows as $row) {
                $img_path = $row->img_path;
                if ($img_path && strpos($img_path, 'http') === false) {
                    $img_path = 'https://akioka.cloud/' . $img_path;
                }
                fputcsv($out, [
                    $row->stock_id,
                    $row->name,
                    $row->s_name,
                    $img_path,
                    $row->stock_storage_id,
                    $row->quantity,
                    $row->storage_address_id,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
