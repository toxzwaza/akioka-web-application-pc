<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockStorage;
use App\Models\StorageAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockCountController extends Controller
{
    // 棚卸しデータ出力ページ
    public function index()
    {
        return Inertia::render('Stock/StockCount/Index');
    }

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

    // 物品マスタ（手配先・単価付き）をCSV形式で出力（Shift_JIS・Windows Excel向け）
    // 手配先はmain_flg=1を優先し、なければ最初の1件を採用して1物品=1行にする
    public function export_stock_master_csv()
    {
        $stocks = Stock::select(
            'suppliers.name as supplier_name',
            'stocks.name',
            'stocks.s_name',
            'stocks.memo',
            'stocks.price',
            'stocks.solo_unit',
            'stocks.org_unit',
            'stocks.quantity_per_org',
            'stocks.updated_at'
        )
            ->join(DB::raw('(
                SELECT ss1.*
                FROM stock_suppliers ss1
                INNER JOIN (
                    SELECT stock_id,
                           COALESCE(MAX(CASE WHEN main_flg = 1 THEN id END), MIN(id)) as selected_id
                    FROM stock_suppliers
                    GROUP BY stock_id
                ) ss2 ON ss1.id = ss2.selected_id
            ) as prioritized_stock_suppliers'), 'prioritized_stock_suppliers.stock_id', '=', 'stocks.id')
            ->join('suppliers', 'suppliers.id', '=', 'prioritized_stock_suppliers.supplier_id')
            ->where('stocks.del_flg', 0)
            ->whereNotNull('stocks.price')
            ->orderBy('suppliers.name', 'asc')
            ->orderBy('stocks.name', 'asc')
            ->get();

        $filename = 'stocks_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($stocks) {
            $out = fopen('php://output', 'w');

            // Windows版Excelでの文字化けを防ぐため、CSVはSJIS-winで出力する
            $toSjis = function (array $row): array {
                return array_map(function ($value) {
                    if ($value === null) {
                        return '';
                    }
                    return mb_convert_encoding((string) $value, 'SJIS-win', 'UTF-8');
                }, $row);
            };

            fputcsv($out, $toSjis(['発注先名', '品名', '品番', 'メモ', '価格', '発注単位', '在庫単位', '換算値', '更新日時']));
            foreach ($stocks as $stock) {
                fputcsv($out, $toSjis([
                    $stock->supplier_name,
                    $stock->name,
                    $stock->s_name,
                    $stock->memo,
                    $stock->price,
                    $stock->solo_unit,
                    $stock->org_unit,
                    $stock->quantity_per_org,
                    $stock->updated_at,
                ]));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=Shift_JIS']);
    }
}
